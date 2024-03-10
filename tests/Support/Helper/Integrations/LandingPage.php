<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;

trait LandingPage
{
    use IntegrationHelper;
    public function configureLandingPage(AcceptanceTester $I, $integrationName)
    {
        global $landingPageUrl;
        $this->turnOnIntegration($I,$integrationName);
        $this->takeMeToConfigurationPage($I);
        $I->clickOnText("Landing Page","Conditional Confirmations");
        $I->clicked(FluentFormsSelectors::radioButton("Enable Form Landing Page Mode"));
        $landingPageUrl = $I->retryGrabTextFrom("//span[contains(@class,'url-bar')]");
        $I->clicked(FluentFormsSelectors::saveButton("Save"));
        return $landingPageUrl;

    }

}