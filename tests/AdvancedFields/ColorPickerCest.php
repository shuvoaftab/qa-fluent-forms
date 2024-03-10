<?php


namespace Tests\AdvancedFields;

use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class ColorPickerCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    public function test_color_picker(AcceptanceTester $I)
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
            'colorPicker' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['colorPicker'],
        ], true, $customName);

        $this->customizeColorPickerField($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
//                'defaultValue' => $defaultValue,
                'requiredMessage' => $requiredMessage,
            ],
            [
                'nameAttribute' => $nameAttribute,
                'helpMessage' => $helpMessage,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,

        ], $I->cmnt('Check element label and required message'));

        $I->canSeeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check Color Picker placeholder'));
        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check Color Picker name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check Color Picker name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Color Picker container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check Color Picker element class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check Color Picker help message'));

        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink') );


    }
}
