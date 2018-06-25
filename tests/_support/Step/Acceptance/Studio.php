<?php
namespace Step\Acceptance;

class Studio extends Administration{
    
    /**
     * This functions only purpose is to navigate to the 'Fields' section of a module in Studio.
     * 
     * @param string $moduleName
     */
   public function goToStudioModuleFields($moduleName){
       $I = $this;
       
       //Navigating the CRM to Studio -> $moduleName -> Fields.
       $I->gotoAdministration();
       $I->click('#studio');
       $I->waitForElementVisible('#studiolink_'.$moduleName, 10);
       $I->click('#studiolink_'.$moduleName);
       $I->waitForText('Fields', 10);
       $I->click('Fields');
    }
    
    /**
     * This function creates fields via Studio based on the parameters it's fed. 
     * 
     * @param string $moduleName
     * @param string $fieldName
     * @param string $fieldType
     */
    public function testScenarioCreateField($moduleName, $fieldName, $fieldType){
        $I = $this;

        //Function takes in a $moduleName parameter and then navigates to the module's field section in Studio.
        $I->goToStudioModuleFields($moduleName);
        

        $I->click(['name' => 'addfieldbtn']);
        $I->wait(1);
        
        //Some modules contain the "Flex Relate" value in the 'Data Type' field. The two conditional statements below are only
        //included to cater to any circumstances where this field is selected.
        if ($fieldType == "Flex Relate" && $I->seePageHas("Flex Relate") == false){
            echo "No Flex Relate";
            return;
        }
        if ($fieldType == "Flex Relate" && $I->seePageHas("Flex Relate") == true){
            $I->selectOption("form select[name=type]", $fieldType);
            $I->wait(1);
            $I->clearField(['name' => 'help']);
            $I->fillField(['name' => 'help'], 'Test Help Text');
            $I->click(['name' => 'fsavebtn']);
            $I->waitForElement(['name' => 'addfieldbtn'], 10);
            return;
        }
        
        //Selecting the field's type based on the $fieldType parameter passed to the function.
        $I->selectOption("form select[name=type]", $fieldType);
        
        
        //Populating relevant fields if they are currently visible. Could easily add additional statements here to cover all fields
        //present during creation.
        if ($I->seePageHas('Field Name:')){
            $I->clearField('#field_name_id');
            $I->fillField('#field_name_id', $fieldName);
        }  
        if ($I->seePageHas('Help Text:')){
            $I->clearField(['name' => 'help']);
            $I->fillField(['name' => 'help'], 'Test Help Text');
        }
        if ($I->seePageHas('Comment Text:')){
            $I->clearField(['name' => 'comments']);
            $I->fillField(['name' => 'comments'], 'Test Comment Text');
        }  
        
        $I->click(['name' => 'fsavebtn']);
        $I->waitForElement(['name' => 'addfieldbtn'], 10);
    }
   
    /**
     * This function deletes fields from Studio based on the parameters it's fed.
     * 
     * @param string $moduleName
     * @param string $fieldName
     * @param string $fieldType
     */
    public function testScenarioDeleteField($moduleName, $fieldName, $fieldType){
        $I = $this;   
        
        //Fields of the "Address" Type create multiple fields (e.g. Country, City, Postal Code) so additional clean up is required.
        if($fieldType == "Address"){
            $I->goToStudioModuleFields($moduleName);
            $I->waitForElement('#'.$fieldName.'_c', 10);
            $I->clickWithLeftButton('#'.$fieldName.'_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
            
            $I->goToStudioModuleFields($moduleName);
            $I->waitForElement('#'.$fieldName.'_country_c', 10);  
            $I->clickWithLeftButton('#'.$fieldName.'_country_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
            
            $I->goToStudioModuleFields($moduleName);
            $I->waitForElement('#'.$fieldName.'_state_c', 10);
            $I->clickWithLeftButton('#'.$fieldName.'_state_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
            
            $I->goToStudioModuleFields($moduleName);
            $I->waitForElement('#'.$fieldName.'_postalcode_c', 10);
            $I->clickWithLeftButton('#'.$fieldName.'_postalcode_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
            
            $I->goToStudioModuleFields($moduleName);
            $I->waitForElement('#'.$fieldName.'_city_c', 10);
            $I->clickWithLeftButton('#'.$fieldName.'_city_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
        } else {
            //Function takes in a $moduleName parameter and then navigates to the module's field section in Studio.
            $I->goToStudioModuleFields($moduleName);
            $I->wait(1);
            
            //Some modules contain the "Flex Relate" value in the 'Data Type' field. The two conditional statements below are only
            //included to cater to any circumstances where this field is selected.
            if ($fieldType == "Flex Relate" && $I->seePageHas("parent_name") == false){
                echo "No Flex Relate";
                return;
            }
            if ($fieldType == "Flex Relate" && $I->seePageHas("parent_name") == true){
                $I->waitForElement('#parent_name');
                $I->clickWithLeftButton('#parent_name');
                $I->waitForElement(['name' => 'fdeletebtn'], 10);
                $I->click(['name' => 'fdeletebtn']);
                $I->acceptPopup();
                $I->waitForElement(['name' => 'addfieldbtn'], 10);
                return;
            }
            
            //Deleting field based on the $fieldName value that was passed to the function.  
            $I->waitForElement('#'.$fieldName.'_c', 10);
            $I->clickWithLeftButton('#'.$fieldName.'_c');
            $I->waitForElement(['name' => 'fdeletebtn'], 10);
            $I->click(['name' => 'fdeletebtn']);
            $I->acceptPopup();
            $I->waitForElement(['name' => 'addfieldbtn'], 10);
        }
    }
}