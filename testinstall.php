<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die('testinstall.php is CLI only.');
}

/* DEFINE SOME VARIABLES FOR INSTALLER */
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = 'install.php';
$_SERVER['SERVER_SOFTWARE'] = 'Apache';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';

$_REQUEST['goto'] = 'SilentInstall';
$_REQUEST['cli'] = true;

// this will fix warning in modules/Users/Save.php:295 during installation
$_POST['email_reminder_checked'] = false;


try {
    ob_start();
    require_once 'install.php';
    ob_end_clean();
} catch (\Exception $e) {
    echo "\nINSTALLATION FAILED! file: " . $e->getFile() . ' - line: ' . $e->getLine()
        . "\n" . $e->getMessage()
        . "\n" . str_repeat('-', 120)
        . "\n" . print_r($e->getTrace(), true)
        . "\n" . str_repeat('-', 120)
        . "\n";
}
