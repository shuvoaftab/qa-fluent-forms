<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\Airtable;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationAirtableCest
{
    use Airtable, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();

    }

    // tests
    #[Group('Integration','all')]
    public function test_airtable_push_data(AcceptanceTester $I)
    {
//        $jvj = $this->fetchAirtableData($I,"Quarterly launch");
//        print_r($jvj);
//        exit();

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Airtable Configuration'=>getenv('AIRTABLE_BASE_NAME')];
        $customName=[
            'nameFields'=>'Name',
            'simpleText'=>'Status',
            'timeDate'=>'Start date',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['nameFields','simpleText','timeDate'],
        ],'yes',$customName);
        $this->configureAirtable($I, "Airtable");

        $fieldMapping= [
            'Name'=>'Name',
            'Status'=>'Status',
            'Start date'=>'Start date',
        ];

        $this->mapAirtableFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
        $fillAbleDataArr = [
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
            'Status'=>'status',
            'Start date'=>['date'=>'m-d-Y'],
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
//        print_r($fakeData);
//        exit();

        foreach ($fakeData as $selector => $value) {
            if (str_contains(FluentFormsSelectors::fillAbleArea($selector), 'Start date')){
                $I->fillByJS(FluentFormsSelectors::fillAbleArea($selector), $value);
            }else{
                $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
            }
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchAirtableData($I, $fakeData['First Name']." ".$fakeData['Last Name']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Status'],
                $fakeData['First Name']." ".$fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found";
        }else{
            $I->fail("Could not fetch data" . PHP_EOL. $remoteData);
        }
    }
}
