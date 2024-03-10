<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\ConstantContact;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationConstantContactCest
{
    use ConstantContact, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }
    // tests
    #[Group('Integration', 'all')]
    public function test_constantcontact_push_data(AcceptanceTester $I)
    {
//        $djfbhf = $this->fetchData("marigu@mailinator.co");
//        dd($djfbhf);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Constant Contact List'=>getenv("CONSTANT_CONTACT_LIST_NAME")];
        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true, $customName);
        $this->configureConstantContact($I, "Constant Contact");

        $fieldMapping = array_merge($this->buildArrayWithKey($customName));
        $this->mapConstantContactFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
        print_r($fakeData);
//        exit();

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchConstantContactData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Constant Contact";
        }else{
            $I->fail("Could not fetch data from Constant Contact". PHP_EOL. $remoteData);
        }


    }


}
