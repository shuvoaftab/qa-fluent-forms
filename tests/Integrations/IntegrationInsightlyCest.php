<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\Insightly;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationInsightlyCest
{
    use Insightly, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_insightly_push_data(AcceptanceTester $I)
    {
//        $hbvdf = $this->fetchInsightlyData($I, 'qa@wpmanageninja.com');
//        dd($hbvdf);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService = [
            'Insightly Services' => 'Contact',
        ];
        $customName=[
            'email' => 'Email',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureInsightly($I, "Insightly");

        $fieldMapping = array_merge($this->buildArrayWithKey($customName), ['Email' => 'Email']);
//        unset($fieldMapping['Last Name']);
//        print_r($fieldMapping);
        $this->mapInsightlyFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email'=>'email',
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
            $remoteData = $this->fetchInsightlyData($I, $fakeData['Email']);
            print_r($remoteData);
        }


//        print_r($remoteData);
        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Insightly";
        }else{
            $I->fail("Could not fetch data from Insightly". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $firstName =  $remoteData['FIRST_NAME'];;
//            $lastName = $remoteData['LAST_NAME'];
//            $email = $remoteData['EMAIL_ADDRESS'];
//
//            $I->assertString([
//                $fakeData['Email'] => $email,
//                $fakeData['First Name'] => $firstName,
//                $fakeData['Last Name'] => $lastName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }
    }
}
