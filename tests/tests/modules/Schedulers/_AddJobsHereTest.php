<?php
/**
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
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

require_once 'include/SugarQueue/SugarJobQueue.php';
require_once 'install/install_utils.php';

class _AddJobsHereTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    protected function storeStateAll()
    {
        // save state
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('job_queue');
        $state->pushGlobals();

        return $state;
    }

    protected function restoreStateAll($state)
    {
        // clean up
        $state->popGlobals();
        $state->popTable('job_queue');

    }

    public function test__construct()
    {
        // save state
        $state = $this->storeStateAll();

        $ScheduledReport = new AORScheduledReportJob();
        $this->assertInstanceOf('AORScheduledReportJob', $ScheduledReport);

        // clean up
        $this->restoreStateAll($state);
    }

    public function testrun()
    {
        // save state
        $state = $this->storeStateAll();

        // test
        $ScheduledReport = new AORScheduledReportJob();

        $this->assertEquals(false, $ScheduledReport->run(0));
        $this->assertEquals('<style>
        h1{
            color: black;
        }
        .list
        {
            font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size: 12px;
            background: #fff;margin: 45px;width: 480px;border-collapse: collapse;text-align: left;
        }
        .list th
        {
            font-size: 14px;
            font-weight: normal;
            color: black;
            padding: 10px 8px;
            border-bottom: 2px solid black;
        }
        .list td
        {
            padding: 9px 8px 0px 8px;
        }
        </style>', $ScheduledReport->html);

        // clean up
        $this->restoreStateAll($state);
    }
}
