<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\MailJet;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationMailJetCest
{
    use MailJet, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_mailJet_push_data(AcceptanceTester $I)
    {
//        $dhfv = $this->fetchMailJetData($I, 'nogowem@gmail.com');
//        dd($dhfv);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService = [
            'Mailjet Services' => 'Contact',
        ];
        $customName=[
            'email' => 'Contact Email',
            'nameFields'=>'Contact Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureMailJet($I, "Mailjet");

        $fieldMapping = ['Contact Email' => 'Contact Email', 'Contact Name' => 'Contact Name'];
//        unset($fieldMapping['Last Name']);
//        print_r($fieldMapping);
        $this->mapMailJetFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Contact Email'=>'email',
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
            $remoteData = $this->fetchMailJetData($I, $fakeData['Contact Email']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Contact Email'],
                $fakeData['First Name']." ".$fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in MailJet";
        }else{
            $I->fail("Could not fetch data from MailJet". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $name =  $remoteData['Name'];;
//            $email = $remoteData['Email'];
//
//            $I->assertString([
//                $fakeData['Contact Email'] => $email,
//                $fakeData['First Name']." ".$fakeData['Last Name'] => $name,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }

    }
}
