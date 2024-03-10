<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Insightly
{

    public function configureInsightly(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Insightly API URL'), getenv('INSIGHTLY_API_URL'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Insightly API Key'), getenv('INSIGHTLY_API_KEY'));

            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Insightly");
    }

    public function mapInsightlyFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null): void
    {
        $this->mapEmailInCommon($I,"Insightly Integration", $listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Insightly Services');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchInsightlyData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Insightly is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $apiUrl = getenv('INSIGHTLY_API_URL').'/v3.1/Contacts';
        $apiToken = getenv('INSIGHTLY_API_KEY');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($apiToken),
            'Accept-Encoding: gzip',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $decompressedResponse = gzdecode($response);

        $data = json_decode($decompressedResponse, true);
        foreach ($data as $subscriber) {
            if (isset($subscriber['EMAIL_ADDRESS']) && $subscriber['EMAIL_ADDRESS'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null;
    }
}