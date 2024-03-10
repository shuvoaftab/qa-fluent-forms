<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\Campaignmonitor;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationCampaignmonitorCest
{
    use Campaignmonitor, GeneralFieldCustomizer, IntegrationHelper, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_campaignmonitor_push_data(AcceptanceTester $I)
    {
//        $jhcb = $this->fetchCampaignmonitorData($I,"balowykosi@mailinator.com");
//        dd($jhcb);

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['Campaign Monitor List'=>getenv('CAMPAIGNMONITOR_LIST')];
        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Full Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true, $customName);
        $this->configureCampaignmonitor($I, "Campaign Monitor");

        $fieldMapping = ['Full Name' => 'Full Name'];

        $this->mapCampaignmonitorFields($I, $fieldMapping, $listOrService);
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
            $remoteData = $this->fetchCampaignmonitorData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
            ]);
            echo " Hurray.....! Data found";
        }else{
            $I->fail("Could not fetch data from Campaignmonitor" . PHP_EOL. $remoteData);
        }


    }
}
