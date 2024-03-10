<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Webhook;
use Tests\Support\Selectors\FieldSelectors;

class IntegrationWebhookCest
{
    use IntegrationHelper, Webhook;
    #[Group('Integration','all')]
    public function _before(AcceptanceTester $I): string
    {
        global $webhookUrl;
        $webhookUrl = self::getWebhook($I);
        $I->loadDotEnvFile(); $I->loginWordpress();
        return $webhookUrl;
    }
    #[Group('Integration')]
    public function test_webhook_push_data(AcceptanceTester $I): void
    {
        global $webhookUrl;
        $pageName = __FUNCTION__.'_'.rand(1,100);

        $this->prepareForm($I,$pageName, ['generalFields' => ['email', 'nameFields']]);
        $this->configureWebhook($I,"Webhooks",$webhookUrl);
        $this->preparePage($I,$pageName);

        $fillAbleDataArr = FieldSelectors::getFieldDataArray(['email', 'first_name', 'last_name']);
        foreach ($fillAbleDataArr as $selector => $value) {
            $I->fillByJS($selector, $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $texts = array(
            $fillAbleDataArr["//input[contains(@id,'email')]"],
            $fillAbleDataArr["//input[contains(@id,'_first_name_')]"],
            $fillAbleDataArr["//input[contains(@id,'_last_name_')]"],
        );
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $this->fetchWebhookData($I,$texts,$webhookUrl);
        }

    }
}
