<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\HubSpot;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationHubSpotCest
{
    use HubSpot, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_hubspot_push_data(AcceptanceTester $I)
    {
//        $jvbh = $this->fetchHubSpotData($I,'alysha.wehner@yahoo.co');
//        dd($jvbh);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureHubSpot($I, "HubSpot");

        $fieldMapping = $this->buildArrayWithKey($customName);

        $this->mapHubSpotFields($I,$fieldMapping);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
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
            $remoteData = $this->fetchHubSpotData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData["results"])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in HubSpot";
        }else{
            $I->fail("Could not fetch data from HubSpot". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $firstName =  $remoteData['properties']['firstname'];;
//            $LastName =  $remoteData['properties']['lastname'];;
//            $email = $remoteData['properties']['email'];
//
//            $I->assertString([
//                $fakeData['Email Address'] => $email,
//                $fakeData['First Name'] => $firstName,
//                $fakeData['Last Name'] => $LastName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }


    }
}
