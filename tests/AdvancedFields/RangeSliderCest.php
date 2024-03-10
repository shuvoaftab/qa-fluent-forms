<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class RangeSliderCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_range_slider(AcceptanceTester $I)
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
        $minValue = $faker->numberBetween(1, 10);
        $maxValue = $faker->numberBetween(20, 50);
        $steps = $faker->numberBetween(2, 5);

        $customName = [
            'rangeSlider' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['rangeSlider'],
        ], true, $customName);

        $this->customiseRangeSlider($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'defaultValue' => $defaultValue,
                'minValue' => $minValue,
                'maxValue' => $maxValue,
                'step' => $steps,
                'requiredMessage' => $requiredMessage,

//                'numberFormat' => 'US Style with Decimal (EX: 123,456.00)',
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
        ], $I->cmnt('Check element label, required message'));

        $I->canSeeElement("//input[@name='$nameAttribute']", [], $I->cmnt('Check Range Slider name attribute'));
        $I->canSeeElement("//input[@data-name='$nameAttribute']", [], $I->cmnt('Check Range Slider name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Range Slider container class'));
        $I->canSeeElement("//div/div/input[contains(@class,'$elementClass')]", [], $I->cmnt('Check Range Slider element class'));


        $I->canSeeElement("//div/div/input", ['min' => $minValue], $I->cmnt('Check Range Slider min value'));
        $I->canSeeElement("//div,div/input", ['max' => $maxValue], $I->cmnt('Check Range Slider max value'));
        $I->canSeeElement("//div/div/input", ['step' => $steps], $I->cmnt('Check Range Slider step value'));

        echo $I->cmnt("All test cases went through. ", 'yellow', '', array('blink'));


    }
}
