<?php

namespace Tests\Support\Helper\Integrations;

use Google\Exception;
use Google_Client;
use Google_Service_Sheets;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Googlesheet
{
    use IntegrationHelper;

    public function mapGoogleSheetField(AcceptanceTester $I, array $otherFieldArray): void
    {
        $I->waitForElement(FluentFormsSelectors::feedName, 30);
        $I->fillField(FluentFormsSelectors::feedName, 'Google Sheets');
        $I->fillField(FluentFormsSelectors::spreadSheetID, getenv("GOOGLE_SPREADSHEET_ID"));
        $I->fillField(FluentFormsSelectors::workSheetName, getenv("GOOGLE_SHEET_NAME"));

        global $fieldCounter;
        $fieldCounter = 1;
        $counter = 1;
        foreach ($otherFieldArray as $fieldLabel => $fieldValue)
        {
            $I->fillField(FluentFormsSelectors::fieldLabel($counter), $fieldLabel);
            $I->fillField(FluentFormsSelectors::fieldValue($counter), $fieldValue);
            $I->clicked(FluentFormsSelectors::addMappingField('Spreadsheet Fields',$counter));
            $counter++;
            $fieldCounter++;
        }
        $I->click(FluentFormsSelectors::removeMappingField('Spreadsheet Fields',$fieldCounter));
        $I->click(FluentFormsSelectors::saveButton("Save Feed"));
    }
    public function configureGoogleSheet(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        
        $saveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if ($saveSettings) // Check if the Google sheet integration is already configured.
        {
            $this->configureApiSettings($I,"Google");
        }else{
            $I->fail('Please connect the Google sheet integration manually.');
        }
    }
    public function fetchGoogleSheetData(AcceptanceTester $I, string $emailToFetch): array
    {
        return $this->retryFetchingData($I,[$this, 'fetchData'], $emailToFetch,8);
    }
    /**
     * @throws Exception
     */
    public function fetchData(string $emailToFetch): array
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig('tests/Support/Data/googlesheet.json'); // Path to the JSON credentials

        $service = new Google_Service_Sheets($client);
        $response = $service->spreadsheets_values->get(getenv("GOOGLE_SPREADSHEET_ID"), getenv("GOOGLE_SHEET_NAME_AND_RANGE"));
        $values = $response->getValues();

        $expectedRow = [];

        foreach ($values as $row) {
            if ($row[0] === $emailToFetch || $row[1] === $emailToFetch || $row[2] === $emailToFetch) {
                $expectedRow = $row;
            }
        }
        return $expectedRow;
    }
}
