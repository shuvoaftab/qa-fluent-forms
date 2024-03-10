<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait MailJet
{
    public function configureMailJet(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings)
        {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Mailjet API Key'), getenv('MAILJET_API_KEY'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Mailjet Secret Key'), getenv('MAILJET_SECRET_KEY'));

            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Mailjet");
    }

    public function mapMailJetFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        $this->mapEmailInCommon($I,"Mailjet Integration", $listOrService, false);
        $this->assignShortCode($I,$fieldMapping,'Mailjet Services');

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchMailJetData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Mailjet is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $apiUrl = 'https://api.mailjet.com/v3/REST/contact';
        $apiKey = getenv('MAILJET_API_KEY');
        $apiSecret = getenv('MAILJET_SECRET_KEY');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        foreach ($data['Data'] as $subscriber) {
            if (isset($subscriber['Email']) && $subscriber['Email'] === $searchTerm) {
                return $subscriber;
            }
        }
        return null;

    }
}