<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Sendinblue;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationSendinblueCest
{
    use IntegrationHelper, SendinBlue, DataGenerator, GeneralFieldCustomizer;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_sendinblue_push_data(AcceptanceTester $I): void
    {
        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Sendinblue Segment'=>getenv('SENDINBLUE_LIST_NAME')];
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureSendinblue($I, "Sendinblue");

        $fieldMapping = $this->buildArrayWithKey($customName);
//            [
//            'email'=>'Email Address',
//            'nameFields'=>['First Name','Last Name'],
//        ];
        $this->mapSendinblueFields($I,$fieldMapping,$listOrService);
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
            $remoteData = $this->fetchSendinblueData($I, $fakeData['Email Address'],);
            print_r($remoteData);
        }

        if (!isset($remoteData['message'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Sendinblue";
        }else{
            $I->fail("Could not fetch data from Sendinblue". PHP_EOL. $remoteData);
        }

//        if (!isset($remoteData['message'])) {
//            $email = $remoteData['email'];
//            $firstName = $remoteData['attributes']['FIRSTNAME'];
//            $lastName = $remoteData['attributes']['LASTNAME'];
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
