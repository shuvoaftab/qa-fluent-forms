<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait PipeDrive
{
    use IntegrationHelper;
    public function configurePipeDrive(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Pipedrive API Token'), getenv('PIPEDRIVE_API'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Pipedrive");
    }

    public function mapPipeDriveFields(AcceptanceTester $I, array $fieldMapping, array $listOrService)
    {
        $this->mapEmailInCommon($I,"Pipedrive Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,"Services");
        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchPipeDriveData(AcceptanceTester $I, string $searchTerm)
    {
        $arguments = [$searchTerm, true, null];
        $callback = function () use ($arguments) {
            return $this->fetchData(...$arguments);
        };
        return $this->retryFetchingData($I, $callback, $searchTerm, 6);
    }
    public function fetchData(string $searchTerm, bool $exactMatch = true, $organizationId = null)
    {
        $apiToken = getenv('PIPEDRIVE_API');
        $url = 'https://wpmanageninja.pipedrive.com/api/v1/persons/search';
        $query = http_build_query([
            'term' => $searchTerm,
            'exact_match' => $exactMatch ? '1' : '0',
            'organization_id' => $organizationId,
            'api_token' => $apiToken,
        ]);
        $url .= '?' . $query;
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);
        $data = json_decode($response, true);
        return $data['data']['items'];
    }
}