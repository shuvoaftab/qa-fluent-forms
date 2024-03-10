<?php

namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Factories\DataProvider\ShortCodes;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\Activecampaign;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationActivecampaignCest
{
    use IntegrationHelper, Activecampaign, ShortCodes, DataGenerator, GeneralFieldCustomizer;

    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    #[Group('Integration','test','all')]
    public function test_activecampaign_push_data(AcceptanceTester $I): void
    {
//        $remoteData = $this->fetchActivecampaignData($I, "dare.emely@icloud.c");
//        dd($remoteData);

        $pageName = __FUNCTION__ . '_' . rand(1, 100);

        $listOrService = ['ActiveCampaign List' => 'Master Contact List'];
        $customName = [
            'email' => 'Email Address',
            'simpleText' => ['First Name', 'Last Name', 'Organization Name'],
            'phone' => 'Phone Number',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'simpleText', 'phone'],
        ], true, $customName);
        $this->configureActivecampaign($I, "ActiveCampaign");
        $fieldMapping = $this->buildArrayWithKey($customName);
        $this->mapActivecampaignField($I, $fieldMapping, $listOrService);
        $this->preparePage($I, $pageName);

        $fillableDataArr = [
            'Email Address' => 'email',
            'First Name' => 'firstName',
            'Last Name' => 'lastName',
            'Phone Number' => 'phoneNumber',
            'Organization Name' => 'company',
        ];
        $fakeData = $this->generatedData($fillableDataArr);

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }

        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchActivecampaignData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        // Retry to submit the form again if data not found
//        if (empty($remoteData['contacts'])) {
//            $I->amOnPage('/' . $pageName);
//
//            $fakeData = $this->generatedData($fillableDataArr);
//
//            // Fill the form with fake data
//            foreach ($fakeData as $selector => $value) {
//                $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
//            }
//
//            // Submit the form
//            $I->clicked(FieldSelectors::submitButton);
//
//            // Fetch ActiveCampaign data again
//            $remoteData = $this->fetchActivecampaignData($I, $fakeData['Email Address']);
//        }

        if (!empty($remoteData['contacts'])) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
                $fakeData['Phone Number'],
            ]);
            echo " Hurray.....! Data found in ActiveCampaign";
        }else{
            $I->fail("Could not fetch data from ActiveCampaign" . PHP_EOL. $remoteData);
        }
    }
}
