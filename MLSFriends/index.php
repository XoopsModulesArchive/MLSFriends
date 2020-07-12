<?php
// $Id: index.php,v 0.1 2006/01/01 - MyLatinSoulmate                         //
//  ------------------------------------------------------------------------ //
//                    MyLatinSoulmate Friends Module                         //
//                  Copyright (c) 2006 MyLatinSoulmate                       //
//                   <http://www.mylatinsoulmate.com/>                       //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //


// Init
include '../../mainfile.php';
$myts =& MyTextSanitizer::getInstance();
$uid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;

// Check HTTP_REQUEST Options
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : "index";
$friendid = !empty($_GET['friendid']) ? intval($_GET['friendid']) : 0;

// Only Logged on members can have Favorites and Admirers
if (!$xoopsUser) {redirect_header('/index.php',5,_PROFILE_LOGON);exit();} else { 

switch ($op) {
    default:
	case "index": // Basically open the Submenus and show your Favorites *Can surely be changed into something more usefull
		$url = "myfavorites.php";
		header("location: ".$url);
    
	case "del": // Delete Favorite from list
		$sql = "SELECT Count(*) FROM ".$xoopsDB->prefix("MLSfriends")." WHERE (uid=$uid AND fuid=$friendid)"; 
		$res = $xoopsDB->query($sql); 
		list($ismyfriend) = $xoopsDB->fetchRow($res); // Check if this member is in your list
		if ($ismyfriend == 1) {
			$sqlstr = "DELETE FROM ".$xoopsDB->prefix("MLSfriends")." WHERE (uid=$uid AND fuid=$friendid)";
			$res = mysql_query($sqlstr);
			redirect_header('myfavorites.php',5,_MLS_REMOVED);
			exit();
		} else { 
			redirect_header('myfavorites.php',5,_MLS_NOT_REMOVED);
			exit();
		}
	
	case "deladm": // Delete Admirer from list
		$sql = "SELECT Count(*) FROM ".$xoopsDB->prefix("MLSfriends")." WHERE (uid=$friendid AND fuid=$uid)";
		$res = $xoopsDB->query($sql); 
		list($ismyfriend) = $xoopsDB->fetchRow($res); // Check if this member has you in his list
		if ($ismyfriend == 1) {
			$sqlstr="DELETE FROM ".$xoopsDB->prefix("MLSfriends")." WHERE (fuid=$uid AND uid=$friendid)";
			$res = mysql_query($sqlstr);
			redirect_header('myfavorites.php?type=admirers',5,_MLS_ADMIRER_REMOVED);
			exit();
		} else { 
			redirect_header('myfavorites.php?type=admirers',5,_MLS_ADMIRER_NOT);
			exit();
		}
	
	case "add": // Add someone to your Favorites list
    	$member_handler =& xoops_gethandler('member');
		$thisUser =& $member_handler->getUser($friendid);
    	if (!is_object($thisUser) || !$thisUser->isActive()) { // Only add valid profiles as your favorites
        	redirect_header("myfavorites.php",3,_PROFILE_MA_SELECTNG);
        	exit();
    	}

		$sql = "SELECT Count(*) FROM ".$xoopsDB->prefix("MLSfriends")." WHERE (uid=$uid AND fuid=$friendid)";
		$res = $xoopsDB->query($sql);
		list($ismyfriend) = $xoopsDB->fetchRow($res);
		if ($ismyfriend == 0) { // This member isn't your friend yet.
			$sqlstr ="INSERT INTO ".$xoopsDB->prefix("MLSfriends")." (uid, fuid) VALUES ($uid, $friendid)";
			$req1=mysql_query($sqlstr);
			
			// Only send email notif if notification method is mail, but let them know they have an admirer
			if ($thisUser->notify_method() == 2) {
				$xoopsMailer =& getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setToEmails($thisUser->email());
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']); // Keep Members email accounts disclosed
				$xoopsMailer->setTemplateDir("language/".$xoopsConfig['language']."/mail_template");
				$xoopsMailer->setTemplate('favorite.tpl');
				$xoopsMailer->assign('X_SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('X_SITEURL', XOOPS_URL."/");
				$xoopsMailer->assign('X_ADMINMAIL', $xoopsConfig['adminmail']);
				$xoopsMailer->assign('X_UNAME', $thisUser->uname());
				$xoopsMailer->assign('X_FROMUNAME', $xoopsUser->uname());
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_MLS_FAV_EMAILSUBJ, $xoopsConfig['sitename'],$xoopsUser->uname()));
				$xoopsMailer->send();
			}
			
			redirect_header('myfavorites.php',5,_MLS_FAV_ADDED);
			exit();
		} else { 
			redirect_header('myfavorites.php',5,_MLS_FAV_ALREADY);
			exit();
		}
	
	case "interest": // Send someone an "I am interested in you" PM
		$member_handler =& xoops_gethandler('member'); // Only to active members 
    	$thisUser =& $member_handler->getUser($friendid);
		if (!is_object($thisUser) || !$thisUser->isActive()) {
        	redirect_header("/index.php",3,_PROFILE_MA_SELECTNG);
        	exit();
    	}
		
		$image = "MLS_interest.gif";  // Show a nice picture in the PM inbox
		$subject = $xoopsUser->getVar('uname')._MLS_INTERESTED;
		$time = time();
		$text = sprintf(_MLS_INTERESTED_BODY, $xoopsUser->uname());
		$sql = "INSERT INTO ".$xoopsDB->prefix("priv_msgs")." (msg_image, subject, from_userid, to_userid, msg_time, msg_text, read_msg, from_delete, from_save, to_delete, to_save) VALUES ('$image', '$subject', $uid, $friendid, $time, '$text', 0,0,0,0,0)";
		$req1=mysql_query($sql) or die($xoopsDB->error()); // Quick & Dirty way to send a PM
		
		
		// Only send email notif if notification method is mail
		if ($thisUser->notify_method() == 2) {
			$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setToEmails($thisUser->email());
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setTemplateDir("language/".$xoopsConfig['language']."/mail_template");
			$xoopsMailer->setTemplate('interest.tpl');
			$xoopsMailer->assign('X_SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('X_SITEURL', XOOPS_URL."/");
			$xoopsMailer->assign('X_ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->assign('X_UNAME', $thisUser->uname());
			$xoopsMailer->assign('X_FROMUNAME', $xoopsUser->uname());
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_MLS_INT_EMAILSUBJ, $xoopsUser->uname()));
			$xoopsMailer->send();
		}
		redirect_header('/modules/profile/userinfo.php?uid='.$friendid,5,_MLS_INTEREST_SENT);
		exit();
} // switch
} //else
?>