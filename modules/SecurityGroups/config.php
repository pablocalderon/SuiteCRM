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

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('XTemplate/xtpl.php');

require_once('modules/Administration/Administration.php');
require_once('modules/SecurityGroups/Forms.php');
require_once('modules/SecurityGroups/SecurityGroup.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;


if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");
//Fix Notice error
$mod_id = "";
$mod_name = "";
if(isset($mod_strings['LBL_MODULE_ID'])) {
	$mod_id = $mod_strings['LBL_MODULE_ID'];
}
if(isset($mod_strings['LBL_MODULE_NAME'])) {
	$mod_name = $mod_strings['LBL_MODULE_NAME'];
}
echo "\n<p>\n";
echo get_module_title($mod_id, $mod_name.": ".$mod_strings['LBL_CONFIGURE_SETTINGS'], false);
echo "\n</p>\n";
global $theme;
global $currentModule;
$theme_path = "themes/".$theme."/";
$image_path = $theme_path."images/";


$focus = new Administration();
$focus->retrieveSettings(); //retrieve all admin settings.
$GLOBALS['log']->info("SecuritySuite Configure Settings view");

$xtpl=new XTemplate ('modules/SecurityGroups/config.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("RETURN_MODULE", "Administration");
$xtpl->assign("RETURN_ACTION", "index");

$xtpl->assign("MODULE", $currentModule);
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("HEADER", get_module_title("SecurityGroups", "{MOD.LBL_CONFIGURE_SETTINGS}", true));


// securitysuite_additive
$securitysuite_additive = '';
if(isset($sugar_config['securitysuite_additive']) && $sugar_config['securitysuite_additive'] == true) {
	$securitysuite_additive = 'CHECKED';
} 
$xtpl->assign('securitysuite_additive', $securitysuite_additive);

// securitysuite_strict_rights
$securitysuite_strict_rights = '';
if(isset($sugar_config['securitysuite_strict_rights']) && $sugar_config['securitysuite_strict_rights'] == true) {
	$securitysuite_strict_rights = 'CHECKED';
} 
$xtpl->assign('securitysuite_strict_rights', $securitysuite_strict_rights);

// securitysuite_filter_user_list
$securitysuite_filter_user_list = '';
if(isset($sugar_config['securitysuite_filter_user_list']) && $sugar_config['securitysuite_filter_user_list'] == true) {
	$securitysuite_filter_user_list = 'CHECKED';
} 
$xtpl->assign('securitysuite_filter_user_list', $securitysuite_filter_user_list);

// securitysuite_user_role_precedence
$securitysuite_user_role_precedence = '';
if(isset($sugar_config['securitysuite_user_role_precedence']) && $sugar_config['securitysuite_user_role_precedence'] == true) {
	$securitysuite_user_role_precedence = 'CHECKED';
} 
$xtpl->assign('securitysuite_user_role_precedence', $securitysuite_user_role_precedence);
// securitysuite_user_popup
$securitysuite_user_popup = '';
if(isset($sugar_config['securitysuite_user_popup']) && $sugar_config['securitysuite_user_popup'] == true) {
	$securitysuite_user_popup = 'CHECKED';
} 
$xtpl->assign('securitysuite_user_popup', $securitysuite_user_popup);
// securitysuite_popup_select
$securitysuite_popup_select = '';
if(isset($sugar_config['securitysuite_popup_select']) && $sugar_config['securitysuite_popup_select'] == true) {
	$securitysuite_popup_select = 'CHECKED';
} 
$xtpl->assign('securitysuite_popup_select', $securitysuite_popup_select);
// securitysuite_inherit_creator
$securitysuite_inherit_creator = '';
if(isset($sugar_config['securitysuite_inherit_creator']) && $sugar_config['securitysuite_inherit_creator'] == true) {
	$securitysuite_inherit_creator = 'CHECKED';
} 
$xtpl->assign('securitysuite_inherit_creator', $securitysuite_inherit_creator);
// securitysuite_inherit_parent
$securitysuite_inherit_parent = '';
if(isset($sugar_config['securitysuite_inherit_parent']) && $sugar_config['securitysuite_inherit_parent'] == true) {
	$securitysuite_inherit_parent = 'CHECKED';
} 
$xtpl->assign('securitysuite_inherit_parent', $securitysuite_inherit_parent);
// securitysuite_inherit_assigned
$securitysuite_inherit_assigned = '';
if(isset($sugar_config['securitysuite_inherit_assigned']) && $sugar_config['securitysuite_inherit_assigned'] == true) {
	$securitysuite_inherit_assigned = 'CHECKED';
} 
$xtpl->assign('securitysuite_inherit_assigned', $securitysuite_inherit_assigned);


// securitysuite_inbound_email
$securitysuite_inbound_email = '';
if(isset($sugar_config['securitysuite_inbound_email']) && $sugar_config['securitysuite_inbound_email'] == true) {
    $securitysuite_inbound_email = 'CHECKED';
} 
$xtpl->assign('securitysuite_inbound_email', $securitysuite_inbound_email);


//default security groups
$groupFocus = new SecurityGroup();
$defaultGroups = $groupFocus->retrieveDefaultGroups();
$defaultGroup_string = "";
foreach($defaultGroups as $default_id => $defaultGroup) {
	$defaultGroup_string .= "
	<tr>
	<td class='dataLabel' width='30%'>
		".$mod_strings['LBL_GROUP']." ".$defaultGroup['group']."
	</td>
	<td class='dataField' width='30%'>
		".$mod_strings['LBL_MODULE']." ".$app_list_strings['moduleList'][$defaultGroup['module']]."
	</td>
	<td class='dataLabel' width='40%'>
		<input type='submit' tabindex='1' class='button' onclick=\"this.form.remove_default_id.value='".$default_id."'; this.form.action.value='SaveConfig'; this.form.return_module.value='SecurityGroups'; this.form.return_action.value='config';\" value='".$mod_strings['LBL_REMOVE_BUTTON_LABEL']."'/>
	</td>
	</tr>";
}
$xtpl->assign("DEFAULT_GROUPS", $defaultGroup_string);

$groups = $groupFocus->get_list("name");
$options = array(""=>"");
foreach($groups['list'] as $group) {
	$options[$group->id] = $group->name;
}
$xtpl->assign("SECURITY_GROUP_OPTIONS", get_select_options_with_id($options, ""));

//$moduleList = $app_list_strings['moduleList'];

//require_once('modules/Studio/DropDowns/DropDownHelper.php');
//$dh = new DropDownHelper();
//$dh->getDropDownModules();
//$moduleList = array_keys($dh->modules);
$security_modules = $groupFocus->getSecurityModules();

$security_modules["All"] = $mod_strings["LBL_ALL_MODULES"];//rost fix
ksort($security_modules);
$xtpl->assign("MODULE_OPTIONS", get_select_options_with_id($security_modules, "All"));


$xtpl->parse("main");

$xtpl->out("main");
?>
