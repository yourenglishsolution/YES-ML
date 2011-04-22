<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

if(Docebo::user()->isAnonymous()) die("You can't access");

require_once(_base_.'/lib/lib.urlmanager.php');

$um =& UrlManager::getInstance("message");
$um->setStdQuery("modname=message&op=message");


if(!defined('IN_LMS')) define("IN_LMS", TRUE);

define("_PATH_MESSAGE", '/doceboLms/'.Get::sett('pathmessage'));
define("_MESSAGE_VISU_ITEM", Get::sett('visuItem'));
define("_MESSAGE_PL_URL", Get::sett('url'));

require_once(_adm_.'/lib/lib.message.php');


?>