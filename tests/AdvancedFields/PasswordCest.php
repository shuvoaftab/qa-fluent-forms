<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class PasswordCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_password_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $placeholder = $faker->words(3, true);
        $requiredMessage = $faker->words(2, true);

        $defaultValue = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $prefixLabel = $faker->words(2, true);
        $suffixLabel = $faker->words(3, true);
        $nameAttribute = $faker->firstName();
        $maxLength = $faker->numberBetween(10, 100);
        $uniqueValidationMessage = $faker->words(4, true);


        $customName = [
            'passwordField' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['passwordField'],
        ], true, $customName);

        $this->customizePassword($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'requiredMessage' => $requiredMessage,
            ],
            [
//                'defaultValue' => $defaultValue,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,

        ], $I->cmnt('Check element label and required message'));

        $I->canSeeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check Password placeholder'));
        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check Password name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check Password name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Password container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check Password element class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check Password help message'));

        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink') );


    }
}
