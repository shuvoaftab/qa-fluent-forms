<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\Integrations\MailerLite;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationMailerLiteCest
{
    use MailerLite, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_mailerLite_push_data(AcceptanceTester $I): void
    {
//        $hgg = $this->fetchData("sabina.abbott@yahoo.co");
//        dd($hgg);
        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Group List'=>getenv('MAILERLITE_GROUP')];
        $customName=[
            'email'=>'Email',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureMailerLite($I, "MailerLite");

        $fieldMapping= [
            'Email'=>'Email',
            'Name'=>'Name'
        ];
//        print_r($fieldMapping);
        $this->mapMailerLiteFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

        $fillAbleDataArr = [
            'Email'=>'email',
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
            $remoteData = $this->fetchMailerLiteData($I, $fakeData['Email'],);
            print_r($remoteData);
        }
        if (isset($remoteData['data'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email'],
                $fakeData['First Name']." ". $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in MailerLite";
        }else{
            $I->fail("Could not fetch data from MailerLite");
        }

//        if (isset($remoteData['data'])) {
//            $email = $remoteData['data']['email'];
//            $name = $remoteData['data']['fields']['name'];
//
//            $I->assertString([
//                $fakeData['Email'] => $email,
//                $fakeData['First Name']." ". $fakeData['Last Name'] => $name,
//
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }


    }
}
