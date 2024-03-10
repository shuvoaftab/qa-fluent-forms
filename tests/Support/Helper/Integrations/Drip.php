<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Drip
{
    public function mapDripFields(AcceptanceTester $I,array $fieldMapping): void
    {
        $this->mapEmailInCommon($I,"Drip Integration");
        $this->assignShortCode($I,$fieldMapping);

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);

    }
    public function configureDrip(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $saveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$saveSettings) // Check if the Mailchimp integration is already configured.
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Drip API Token'), getenv('DRIP_API_TOKEN'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Drip Account ID'), getenv('DRIP_ACCOUNT_ID'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Drip");

    }

    public function fetchDripData(AcceptanceTester $I, string $emailToFetch): array
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);
    }
    public function fetchData(string $emailToFetch)
    {
        $accountId = getenv('DRIP_ACCOUNT_ID');
        $apiKey = getenv('DRIP_API_TOKEN');
        $email = $emailToFetch;
        $apiEndpoint = "https://api.getdrip.com/v2/{$accountId}/subscribers/{$email}";
        $curl = curl_init($apiEndpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'User-Agent: Fluentforms (www.authlab.io)',
            'Authorization: Basic ' . base64_encode($apiKey . ':'),
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