<?php
namespace Tests\Support\Helper\Integrations;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;

trait UserRegistration
{
    public function configureUserRegistration(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $this->configureApiSettings($I,"UserRegistration");
    }

    public function mapUserRegistrationField(AcceptanceTester $I, array $fieldMapping, array $listOrService=null): void
    {
        $this->mapEmailInCommon($I,"User Registration", $listOrService, false);
        $this->assignShortCode($I,$fieldMapping,);
        $I->clicked(FluentFormsSelectors::saveButton("Save Feed"));


    }
}