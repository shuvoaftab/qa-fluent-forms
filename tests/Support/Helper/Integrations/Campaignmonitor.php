<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Campaignmonitor
{

    public function configureCampaignmonitor(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Campaign Monitor API Key'), getenv('CAMPAIGNMONITOR_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->wait(1);
            $I->reloadPage();
            $I->clicked("//i[contains(@class,'el-select__caret')]");
            $I->clicked("(//li[contains(@class,'el-select-dropdown')])[1]");

            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Monitor");
    }

    public function mapCampaignmonitorFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Campaignmonitor Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchCampaignmonitorData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 2; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (isset($remoteData['Code'])) {
                $I->wait(30, 'Campaignmonitor is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $apiKey = getenv('CAMPAIGNMONITOR_API_KEY');
        $listId = '05be437da0840c441d489ac758d0a9d7';
        $apiUrl = "https://api.createsend.com/api/v3.3/subscribers/{$listId}.json?email={$searchTerm}";
        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($apiKey . ':x'),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);

    }
}