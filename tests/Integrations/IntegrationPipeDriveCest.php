<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\PipeDrive;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationPipeDriveCest
{
    use IntegrationHelper,PipeDrive, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_pipedrive_people_creation(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =[
            'Services'=>'Person',
            'Owner'=>getenv('PIPEDRIVE_OWNER'),
            'Visible to'=>'item owner',
        ];
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configurePipeDrive($I, "Pipedrive");

        $fieldMapping= [
            'Email'=>'Email',
            'Name'=>'Name',
        ];
        print_r($fieldMapping);
        $this->mapPipeDriveFields($I,$fieldMapping,$listOrService);
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
            $remoteData = $this->fetchPipeDriveData($I, $fakeData['Email Address'],);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name']." ".$fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in PipeDrive";
        }else{
            $I->fail("Could not fetch data from PipeDrive". PHP_EOL. $remoteData);
        }

//        if (isset($remoteData)) {
//            $email = $remoteData[0]['item']['primary_email'];
//            $name = $remoteData[0]['item']['name'];
//
//            $I->assertString([
//                $fakeData['Email Address'] => $email,
//                $fakeData['First Name']." ".$fakeData['Last Name'] => $name,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }


    }
}
