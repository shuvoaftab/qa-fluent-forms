<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait SendFox
{

    public function configureSendFox(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField("//textarea[@placeholder='API Key']", getenv('SENDFOX_ACCESS_TOKEN'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"SendFox");
    }

    public function mapSendFoxFields(AcceptanceTester $I, array $fieldMapping, array $listOrService): void
    {
        $this->mapEmailInCommon($I,"SendFox Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping);

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchSendFoxData(AcceptanceTester $I, string $emailToFetch)
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);
    }

    public function fetchData(string $emailToFetch)
    {
        $accessToken = getenv("SENDFOX_ACCESS_TOKEN");
        $apiEndpoint = "https://api.sendfox.com/contacts?email={$emailToFetch}";
        $curl = curl_init($apiEndpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);
        return json_decode($response, true);
    }
}