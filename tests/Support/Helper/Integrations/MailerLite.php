<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait MailerLite
{
    use IntegrationHelper;
    public function configureMailerLite(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField("MailerLite API Key"), getenv('MAILERLITE_API'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"MailerLite");
    }

    public function mapMailerLiteFields(AcceptanceTester $I, array $fieldMapping, array $listOrService): void
    {
        $this->mapEmailInCommon($I,"SendFox Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchMailerLiteData(AcceptanceTester $I, string $emailToFetch)
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);

    }

    public function fetchData(string $emailToFetch)
    {
        $apiKey = getenv('MAILERLITE_API');
        $endpoint = 'https://connect.mailerlite.com/api/subscribers/' . urlencode($emailToFetch);
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
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