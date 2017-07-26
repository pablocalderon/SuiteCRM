{*
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
*}

<form name="ConfigureSettings" enctype='multipart/form-data' method="POST" action="index.php"
      onSubmit="return (add_checks(document.ConfigureSettings) && check_form('ConfigureSettings'));">
    <input type='hidden' name='action' value='SaveConfig'/>
    <input type='hidden' name='module' value='Configurator'/>
    <span class='error'>{$error.main}</span>
    <table width="100%" cellpadding="0" cellspacing="1" border="0" class="actionsContainer">
        <tr>

            <td>
                <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"
                       class="button primary" id="ConfigureSettings_save_button" type="submit" name="save"
                       value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
                &nbsp;<input title="{$MOD.LBL_SAVE_BUTTON_TITLE}" id="ConfigureSettings_restore_button" class="button"
                             type="submit" name="restore" value="  {$MOD.LBL_RESTORE_BUTTON_LABEL}  ">
                &nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}" id="ConfigureSettings_cancel_button"
                             onclick="document.location.href='index.php?module=Administration&action=index'"
                             class="button" type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  "></td>
        </tr>
    </table>


    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
        <tr>
            <th align="left" scope="row" colspan="4"><h4>{$MOD.DEFAULT_SYSTEM_SETTINGS}</h4></th>
        </tr>


        {*
        Favicon by module:
        *}

        <tr>
            <td scope="row">{$MOD.LBL_MODULE_FAVICON} &nbsp;{sugar_help text=$MOD.LBL_MODULE_FAVICON_HELP} </td>
            {if !empty($config.default_module_favicon)}
                {assign var='default_module_favicon' value='CHECKED'}
            {else}
                {assign var='default_module_favicon' value=''}
            {/if}
            <td>
                <input type='hidden' name='default_module_favicon' value='false'>
                <input name='default_module_favicon' type="checkbox" value="true" {$default_module_favicon}>
            </td>
        </tr>

        {*
        Company logo:
        *}
        <tr>
            <td scope="row" width='12%' nowrap>
                {$MOD.CURRENT_LOGO}&nbsp;{sugar_help text=$MOD.CURRENT_LOGO_HELP}
            </td>
            <td width='35%'>
                <img id="company_logo_image" src='{$company_logo}' alt=$mod_strings.LBL_LOGO>
            </td>
        </tr>
        <tr>
            <td scope="row" width='12%' nowrap>
                {$MOD.NEW_LOGO}&nbsp;{sugar_help text=$MOD.NEW_LOGO_HELP_NO_SPACE}
            </td>

        </tr>

        {*
        Custom Favicon Code:
        *}
        <tr>
            <td scope="row" width='12%' nowrap>
                {$MOD.CURRENT_FAVICON}&nbsp;{sugar_help text=$MOD.CURRENT_FAVICON_HELP}
            </td>
            <td width='35%'>
                <img id="favicon_image" src='{$favicon}'>
            </td>
        </tr>
        <tr>
            <td scope="row" width='12%' nowrap>
                {$MOD.NEW_FAVICON}&nbsp;{sugar_help text=$MOD.NEW_NEW_FAVICON_HELP_NO_SPAC}
            </td>
            <td width='35%'>
                <div id="container_upload_1"></div>
                <input type='text' id='favicon' name='favicon' style="display:none">
            </td>
        </tr>

    </table>


    <div style="padding-top: 2px;">
        <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" class="button primary" type="submit" name="save"
               value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="button primary"/>
        &nbsp;<input title="{$MOD.LBL_SAVE_BUTTON_TITLE}" class="button" type="submit" name="restore"
                     value="  {$MOD.LBL_RESTORE_BUTTON_LABEL} "/>
        &nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}"
                     onclick="document.location.href='index.php?module=Administration&action=index'" class="button"
                     type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  "/>
    </div>
    {$JAVASCRIPT}

</form>

<div id='upload_panel_1' style="display:none">
    <form id="upload_form" name="upload_form" method="POST" action='index.php' enctype="multipart/form-data">
        <input type="file" id="my_file_company_1" name="file_1" size="20" onchange="icon_upload_check(false)"/>
        {sugar_getimage name="sqsWait" ext=".gif" alt=$mod_strings.LBL_LOADING other_attributes='id="loading_img_favicon" style="display:none" '}
    </form>
</div>
{if $error.company_logo}
    <script type='text/javascript'>
        {literal}$(function () {
          alert('{/literal}{$error.company_logo}{literal}');
        });{/literal}
    </script>
{/if}
{literal}
    <script type='text/javascript'>
      function init_logo() {
        document.getElementById('upload_panel').style.display = "inline";
        document.getElementById('upload_panel').style.position = "absolute";
        YAHOO.util.Dom.setX('upload_panel', YAHOO.util.Dom.getX('container_upload'));
        YAHOO.util.Dom.setY('upload_panel', YAHOO.util.Dom.getY('container_upload') - 5);
      }
      function init_favicon() {
        document.getElementById('upload_panel_1').style.display = "inline";
        document.getElementById('upload_panel_1').style.position = "absolute";
        YAHOO.util.Dom.setX('upload_panel_1', YAHOO.util.Dom.getX('container_upload_1'));
        YAHOO.util.Dom.setY('upload_panel_1', YAHOO.util.Dom.getY('container_upload_1') - 5);
      }
      YAHOO.util.Event.onDOMReady(function () {
        init_favicon();
      });
      function toggleDisplay_2(div_string) {
        alert('toggledisplay');
        toggleDisplay(div_string);
        init_logo();
      }
      function uploadCheck(quotes) {
        //AJAX call for checking the file size and comparing with php.ini settings.
        var callback = {
          upload: function (r) {
            eval("var file_type = " + r.responseText);
            var forQuotes = file_type['forQuotes'];
            document.getElementById('loading_img_' + forQuotes).style.display = "none";
            bad_image = SUGAR.language.get('Configurator', (forQuotes == 'quotes') ? 'LBL_ALERT_TYPE_JPEG' : 'LBL_ALERT_TYPE_IMAGE');
            switch (file_type['data']) {
              case 'other':
                alert(bad_image);
                document.getElementById('my_file_' + forQuotes).value = '';
                break;
              case 'size':
                alert(SUGAR.language.get('Configurator', 'LBL_ALERT_SIZE_RATIO'));
                document.getElementById(forQuotes + "_logo").value = file_type['path'];
                document.getElementById(forQuotes + "_logo_image").src = file_type['url'];
                break;
              case 'file_error':
                alert(SUGAR.language.get('Configurator', 'ERR_ALERT_FILE_UPLOAD'));
                document.getElementById('my_file_' + forQuotes).value = '';
                break;
              //File good
              case 'ok':
                document.getElementById(forQuotes + "_logo").value = file_type['path'];
                document.getElementById(forQuotes + "_logo_image").src = file_type['url'];
                break;
              //error in getimagesize because unsupported type
              default:
                alert(bad_image);
                document.getElementById('my_file_' + forQuotes).value = '';
            }
          },
          failure: function (r) {
            alert(SUGAR.language.get('app_strings', 'LBL_AJAX_FAILURE'));
          }
        }
        document.getElementById("company_logo").value = '';
        document.getElementById('loading_img_company').style.display = "inline";
        var file_name = document.getElementById('my_file_company').value;
        postData = '&entryPoint=UploadFileCheck&forQuotes=false';
        YAHOO.util.Connect.setForm(document.getElementById('upload_form'), true, true);
        if (file_name) {
          if (postData.substring(0, 1) == '&') {
            postData = postData.substring(1);
          }
          YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
        }
      }

      function icon_upload_check(quotes) {
        //AJAX call for checking the file size and comparing with php.ini settings.
        var callback = {
          upload: function (r) {
            eval("var file_type = " + r.responseText);
            var forQuotes = file_type['forQuotes'];
            document.getElementById('loading_img_' + forQuotes).style.display = "none";
            bad_image = SUGAR.language.get('Configurator', (forQuotes == 'quotes') ? 'LBL_ALERT_TYPE_JPEG' : 'LBL_ALERT_TYPE_IMAGE');
            switch (file_type['data']) {
              case 'ok':
                alert(bad_image);
                document.getElementById('my_file_' + forQuotes).value = '';
                break;
              case 'size':
                alert(SUGAR.language.get('Configurator', 'LBL_ALERT_SIZE_RATIO'));
                document.getElementById(forQuotes + "_logo").value = file_type['path'];
                document.getElementById(forQuotes + "_logo_image").src = file_type['url'];
                break;
              case 'file_error':
                alert(SUGAR.language.get('Configurator', 'ERR_ALERT_FILE_UPLOAD'));
                document.getElementById('my_file_' + forQuotes).value = '';
                break;
              //File good
              case 'other':
                document.getElementById(forQuotes).value = file_type['path'];
                document.getElementById(forQuotes + "_image").src = file_type['url'];
                break;
              //error in getimagesize because unsupported type
              default:
                alert(bad_image);
                document.getElementById('my_file_' + forQuotes).value = '';
            }
          },
          failure: function (r) {
            alert(SUGAR.language.get('app_strings', 'LBL_AJAX_FAILURE'));
          }
        }
        document.getElementById("favicon").value = '';
        document.getElementById('loading_img_favicon').style.display = "inline";
        var file_name = document.getElementById('my_file_company_1').value;
        postData = '&entryPoint=UploadFileCheck&forQuotes=false';
        YAHOO.util.Connect.setForm(document.getElementById('upload_form'), true, true);
        if (file_name) {
          if (postData.substring(0, 1) == '&') {
            postData = postData.substring(1);
          }
          YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
        }
      }


    </script>
{/literal}
