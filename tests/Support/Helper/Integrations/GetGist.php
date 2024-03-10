<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait GetGist
{
    use IntegrationHelper;

    public function configureGetGist(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->tryToFilledField(FluentFormsSettingsSelectors::apiField("GetGist API Key"), getenv('GETGIST_API'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"GetGist");
    }

    public function mapGetGistFields(AcceptanceTester $I, array $fieldMapping, array $listOrService=null)
    {
        $this->mapEmailInCommon($I,"GetGist Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchGetGistData(AcceptanceTester $I, string $emailToFetch)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($I,$emailToFetch);
            if (isset($remoteData['errors']) && $remoteData['errors'][0]['code'] === 'not_found') {
                $I->wait(30, 'GetGist is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(AcceptanceTester $I, string $emailToFetch)
    {
//        $I->wait(10,'GetGist is taking too long to respond. Trying again...');

        $apiKey = getenv('GETGIST_API');
        $url = 'https://api.getgist.com/contacts?email=' . urlencode($emailToFetch);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
            return false;
        }
        curl_close($curl);
        return json_decode($response, true);
    }
}