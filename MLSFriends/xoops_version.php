<?php
// $Id: xoops_version.php,v 0.1 2006/01/01 - MyLatinSoulmate                         //
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
$modversion['name'] 		= _MLS_NAME;
$modversion['version'] 		= "0.1";
$modversion['description'] 	= _MLS_DESC;
$modversion['credits'] 		= "Marc Schot - MyLatinSoulmate <br> http://www.mylatinsoulmate.com <br>Inspired by Directfriends module";
$modversion['author'] 		= "Marc Schot";
$modversion['license'] 		= "GPL see LICENSE";
$modversion['official'] 	= 0;
$modversion['image'] 		= "images/MLS_Friends.gif";
$modversion['hasAdmin'] 	= 0;
$modversion['dirname'] 		= "MLSFriends";
$modversion['namemod'] 		= "MyLatinSoulmate Friends";


// Menu
$modversion['hasMain'] 			= 1;
$modversion['sub'][1]['name'] 	= _MLS_MENU_FAVORITES; // Shows the members who are your Favorites
$modversion['sub'][1]['url'] 	= "myfavorites.php";
$modversion['sub'][2]['name']	= _MLS_MENU_ADMIRERS;  // Shows the members who added you as their Favorites
$modversion['sub'][2]['url'] 	= "myfavorites.php?type=admirers";

// Sql
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] 		= "MLSfriends";

// Templates
$modversion['templates'][1]['file'] 		= "MLSfriends_list.html";
$modversion['templates'][1]['description'] 	= "";

// Blocks
$modversion['blocks'][1]['file'] 		= "MLSfriends.php";
$modversion['blocks'][1]['name'] 		= _MLS_BLOCK_ADMIRERS;
$modversion['blocks'][1]['description'] = _MLS_BLOCK_ADMIRERS_DESC;
$modversion['blocks'][1]['show_func'] 	= "myadmirers_show";
$modversion['blocks'][1]['template'] 	= 'myadmirers.html';
?>
