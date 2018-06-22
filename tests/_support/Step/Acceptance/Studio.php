<?php
namespace Step\Acceptance;

class Studio extends Administration{
    
    /**
     * This function will be used to navigate to a specific module in Studio.
     * 
     * @param string $moduleName
     */
   public function goToStudioModuleFields($moduleName){
       $I = $this;
       
       $I->gotoAdministration();
       $I->click('#studio');
       $I->waitForElementVisible('#studiolink_'.$moduleName, 10);
       $I->click('#studiolink_'.$moduleName);
       $I->waitForText('Fields', 10);
       $I->click('Fields');
    }
    
    /**
     * This function will be used to create fields via Studio based on the parameters it's fed.
     * 
     * @param string $moduleName
     * @param string $fieldName
     * @param string $fieldType
     */
    public function testScenarioCreateField($moduleName, $fieldName, $fieldType){
        $I = $this;
        
        //Go to Studio>$moduleName>Fields.
        $I->goToStudioModuleFields($moduleName);
        
        //Selecting the field's type based on the $fieldType parameter passed to the function.
        $I->click(['name' => 'addfieldbtn']);
        $I->wait(1);
        $I->selectOption("form select[name=type]", $fieldType);
        
        //Populating relevant fields if they are currently visible. Could add more conditions to cover
        //all fields that may be visible during field creation in Studio.
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
        
        //Saving the field.
        $I->click(['name' => 'fsavebtn']);
        $I->waitForElement(['name' => 'addfieldbtn'], 10);
    }
   
    /*
    /**
     * This function will be used to add the newly created fields to the Edit and Detail Views.
     * 
     * @param string $moduleName
     */
    /*public function testScenarioAddFieldToView($moduleName){
        $I = $this;
        
        //Go to Studio>$moduleName>Layouts>Edit View.
        $I->gotoAdministration();
        $I->click('#studio');
        $I->waitForElementVisible('#studiolink_'.$moduleName, 10);
        $I->click('#studiolink_'.$moduleName);
        $I->waitForText('Layouts', 10);
        $I->click('Layouts');
        $I->waitForText('Edit View', 10);
        $I->click('Edit View');
        $I->wait(3);
        
        //Add the newly created field to the Edit and Detail Views.
        $I->dragAndDrop('#1003', '#33');
        $I->dragAndDrop('#le_label_32', '#1004');   
        $I->checkOption('#syncCheckbox'); 
        $I->click('#publishBtn');
        $I->waitForText('This operation is completed successfully', 10);
        $I->click('Close');
    }*/
    
    /**
     * This function will be used to delete fields from Studio based on the parameters it's fed.
     * 
     * @param string $moduleName
     * @param string $fieldName
     * @param string $fieldType
     */
    public function testScenarioDeleteField($moduleName, $fieldName, $fieldType){
        $I = $this;
        
        //Fields of "Address" Type create multiple fields so additional clean up is required.
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
            //Go to Studio>$moduleName>Fields.
            $I->goToStudioModuleFields($moduleName);

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