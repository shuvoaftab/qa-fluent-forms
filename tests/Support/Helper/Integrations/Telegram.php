<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Telegram
{
    use IntegrationHelper;
    public function configureTelegram(AcceptanceTester $I, string $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
        if (!$isSaveSettings) {
            $I->filledField(FluentFormsSettingsSelectors::apiField('Bot Token'), getenv('TELEGRAM_BOT_TOKEN'));
            $I->filledField(FluentFormsSettingsSelectors::apiField('Default Channel/Group Chat ID'), getenv('TELEGRAM_CHANNEL_ID'));
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            $I->seeSuccess("Success");
        }
        $this->configureApiSettings($I,"Telegram");
    }

    public function mapTelegramFields(AcceptanceTester $I): void
    {
        $I->waitForElementClickable(FluentFormsSelectors::integrationFeed, 20);
        $I->fillByJS(FluentFormsSelectors::feedName, 'Telegram Integration');

        $I->filledField("//textarea[@placeholder='Select a Field or Type Custom value']",
            '{inputs.email}
                    {inputs.names}
                    {inputs.input_text}');
        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
    }

    public function fetchTelegramData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Telegram is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $botToken = getenv('TELEGRAM_BOT_TOKEN');
        $chatId = getenv('TELEGRAM_CHANNEL_ID');
        $url = "https://api.telegram.org/bot$botToken/getUpdates?chat_id=$chatId&offset=-2";
//        dd($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);
//        return $data;
        foreach ($data['result'] as $record) {
            if (isset($record['channel_post']['text']) && stripos($record['channel_post']['text'], $searchTerm) !== false) {
                return $record;
            }
        }
        return null; // Return null if no matching record is found
    }
}