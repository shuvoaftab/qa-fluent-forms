<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\ClickSend;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationClickSendCest
{
    use ClickSend, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration', 'test', 'all')]
    public function test_clickSend_push_data(AcceptanceTester $I)
    {
//        $jvhhfdh = $this->fetchClickSendData($I,"russel.dax@yahoo.com");
//        dd($jvhhfdh);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Services'=>'Create Subscriber Contact', 'Campaign List'=>'Example List'];
        $customName=[
            'email' => 'Email',
            'phone'=>'Phone Number',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'phone'],
        ],true,$customName);
        $this->configureClickSend($I, "ClickSend");

        $fieldMapping = array_merge($this->buildArrayWithKey($customName),['Email'=>'Email']);

        $this->mapClickSendFields($I,$fieldMapping,$listOrService);

        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email'=>'email',
            'Phone Number' => 'e164PhoneNumber',

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
            $remoteData = $this->fetchClickSendData($I, $fakeData['Email']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email'],
                $fakeData['Phone Number'],
            ]);
            echo " Hurray.....! Data found";
        }else{
            $I->fail("Could not fetch data" . PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $phone =  $remoteData['phone_number'];
//            $email = $remoteData['email'];
//
//            $I->assertString([
//                $fakeData['Email'] => $email,
//                $fakeData['Phone Number'] => $phone,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }

    }
}
