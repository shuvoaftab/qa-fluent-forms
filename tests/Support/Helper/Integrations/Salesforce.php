<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Salesforce
{
    use IntegrationHelper;
    public function configureSalesforce(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            //since it is required human interaction, we will skip it for now
            $I->fail("Zoho is not connected, Please connect manually");

        }
        $this->configureApiSettings($I,"Salesforce");
    }

    public function mapSalesforceFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Salesforce Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Salesforce Services');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchSalesforceData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Salesforce is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $accessToken = self::refreshSalesforceAccessToken();
        $endpoint = getenv("SALSEFORCE_DOMAIN_URL")."/services/data/v52.0/query?q=";
        $query = "SELECT Id, Name, Email FROM Contact";
        $encodedQuery = urlencode($query);
        $fullEndpoint = $endpoint . $encodedQuery;

        $ch = curl_init($fullEndpoint);

        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            return false; // Handle the error as needed
        }

        curl_close($ch);

        $data = json_decode($response, true);

        foreach ($data['records'] as $subscriber) {
            if (isset($subscriber['Email']) && $subscriber['Email'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null; // Return null if no matching record is found
    }

    public function refreshSalesforceAccessToken()
    {
        $clientId = getenv("SALSEFORCE_CONSUMER_KEY");
        $clientSecret = getenv("SALSEFORCE_CONSUMER_SECRET");
        $refreshToken = getenv("SALSEFORCE_REFRESH_TOKEN");

        $tokenUrl = 'https://login.salesforce.com/services/oauth2/token';
        $postData = array(
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken
        );

        $ch = curl_init($tokenUrl);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['access_token'];

    }

}