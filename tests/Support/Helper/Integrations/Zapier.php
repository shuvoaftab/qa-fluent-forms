<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;

trait Zapier
{
    use IntegrationHelper;
    public function configureZapier(AcceptanceTester $I, $integrationName, $webhookUrl): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $this->takeMeToConfigurationPage($I);
        $I->clickOnText("Zapier","Conditional Confirmations");
        $I->clickOnText("Add Webhook","Zapier Integration");
        $I->filledField("(//input[@type='text'])[1]","Zapier");
        $I->filledField("(//input[@type='text'])[2]",$webhookUrl);
        $I->clicked(FluentFormsSelectors::saveButton("Save Feed"));

    }

}