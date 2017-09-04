<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 * @Package Gantt chart
 * @copyright Andrew Mclaughlan 2014
 * @author Andrew Mclaughlan <andrew@mclaughlan.info>
 */

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.detail.php');
class ProjectViewGanttChart extends ViewDetail
{

    /**
     * ProjectViewGanttChart constructor.
     */
    public function __construct()
    {
        parent::SugarView();
    }


    public function display()
    {

        global $db, $mod_strings, $app_strings;

        $project = new Project();

		if (!isset($_REQUEST['project_id']) || trim($_REQUEST['project_id']) === '') {
            $_REQUEST['project_id'] = $_REQUEST['record'];
        }
        $project->retrieve($_REQUEST['project_id']);
        //Get project resources (users & contacts)
        $resources1 = $project->get_linked_beans('project_users_1', 'User');
        $resources2 = $project->get_linked_beans('project_contacts_1', 'Contact');
        //Combine resources into array of objects
        $resource_array = array();
        foreach ($resources1 as $user) {
            $resource = new stdClass;
            $resource->id = $user->id;
            $resource->name = $user->name;
            $resource->type = 'user';
            $resource_array[] = $resource;
        }
        foreach ($resources2 as $contact) {
            $resource = new stdClass;
            $resource->id = $contact->id;
            $resource->name = $contact->name;
            $resource->type = 'contact';
            $resource_array[] = $resource;
        }


        //Get the start and end date of the project in database format
        $query = "SELECT estimated_start_date FROM project WHERE id = '{$project->id}'";
        $start_date = $db->getOne($query);
        $query = "SELECT estimated_end_date FROM project WHERE id = '{$project->id}'";
        $end_date = $db->getOne($query);

        parent::display();

        if (ACLController::checkAccess('Project', 'edit', true)) {
            echo '<div style="clear:both;padding:10px;"><button id="add_button" class="gantt_button">'
                . $mod_strings['LBL_ADD_NEW_TASK'] . '</button></div>';
            echo '<input id="is_editable" name="is_editable" type="hidden" value="1" >';
        }

        $ss = new Sugar_Smarty();
        $ss->assign('app', $app_strings);
        $ss->assign('mod', $mod_strings);
        $ss->assign('theme', SugarThemeRegistry::current());
        $ss->assign('langHeader', get_language_header());
        $ss->assign('projectID', $project->id);
        $ss->assign('projectBusinessHours', $project->override_business_hours);

        $ss->assign('projectTasks', $_REQUEST["record"]);

        $ss->display('modules/Project/tpls/PopupBody.tpl');

    }

    //Returns the time span between two dates in years  months and days
    function time_range($start_date, $end_date)
    {
        global $mod_strings;

        $datetime1 = new DateTime($start_date);
        $datetime2 = new DateTime($end_date);
        $datetime2->add(new DateInterval('P1D')); //Add 1 day to include the end date as a day
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%m '.$mod_strings['LBL_MONTHS'].', %d '.$mod_strings['LBL_DAYS']);
    }
}
