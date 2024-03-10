<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class MaskInputCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_mask_input_field(AcceptanceTester $I)
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


        $customName = [
            'maskInput' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['maskInput'],
        ], true, $customName);

        $this->customizeMaskInput($I, $elementLabel,
            [
//            'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'requiredMessage' => $requiredMessage,
            ],
            [
//            'defaultValue' => $defaultValue,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'prefixLabel' => $prefixLabel,
                'suffixLabel' => $suffixLabel,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $prefixLabel,
            $suffixLabel,
            $requiredMessage,

        ], $I->cmnt('Check element label, prefix label, suffix label and required message'));

        $I->seeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check maskinput placeholder'));
        $I->seeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check maskinput name attribute'));
        $I->seeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check maskinput name attribute'));
        $I->seeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check maskinput container class'));
        $I->seeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check maskinput element class'));
        $I->seeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check maskinput help message'));

        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink') );
    }

    #[Group('generalFields')]
    public function test_maskinput_field_with_default_value(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $defaultValue = $faker->words(2, true);

        $customName = [
            'maskInput' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['maskInput'],
        ], true, $customName);

        $this->customizeMaskInput($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
            ],
            [
                'defaultValue' => $defaultValue,
            ]);

        $this->preparePage($I, $pageName);
        $I->seeElement("//input", ['value' => $defaultValue], $I->cmnt('Check maskInput default value'));
        $I->clicked(FieldSelectors::submitButton);
        $I->checkAdminArea([$adminFieldLabel], $I->cmnt('Check maskInput adminfield label'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));
    }
}
