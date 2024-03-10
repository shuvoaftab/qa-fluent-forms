<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\ConvertKit;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationConvertKitCest
{
    use ConvertKit, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_convertKit_push_data(AcceptanceTester $I)
    {
//        $kjnfdj = $this->fetchConvertKitData($I,'jasitowe@gmail.com');
//        dd($kjnfdj);


        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['ConvertKit Form'=>getenv('CONVERTKIT_LIST')];
        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true,$customName);
        $this->configureConvertKit($I, "ConvertKit");

        $fieldMapping = $this->buildArrayWithKey($customName);
        unset($fieldMapping['Last Name']);

        $this->mapConvertKitFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
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
            $remoteData = $this->fetchConvertKitData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
            ]);
            echo " Hurray.....! Data found";
        }else{
            $I->fail("Could not fetch data from ConvertKit" . PHP_EOL. $remoteData);
        }


//        if (isset($remoteData)) {
//            $firstName =  $remoteData['first_name'];;
//            $email = $remoteData['email_address'];
//
//            $I->assertString([
//                $fakeData['Email Address'] => $email,
//                $fakeData['First Name'] => $firstName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }


    }
}
