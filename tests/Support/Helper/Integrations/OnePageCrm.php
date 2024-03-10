<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait OnePageCrm
{
    use IntegrationHelper;

    public function configureOnePageCrm(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('OnePageCrm User ID'), getenv('ONEPAGECRM_API_USER'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('OnePageCrm Api Key'), getenv('ONEPAGECRM_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"OnePageCrm");
    }

    public function mapOnePageCrmFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"OnePageCrm Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'OnePageCRM Services');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchOnePageCrmData(AcceptanceTester $I, string $emailToFetch)
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);
    }

    public function fetchData(string $emailToFetch)
    {
        $userId = '65014693cbd21b3fc4da90a0';
        $apiKey = 'HNscQBQAdOTumBYkPUgGLl2kHylZvwoNiWixH6l0vws=';
        $apiUrl = 'https://app.onepagecrm.com/api/v3/contacts';
        $apiUrl .= '?email=' . urlencode($emailToFetch);
        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$userId:$apiKey");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return false;
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}