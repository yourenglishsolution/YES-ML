<?php

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

session_name("docebo_session");
session_start();

define('IN_DOCEBO', true);
define('INSTALL_ENV', 'upgrade');

define("_deeppath_", "../");
require(dirname(__FILE__).'/../base.php');
define('_installer_', _base_.'/install');
define('_upgrader_', _base_.'/upgrade');

include(_installer_.'/lib/lib.php');
include(_installer_.'/lib/lib.lang.php');
include(_installer_.'/lib/lib.step.php');
include(_installer_.'/lib/lib.pagewriter.php');
include(_installer_.'/lib/lib.template.php');

PageWriter::init();

include(_base_.'/lib/lib.utils.php');
include(_base_.'/lib/lib.yuilib.php');
include(_base_.'/lib/lib.form.php');
include(_upgrader_.'/config.php');

$GLOBALS['page']->setZone('page_head');
YuiLib::load();
$GLOBALS['page']->setZone('main');

?>