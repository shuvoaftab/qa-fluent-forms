<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait IContact
{

    public function configureIContact(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Application Key'), getenv('ICONTACT_APPLICATION_KEY'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Account Email Address'), getenv('ICONTACT_EMAIL'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('API Password'), getenv('ICONTACT_API_PASSWORD'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Account ID'), getenv('ICONTACT_ACCOUNT_ID'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Client Folder ID'), getenv('ICONTACT_CLIENT_FOLDER_ID'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"iContact");
    }

    public function mapIContactFields(AcceptanceTester $I, array $fieldMapping, array $listOrService)
    {
        $this->mapEmailInCommon($I,"Airtable Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchIContactData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'IContact is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;

    }

    public function fetchData(string $searchTerm)
    {
        $accountId = getenv('ICONTACT_ACCOUNT_ID');
        $apiAppId = getenv('ICONTACT_APPLICATION_KEY');
        $folderID = getenv('ICONTACT_CLIENT_FOLDER_ID');
        $apiUsername = getenv('ICONTACT_EMAIL');
        $apiPassword = getenv('ICONTACT_API_PASSWORD');

        $apiEndpoint = "https://app.icontact.com/icp/a/{$accountId}/c/{$folderID}/contacts/";
//        $apiEndpoint = "https://app.icontact.com/icp/a/{$accountId}/c/{$folderID}/contacts/3745691";
//        dd($apiEndpoint);

        $curl = curl_init($apiEndpoint);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Expect: ',
            'Accept: application/json',
            'Content-type: application/json',
            "Api-Version: 2.2",
            "Api-AppId: {$apiAppId}",
            "Api-Username: {$apiUsername}",
            "Api-Password: {$apiPassword}"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
//        dd($response);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);
        $data =  json_decode($response, true);

        foreach ($data['contacts'] as $contact) {
            if (isset($contact['email']) && $contact['email'] === $searchTerm) {
                return $contact;
            }
        }
        return null; // Return null if no matching record is found
    }

}