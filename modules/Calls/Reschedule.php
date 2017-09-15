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

require_once('modules/Calls_Reschedule/Calls_Reschedule.php');
require_once('modules/Calls/Call.php');


$call = new call();
$timedate = new TimeDate();


$id = $_POST['call_id'];
$date = $_POST['date'];
$reason = $_POST['reason'];
$hour = $_POST['date_start_hours'];
$minutes = $_POST['date_start_minutes'];
$ampm = $_POST['date_start_meridiem'];

// Get the logged in users time settings
$time_format = $timedate->get_user_time_format();

// Combine date and time dependant on users settings
$time_separator = ":";
if (preg_match('/\d+([^\d])\d+([^\d]*)/s', $time_format, $match)) {
    $time_separator = $match[1];
}

if (!empty($hour) && !empty($minutes)) {

    $time_start = $hour . $time_separator . $minutes;

}

if (isset($ampm) && !empty($ampm)) {


    $time_start = $timedate->merge_time_meridiem($time_start, $timedate->get_time_format(), $ampm);
}

if (isset($time_start) && strlen($date) == 10) {

    $date_start = $date . ' ' . $time_start;
}


$call->retrieve($id);
// Set new the start date
$call->date_start = $date_start;
// Save the new start date
$call->save();
// Get the duration of the call
$hours = $call->duration_hours;
$mins = $call->duration_minutes;

// Get the new start date directly from the database to avoid sugar changing the format to users setting
$query = 'SELECT date_start FROM calls WHERE id="' . $id . '"';
$result = $call->db->getOne($query);
// Add on the duration of call and save the end date/time
$Date = strtotime($result);
$newDate = strtotime('+' . $hours . ' hours', $Date);
$newDate = strtotime('+' . $mins . ' minutes', $newDate);
$newDate = date("Y-m-d H:i:s", $newDate);
$call->date_end = $newDate;
// Save call and call attempt history
$reschedule = new Calls_Reschedule();

$reschedule->reason = $reason;
$reschedule->call_id = $id;

$call->save();
// Save call attempt history line
$reschedule->save();

