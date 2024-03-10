<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class TimeDateCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_date_time_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $placeholder = $faker->words(3, true);
        $requiredMessage = $faker->words(3, true);
        $validationMessage = $faker->words(3, true);

        $defaultValue = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $prefixLabel = $faker->words(2, true);
        $suffixLabel = $faker->words(3, true);
        $nameAttribute = $faker->firstName();

        $customName = [
            'timeDate' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['timeDate'],
        ], true, $customName);

        $this->customizeWebsiteUrl($I, $elementLabel,
            [
//            'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'dateFormat' => false,
                'requiredMessage' => $requiredMessage,
            ],
            [
//                'defaultValue' => $defaultValue,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'nameAttribute' => $nameAttribute,
//                'advancedDateConfiguration' => false,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label and required message'));

        $I->canSeeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check TimeDate placeholder'));
        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check TimeDate name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check TimeDate name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check TimeDate container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check TimeDate element class'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));
    }
}
