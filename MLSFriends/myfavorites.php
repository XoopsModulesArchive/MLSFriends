<?php
// $Id: myfavorites.php,v 0.1 2006/01/01 - MyLatinSoulmate                   //
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

// Only Logged on members can have Favorites and Admirers
if (!$xoopsUser) {redirect_header('/index.php',5,_PROFILE_LOGON);exit();} else {

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "normal";
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;


$xoopsOption['template_main'] = "MLSfriends_list.html";


include_once "../../header.php";
$uid = $xoopsUser->getVar('uid');

if ($type == "admirers") {
	$sqlstr = "SELECT uid FROM ".$xoopsDB->prefix("MLSfriends")." WHERE fuid='".$uid."' ORDER BY ref DESC ";
	$title = _MLS_ADMIRER_TITLE;
	$message = _MLS_ADMIRER_NONE;
} else {
	$sqlstr = "SELECT fuid FROM ".$xoopsDB->prefix("MLSfriends")." WHERE uid='".$uid."' ORDER BY ref DESC ";
	$title = _MLS_FAVORITE_TITLE;
	$message = _MLS_FAVORITE_NONE;
}

$result = $xoopsDB->query($sqlstr) or die($xoopsDB->error() );
$z = 0;
while ( $item = $xoopsDB->fetchArray ( $result ) ) { // Fill array with results *this can be done more elegant
    $interests[$z] = $item;
	$z++;
}

$i=$start;  // So we can also see where we have been on the results page
$total_users=$z;
$limit = 5; // How many profiles to show per page
$max = $i+$limit > $z ? $z : $i+$limit; // Don't show more profiles then the limit set...or end of list

while ($i < $max) {
	if ($type == "admirers")
		$wuid = $interests[$i]['uid']; // You are the one beeing Admired
	else
		$wuid = $interests[$i]['fuid']; // You are the one Amiring
	$thisUser =& $member_handler->getUser($wuid);
	if (is_object($thisUser) && $thisUser->isActive()) { // Allowed users only
		$userarray["output"][] = "<a href='/userinfo.php?uid=".$thisUser->getVar('uid')."'>".$thisUser->getVar('uname')."</a>";
		if($thisUser->getVar('user_avatar') && "blank.gif" != $thisUser->getVar('user_avatar')){
			$userarray["output"][] = "<a href='/userinfo.php?uid=".$thisUser->getVar('uid')."'>
			<img src='".XOOPS_UPLOAD_URL."/".$thisUser->getVar('user_avatar')."' alt='".$thisUser->getVar('uname')."' /></a>";
		} else {
			$userarray["output"][] = "<img src='".XOOPS_UPLOAD_URL."/blank.gif' alt='".$thisUser->getVar('uname')."' />";
		}
		$userarray["output"][] = $thisUser->getVar('user_from');
		if ($thisUser->isOnline()) {
			$active = "Online!";
		} else {
			$last_log = $thisUser->getVar('last_login');
			if (time() - $last_log < (24*60*60)) { // 24 Hours
				$active = _MLS_24HOURS;
			} elseif (time() - $last_log < (2*24*60*60)) { // 48 Hours
				$active = _MLS_48HOURS;
			} elseif (time() - $last_log < (7*24*60*60)) { // 1 Week
				$active =  _MLS_1WEEK;
			} elseif (time() - $last_log < (13*7*24*60*60/3)) { // 1 Month
				$active =  _MLS_1MONTH;
			} elseif (time() - $last_log < (13*7*24*60*60)) { // 3 Months
				$active =  _MLS_3MONTHS;
			} else {
				$active =  _MLS_LONGER;
			}
		}
		$userarray["output"][] = $active;
		$userarray["output"][] = $thisUser->getVar('bio');
		if ($type == "admirers") {
			$userarray["output"][] = "index.php?op=deladm&friendid=".$thisUser->getVar('uid');
		} else {
			$userarray["output"][] = "index.php?op=del&friendid=".$thisUser->getVar('uid');
		}
		$userarray["output"][] = "index.php?op=interest&friendid=".$thisUser->getVar('uid');
		$userarray["output"][] = "/userinfo.php?uid=".$thisUser->getVar('uid');
		$xoopsTpl->append('users', $userarray);
		unset($userarray);
	} else { $total_users--;}	
	$i++;
}

$xoopsTpl->assign('location', _MLS_LOCATION);
$xoopsTpl->assign('active', _MLS_ACTIVE);
$xoopsTpl->assign('page', _MLS_PAGE);
if ($type == "admirers") {
	$xoopsTpl->assign('name', _MLS_ADMIRER_NAME);
	$xoopsTpl->assign('button1', _MLS_DEL_ADMIRER); 
} else {
	$xoopsTpl->assign('name', _MLS_FAVORITE_NAME);
	$xoopsTpl->assign('button1', _MLS_DEL_FAVORITE);
}
$xoopsTpl->assign('button2', _MLS_INTEREST);
$xoopsTpl->assign('button3', _MLS_VIEW_PROFILE);
$xoopsTpl->assign('title', $title);
$xoopsTpl->assign('message',$message);
	
if ($total_users > $limit) { // More pages
	$search_url[] = "view=".$view;
	$search_url[] = "type=".$type;
	if (isset($search_url)) {
            $args = implode("&amp;", $search_url);
    }
	if (isset($search_url)) {
            $args = implode("&amp;", $search_url);
    }
	if (isset($search_url)) {
            $args = implode("&amp;", $search_url);
    }
	include_once XOOPS_ROOT_PATH."/class/pagenav.php";
    $nav = new XoopsPageNav($total_users, $limit, $start, "start", $args);
    $xoopsTpl->assign('nav', $nav->renderNav(5));
}
   

include XOOPS_ROOT_PATH."/footer.php";
}
?>