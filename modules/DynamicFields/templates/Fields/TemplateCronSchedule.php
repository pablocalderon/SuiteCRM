<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/DynamicFields/templates/Fields/TemplateText.php');
class TemplateCronSchedule extends TemplateText{
    var $type='CronSchedule';

    function __construct(){
        parent::__construct();
    }

    function get_field_def(){
        $def = parent::get_field_def();
        $def['dbType'] = 'varchar';
        return $def;
    }
}
