<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Airtable
{
    use IntegrationHelper;
    public function configureAirtable(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Airtable Access Token'), getenv('AIRTABLE_ACCESS_TOKEN'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Airtable");

    }

    public function mapAirtableFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null): void
    {
        $this->mapEmailInCommon($I,"Airtable Integration",$listOrService, false);

        $I->retryClicked(FluentFormsSelectors::dropdown('Select Table'));
        $I->retryClickOnText(getenv('AIRTABLE_TABLE_NAME'));

        $this->assignShortCode($I,$fieldMapping,'Airtable Configuration');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchAirtableData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Airtable is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;


    }

    public function fetchData(string $searchTerm)
    {
        $baseId = getenv('AIRTABLE_BASE_ID');
        $tableIdOrName = getenv('AIRTABLE_TABLE_NAME');
//        $recordId = 'rect30WSupWhxtq97';
        $accessToken = getenv('AIRTABLE_ACCESS_TOKEN');

        $url = "https://api.airtable.com/v0/{$baseId}/{$tableIdOrName}";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}",
        ]);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
            return false;
        }
        curl_close($curl);
        $data = json_decode($response, true);

        foreach ($data['records'] as $record) {
            if (isset($record['fields']['Name']) && $record['fields']['Name'] === $searchTerm) {
                return $record;
            }
        }
        return null; // Return null if no matching record is found

    }

}