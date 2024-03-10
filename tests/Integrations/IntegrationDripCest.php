<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\Integrations\Drip;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationDripCest
{
    use IntegrationHelper,Drip, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_drip_push_data(AcceptanceTester $I)
    {
//        $remoteData = $this->fetchDripData($I, "omraz@yandex.com");
//        dd($remoteData);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureDrip($I, "Drip");

        $fieldMapping=[
            'First Name' => 'First Name',
            'Last Name' => 'Last Name',
        ];
        $this->mapDripFields($I,$fieldMapping);
        $this->preparePage($I,$pageName);

        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
//        print_r($fakeData);

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchDripData($I, $fakeData['Email Address'],);
            print_r($remoteData);
        }

        if (isset($remoteData['subscribers'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Drip";
        }else{
            $I->fail("Could not fetch data from Drip. " . PHP_EOL. $remoteData);
        }

//        print_r($remoteData);
//        if (!isset($remoteData['errors'])) {
//            $email = $remoteData['subscribers'][0]['email'];
//            $firstName = $remoteData['subscribers'][0]['first_name'];
//            $lastName = $remoteData['subscribers'][0]['last_name'];
//
//            $I->assertString([
//                $fakeData['Email Address'] => $email,
//                $fakeData['First Name'] => $firstName,
//                $fakeData['Last Name'] => $lastName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }
    }
}
