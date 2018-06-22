<?php

class StudioCest
{ 
    /**
     * @param \AcceptanceTester $I
     * @param \Helper\WebDriverHelper $webDriverHelper
     * @param \Step\Acceptance\Studio $studio
     *  
     */
    public function testingStudio(
        \AcceptanceTester $I,
        \Step\Acceptance\Studio $studio,
        \Step\Acceptance\Repair $repair,
        \Helper\WebDriverHelper $webDriverHelper
    ){
        
        $I->amOnUrl($webDriverHelper->getInstanceURL());
        
        $I->loginAsAdmin();
        
        $modules = array("Tasks", "Users");
        
        /*"Accounts", "Bugs", "Calls", "Campaigns", "Cases", "Contacts", "AOS_Contracts",
            "Documents", "EmailTemplates", "Emails", "Employees", "FP_events", "AOS_Invoices",
            "AOK_Knowledge_Base_Categories", "Leads", "AOS_Products_Quotes", "FP_Event_Locations",
            "jjwg_Maps", "jjwg_Address_Cache", "jjwg_Areas", "jjwg_Markers", "Meetings", "Notes",
            "Opportunities", "OutboundEmailAccounts", "AOS_PDF_Templates", "AOS_Products",
            "AOS_Product_Categories", "AM_TaskTemplates", "ProjectTask", "Project", "AM_ProjectTemplates",
            "AOS_Quotes", "AOR_Reports", "AOR_Scheduled_Reports", "SecurityGroups", "Spots",
            "SurveyQuestionOptions", "SurveyQuestionResponses", "SurveyQuestions", "SurveyResponses",
            "Surveys", "Prospects", */
        
        $fieldTypes = array("IFrame", "Image", "Integer",
            "MultiSelect", "Flex Relate", "Phone", "Radio", "Relate", "TextArea", "URL", "TextField");
        
        /*"Address", "Checkbox", "Currency", "Date", "Datetime", "Decimal",
            "Dynamic DropDown", "DropDown", "Float", "HTML", */
        
        foreach($modules as $x){
            foreach($fieldTypes as $y){
                $studio->testScenarioCreateField($x, "Test_Studio_Field", $y);
                $studio->testScenarioDeleteField($x, "test_studio_field", $y);
                $repair->clickQuickRepairAndRebuild();
            } 
        }    
    }
}