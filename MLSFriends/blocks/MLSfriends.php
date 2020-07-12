<?php
// $Id: MLSfriends.php,v 0.1 2006/01/01 - MyLatinSoulmate                         //
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

function myadmirers_show() {
	global $xoopsConfig, $xoopsDB, $xoopsUser;
	//include '/modules/profile/include/functions.php';
	$block = array();
	
	$uid = $xoopsUser->getVar('uid');
	$interests = array();
	
	$sqlstr="SELECT ref, uid FROM ".$xoopsDB->prefix("MLSfriends")." WHERE fuid='".$uid."' ORDER BY ref DESC ";
	$result = $xoopsDB->query($sqlstr) or die($xoopsDB->error() );
	$z = 0;
	
	while ( $item = $xoopsDB->fetchArray ( $result ) ) {
    	$interests[$z] = $item;
		$z++;
	}
	
	if ($z > 2) {
		$zz = 2;
		$block[0]['link'] = "<a href='/modules/profile/myfavorites.php?type=admirers'>More &#8250;&#8250;</a>";	
	} else {$zz = $z;}
	$i=0; $n =0;
	$member_handler =& xoops_gethandler('member');
	$profile_handler =& xoops_gethandler('profile');
	$fields =& $profile_handler->loadFields();
	$tmonth = date('n');
   	$tday = date('j');
   	$tyear = date('Y');
   	
	while ($i <= $zz) {
			$fuid = $interests[$i]['uid'];
			$thisUser =& $member_handler->getUser($fuid);
			if (is_object($thisUser) && $thisUser->isActive()) {
				$block[$n]['fuid'] = $interests[$i]['uid'];
				if($thisUser->getVar('user_avatar') && "blank.gif" != $thisUser->getVar('user_avatar')){
					$block[$n]['avatar'] = "<a href='/userinfo.php?uid=".$thisUser->getVar('uid')."'>
					<img src='".XOOPS_UPLOAD_URL."/".$thisUser->getVar('user_avatar')."' alt='".$thisUser->getVar('uname')."' /></a>";
				} else {
					$block[$n]['avatar'] = "<img src='".XOOPS_UPLOAD_URL."/blank.gif' alt='".$thisUser->getVar('uname')."' />";
				}
				$block[$n]['realname'] = "<a href='/userinfo.php?uid=".$thisUser->getVar('uid')."'>".$thisUser->getVar('uname')."</a>";
				$block[$n]['location'] = $thisUser->getVar('user_from');
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
				$block[$n]['active'] = $active;
				$block[$n]['delete'] = "/modules/MLSFriends/index.php?op=deladm&friendid=".$fuid;
				$n++;
			} elseif ($z > $zz) {$zz++;}
			$i++;
	}
	$block[99]['delete'] = _MLS_DEL_ADMIRER;
	$block[99]['location'] = _MLS_LOCATION;
	$block[99]['active'] = _MLS_ACTIVE;
	$block[99]['link'] = "<a href='http://www.mylatinsoulmate.com' target='_blank'><br />MyLatinSoulmate Friends version 0.1 </a>";
	$block[99]['message'] = _MLS_BLOCK_MSG;
	return $block;
	

}
?>