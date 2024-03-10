<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait ClickSend
{
    use IntegrationHelper;

    public function configureClickSend(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Sender Number'), getenv('CLICKSEND_NUMBER'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Username'), getenv('CLICKSEND_USERNAME'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('API Key'), getenv('CLICKSEND_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"ClickSend");
    }

    public function mapClickSendFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"ClickSend Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Campaign List');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchClickSendData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'ClickSend is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $username = getenv('CLICKSEND_USERNAME');
        $password = getenv('CLICKSEND_API_KEY');
        $listId = getenv('CLICKSEND_LIST_ID');

        $url = "https://rest.clicksend.com/v3/lists/{$listId}/contacts";
//        dd($url);
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode("$username:$password"),
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
            return null;
        }
        curl_close($curl);

        $data = json_decode($response, true);

        foreach ($data['data']['data'] as $subscriber) {
            if (isset($subscriber['email']) && $subscriber['email'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null; // Return null if no matching record is found
    }
}