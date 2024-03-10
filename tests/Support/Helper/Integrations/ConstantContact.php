<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait ConstantContact
{
    use IntegrationHelper;
    public function configureConstantContact(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Constant Contact access Key'), getenv('CONSTANT_CONTACT_ACCESS_KEY'));

            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Your settings has been updated!");
        }
        $this->configureApiSettings($I,"Consta");
    }

    public function mapConstantContactFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Constant Contact Integration",$listOrService);
        $this->assignShortCode($I,$fieldMapping);

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchConstantContactData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData["contacts"])) {
                $I->wait(30, 'Constant Contact is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $accessToken = self::refreshConstantContactAccessToken();

        $apiUrl = "https://api.cc.email/v3/contacts?email={$searchTerm}";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ]);
        $response = curl_exec($ch);

        curl_close($ch);
        return json_decode($response, true);
    }

    public static function refreshConstantContactAccessToken()
    {
        $clientId = getenv("CONSTANT_CONTACT_CLIENT_ID");
        $clientSecret = getenv("CONSTANT_CONTACT_CLIENT_SECRET");
        $refreshToken = getenv("CONSTANT_CONTACT_REFRESH_TOKEN");

        $apiUrl = "https://authz.constantcontact.com/oauth2/default/v1/token";

        $postData = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        $postData = http_build_query($postData);

        $headers = [
            'Authorization: Basic ' . base64_encode("$clientId:$clientSecret"),
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ];

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'];
    }
}