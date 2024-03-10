<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Activecampaign
{
    use IntegrationHelper;

    public function mapActivecampaignField(AcceptanceTester $I, array $fieldMapping, array $listOrService=null): void
    {
        $this->mapEmailInCommon($I, "Activecampaign Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping);
        $I->clicked(FluentFormsSelectors::saveButton("Save Feed"));
    }

    public function configureActivecampaign(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
            $isConfigured = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
            if (!$isConfigured) {
                $I->fillField(
                    FluentFormsSelectors::commonFields("ActiveCampaign API URL", "API URL"),
                    getenv("ACTIVECAMPAIGN_API_URL")
                );
                $I->fillField(
                    FluentFormsSelectors::commonFields("ActiveCampaign API Key", "API Key"),
                    getenv("ACTIVECAMPAIGN_API_KEY")
                );
                $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            }
            $this->configureApiSettings($I, "ActiveCampaign");
        }

    public function fetchActivecampaignData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 2; $i++) {
            $remoteData = $this->fetchData($I,$searchTerm);
            if (empty($remoteData['contacts'])) {
                $I->wait(30, 'Activecampaign is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }
    public function fetchData(AcceptanceTester $I, string $searchTerm)
    {
//        $I->wait(30,'Activecampaign is taking too long to respond. Trying again...');
        $apiUrl = getenv("ACTIVECAMPAIGN_API_URL").'/api/3/contacts';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '?email=' . urlencode($searchTerm));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Api-Token: ' . getenv("ACTIVECAMPAIGN_API_KEY"),
        ]);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response, true);

    }

}