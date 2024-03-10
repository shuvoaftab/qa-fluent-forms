<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;

trait Webhook
{
    use IntegrationHelper;
    private static function getWebhook(AcceptanceTester $I): string
    {
        $I->amOnUrl("https://webhook.site/");
        $I->waitForElementVisible("(//code)[1]");
        $url = $I->grabTextFrom("(//code)[1]");

        return $url;

    }

    public function configureWebhook(AcceptanceTester $I, $integrationName, $webhookUrl): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $this->takeMeToConfigurationPage($I);
        $I->clickOnText("WebHook","Conditional Confirmations");
        $I->clickOnText("Add New","WebHooks Integration");
        $I->filledField("//input[@placeholder='WebHook Feed Name']","Webhook");
        $I->filledField("//input[@placeholder='WebHook URL']",$webhookUrl);
        $I->clicked(FluentFormsSelectors::saveButton("Save Feed"));

    }
    public function fetchWebhookData(AcceptanceTester $I, array $texts, $webhookUrl): void
    {
        $maxRetries = 8;
        $retryDelay = 30;
        for ($i = 0; $i < $maxRetries; $i++) {
            if ($this->fetchData($I, $texts,$webhookUrl)) {
                echo "Webhook data fetched successfully!";
                return;
            } else {
                $I->wait($retryDelay);
            }
        }
        $I->fail("Failed to fetch webhook data after {$maxRetries} attempts.");
    }
    public function fetchData(AcceptanceTester $I, array $texts, $webhookUrl): bool
    {
        $webhookUrl = preg_replace('/https:\/\/webhook\.site\//', 'https://webhook.site/#!/', $webhookUrl);

        $I->amOnUrl($webhookUrl);
        try {
            $I->seeText($texts);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


}