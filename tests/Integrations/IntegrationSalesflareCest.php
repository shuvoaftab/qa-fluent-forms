<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Salesflare;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationSalesflareCest
{
    use Salesflare, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_Salesflare_push_data(AcceptanceTester $I)
    {
//        $jhvhf = $this->fetchSalesflareData($I,"qixar@mailinator.com");
//        dd($jhvhf);

        $pageName = __FUNCTION__.'_'.rand(1,100);

        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureSalesflare($I, "Salesflare");

        $fieldMapping = $this->buildArrayWithKey($customName);
//        print_r($fieldMapping);

        $this->mapSalesflareFields($I,$fieldMapping);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_salesflare_push_data_93");
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
            $remoteData = $this->fetchSalesflareData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (isset($remoteData['subscribers'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in Salesflare";
        }else{
            $I->fail("Could not fetch data from Salesflare" . PHP_EOL. $remoteData);
        }


//        if (isset($remoteData)) {
//            $firstName =  $remoteData[0]['firstname'];;
//            $LastName =  $remoteData[0]['lastname'];;
//            $email = $remoteData[0]['email'];
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
