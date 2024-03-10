<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Salesflare
{

    public function configureSalesflare(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Salesflare API Key'), getenv('SALSEFLARE_API_KEY'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Salesflare");
    }

    public function mapSalesflareFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Salesflare Integration", $listOrService);
        $this->assignShortCode($I,$fieldMapping,'Map Fields');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchSalesflareData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($I, $searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Salesflare is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(AcceptanceTester $I, $searchTerm)
    {
        $apiUrl = 'https://api.salesflare.com/contacts';
        $bearerToken = getenv('SALSEFLARE_API_KEY');

        $ch = curl_init();

        $queryParams = ['email' => $searchTerm];
        if (!empty($searchTerm)) {
            $apiUrl .= '?' . http_build_query($queryParams);
        }
//        dd($apiUrl);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $bearerToken,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            $I->fail($data['message']);
        }

        return $data;
    }
}