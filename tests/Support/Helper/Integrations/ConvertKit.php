<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait ConvertKit
{
    use IntegrationHelper;
    public function configureConvertKit(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('ConvertKit API Key'), getenv('CONVERTKIT_API'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('ConvertKit API Secret'), getenv('CONVERTKIT_SECRET'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"ConvertKit");
    }

    public function mapConvertKitFields(AcceptanceTester $I, array $fieldMapping, array $listOrService)
    {
        $this->mapEmailInCommon($I,"ConvertKit Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchConvertKitData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'ConvertKit is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $apiSecretKey = getenv("CONVERTKIT_SECRET");
        $fromDate = date('Y-m-d', strtotime('-2 day'));
        $toDate = date('Y-m-d');

        $url = "https://api.convertkit.com/v3/subscribers?api_secret={$apiSecretKey}&from={$fromDate}&to={$toDate}";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);

        $data = json_decode($response, true);

        foreach ($data['subscribers'] as $subscriber) {
            if (isset($subscriber['email_address']) && $subscriber['email_address'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null; // Return null if no matching record is found
    }

}