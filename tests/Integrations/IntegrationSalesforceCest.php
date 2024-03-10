<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Salesforce;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationSalesforceCest
{
    use Salesforce, IntegrationHelper, DataGenerator, GeneralFieldCustomizer;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_salesforce_push_data(AcceptanceTester $I)
    {
//       $jhvh =  $this->fetchSalesforceData($I,'gherzog@icloud.com');
//        print_r($jhvh);
//        exit();

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Salesforce Services'=>'Contact'];
        $customName=[
            'email' => 'Email',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],'yes',$customName);
        $this->configureSalesforce($I, "Salesforce");

        $fieldMapping = array_merge($this->buildArrayWithKey($customName),['Email'=>'Email']);
        unset($fieldMapping['First Name']);

        $this->mapSalesforceFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email'=>'email',
            'Last Name'=>'firstName',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
//        print_r($fakeData);
//        exit();

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchSalesforceData($I, $fakeData['Email']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Salesforce";
        }else{
            $I->fail("Could not fetch data from Salesforce". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $lastName =  $remoteData['Name'];;
//            $email = $remoteData['Email'];
//
//            $I->assertString([
//                $fakeData['Email'] => $email,
//                $fakeData['Last Name'] => $lastName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }


    }
}
