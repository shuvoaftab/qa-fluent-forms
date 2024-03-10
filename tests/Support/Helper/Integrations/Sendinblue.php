<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Sendinblue
{

    public function configureSendinblue(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Sendinblue V3 API Key'), getenv('SENDINBLUE_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Sendinblue");
    }

    public function mapSendinblueFields(AcceptanceTester $I,array $fieldMapping, $listOrService): void
    {
        $this->mapEmailInCommon($I,"Sendinblue Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping);

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);

    }

    public function fetchSendinblueData(AcceptanceTester $I, string $emailToFetch): array
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);
    }
    public function fetchData(string $emailToFetch)
    {
        $apiKey = getenv('SENDINBLUE_API_KEY');
        $apiEndpoint = 'https://api.brevo.com/v3/contacts/' . urlencode($emailToFetch);
        $curl = curl_init($apiEndpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'api-key: ' . $apiKey,
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