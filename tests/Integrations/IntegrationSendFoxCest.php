<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\SendFox;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationSendFoxCest
{
    use IntegrationHelper, SendFox, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_sendFox_push_data(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['SendFox Mailing Lists'=>getenv('SENDFOX_LIST_NAME')];
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureSendFox($I, "SendFox");

        $fieldMapping= $this->buildArrayWithKey($customName);
//        print_r($fieldMapping);
        $this->mapSendFoxFields($I,$fieldMapping,$listOrService);
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
            $remoteData = $this->fetchSendFoxData($I, $fakeData['Email Address'],);
            print_r($remoteData);
        }


//        print_r($remoteData);

        if (!empty($remoteData['data'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in SendFox";
        }else{
            $I->fail("Could not fetch data from SendFox". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData['data'])) {
//            $email = $remoteData['data'][0]['email'];
//            $firstName = $remoteData['data'][0]['first_name'];
//            $lastName = $remoteData['data'][0]['last_name'];
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
