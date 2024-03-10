<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;

trait Slack
{
    public function configureSlack(AcceptanceTester $I, string $integrationName, $slackTitle = null, $slackFooterMessage = null)
    {
        $this->turnOnIntegration($I,$integrationName);
        $this->takeMeToConfigurationPage($I);
        $I->clickOnText("Slack","Conditional Confirmations");
        $I->toggleOn($I,"Integrate Slack");

        $I->filledField(FluentFormsSelectors::fillAbleArea('Slack Title'),$slackTitle);
        $I->filledField(FluentFormsSelectors::fillAbleArea('Webhook URL'),getenv('SLACK_WEBHOOK_URL'));
        $I->clicked("(//span[@class='el-checkbox__inner'])[1]");
        $I->filledField(FluentFormsSelectors::fillAbleArea('Slack Footer message'),$slackFooterMessage);
        $I->clicked(FluentFormsSelectors::saveButton("Save Settings"));
    }

    public function mapSlackFields(AcceptanceTester $I, array $fieldMapping, array $listOrService = null)
    {
        // TODO: Implement mapFields() method.
    }

    public function fetchSlackData(AcceptanceTester $I, string $searchTerm)
    {
        for ($i = 0; $i < 8; $i++) {
            $remoteData = $this->fetchData($searchTerm);
            if (empty($remoteData)) {
                $I->wait(30, 'Slack is taking too long to respond. Trying again...');
            } else {
                break;
            }
        }
        return $remoteData;
    }

    public function fetchData(string $searchTerm)
    {
        $token = getenv('SLACK_TOKEN');
        $query = urlencode($searchTerm);
        $apiUrl = "https://slack.com/api/search.messages?query={$query}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

        $data = json_decode($response, true);
        return $data['messages']['matches'];
    }
}