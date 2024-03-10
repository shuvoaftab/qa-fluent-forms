<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class GDPRCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_gdpr_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $requiredMessage = $faker->words(2, true);
        $description = $faker->paragraph(5, true);

        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $nameAttribute = $faker->firstName();


        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['gdpr'],
        ], false);

        $this->customizeGDPRAgreement($I,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'description' => $description,
                'validationMessage' => $requiredMessage,
                'containerClass' => $containerClass,
            ],
            [
                'elementClass' => $elementClass,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $description,
            $requiredMessage,
        ], $I->cmnt('description, and required message'));

        $I->canSeeElement("//div[contains(@name,$nameAttribute)]", [], $I->cmnt('Check GDPR name attribute'));
        $I->canSeeElement("//div[contains(@data-name,$nameAttribute)]",[], $I->cmnt('Check GDPR data-name attribute'));
        $I->canSeeElement("//input[contains(@class,$containerClass)]", [], $I->cmnt('Check GDPR container class'));
        $I->canSeeElement("//input[contains(@class,$elementClass)]", [], $I->cmnt('Check GDPR element class'));

        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }
}
