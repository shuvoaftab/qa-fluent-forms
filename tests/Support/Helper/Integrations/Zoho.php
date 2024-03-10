<?php

namespace Tests\Support\Helper\Integrations;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Zoho
{
    public function configureZoho(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            //since it is required human interaction, we will skip it for now
            $I->fail("Zoho is not connected, Please connect manually");
//            $I->retryClicked(FluentFormsSelectors::dropdown('Account URL'));
//            $I->clickOnExactText('US', 'Account URL');
//
//            $I->filledField(FluentFormsSettingsSelectors::apiField('Zoho CRM Client ID'), getenv('ZOHO_CLIENT_ID'));
//            $I->filledField(FluentFormsSettingsSelectors::apiField('Zoho CRM Client Secret'), getenv('ZOHO_SECRET_ID'));
//            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
//            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Zoho");
    }

    public function mapZohoFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Zoho Integration",$listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Services');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchZohoData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Zoho is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData( string $searchTerm)
    {

        $accessToken = self::refreshZohoAccessToken();
        $url = "https://www.zohoapis.com/crm/v5/Contacts";

        $queryParams = [
            'fields' => 'Last_Name,Email,Converted__s,Converted_Date_Time',
            'per_page' => 5,
        ];

        $url .= '?' . http_build_query($queryParams);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Zoho-oauthtoken ' . $accessToken,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);
        foreach ($data['data'] as $subscriber) {
            if (isset($subscriber['Email']) && $subscriber['Email'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null; // Return null if no matching record is found

    }

    public static function refreshZohoAccessToken()
    {
        $clientId = getenv("ZOHO_CLIENT_ID");
        $clientSecret = getenv("ZOHO_SECRET_ID");
        $refreshToken = getenv("ZOHO_REFRESH_TOKEN");

        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token';
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
