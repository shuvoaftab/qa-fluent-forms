<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;


trait MooSend
{
    use IntegrationHelper;
    public function configureMooSend(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('MooSend API Key'), getenv('MOOSEND_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"MooSend");

    }
    public function mapMooSendFields(AcceptanceTester $I, array $fieldMapping, array $listOrService): void
    {
        $this->mapEmailInCommon($I,"Moosend Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping);

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);

    }
    public function fetchMooSendData(AcceptanceTester $I, string $emailToFetch)
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);

    }
    public function fetchData(string $emailToFetch)
    {
        $hostname = 'api.moosend.com';
        $mailingListId = getenv('MOOSEND_LIST_ID');
        $format = 'json';
        $apiKey = getenv('MOOSEND_API_KEY');
        $apiEndpoint = "https://{$hostname}/v3/subscribers/{$mailingListId}/view.{$format}?apikey={$apiKey}&Email={$emailToFetch}";

//        dd($apiEndpoint);
        $curl = curl_init($apiEndpoint);
//        dd($curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
//        dd($response);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);
        return json_decode($response, true);
    }


}