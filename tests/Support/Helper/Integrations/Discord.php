<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Discord
{
    use IntegrationHelper;
    public function configureDiscord(AcceptanceTester $I, string $integrationName)
    {
        $this->turnOnIntegration($I,$integrationName);
        $this->configureApiSettings($I,"Discord");
    }

    public function mapDiscordFields(AcceptanceTester $I)
    {
        $I->waitForElementClickable(FluentFormsSelectors::integrationFeed, 20);
        $I->fillByJS("(//input[@placeholder='Select a Field or Type Custom value'])[1]", 'Discord Integration');

        $I->filledField(FluentFormsSettingsSelectors::apiField("Webhook Url"), getenv("DISCORD_WEBHOOK"));
        $I->filledField("//textarea[@placeholder='Select a Field or Type Custom value']", '{inputs.email}');
        $I->tryToPressKey("//textarea[@placeholder='Select a Field or Type Custom value']", \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->type("{inputs.names}");
        $I->tryToPressKey("//textarea[@placeholder='Select a Field or Type Custom value']", \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->type("{inputs.input_text}");
        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
    }

    public function fetchDiscordData(AcceptanceTester $I, $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($I, $searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Discord is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(AcceptanceTester $I, $searchTerm)
    {
        $channelId = getenv("DISCORD_CHANNEL_ID");
        $botToken = getenv("DISCORD_BOT_TOKEN");
        $apiUrl = "https://discord.com/api/v10/channels/{$channelId}/messages?limit=1";

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bot ' . $botToken, // Include the bot token in the header
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        try {
            $isPresent = $I->checkValuesInArray($data, $searchTerm);
            if ($isPresent) {
                return $data;
            }
        }catch (\Exception $e){
            return null;
            }

        return false;

    }
}