<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class TextAreaCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_textarea_field(AcceptanceTester $I)
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
        $rows = $faker->numberBetween(1, 6);
        $columns = $faker->numberBetween(1, 6);
        $maxLength = $faker->numberBetween(10, 100);



        $customName = [
            'textArea' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['textArea'],
        ], true, $customName);

        $this->customizeTextArea($I, $elementLabel,
            [
//            'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'rows' => $rows,
                'columns' => $columns,
                'requiredMessage' => $requiredMessage,
            ],
            [
//            'defaultValue' => $defaultValue,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'maxLength' => $maxLength,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,

        ], $I->cmnt('Check element label and required message'));

        $I->seeElement("//textarea", ['placeholder' => $placeholder], $I->cmnt('Check textarea placeholder'));
        $I->seeElement("//textarea", ['rows' => $rows, 'cols' => $columns], $I->cmnt('Check textarea rows and columns'));
        $I->seeElement("//textarea", ['maxlength' => $maxLength], $I->cmnt('Check textarea maxlength'));
        $I->seeElement("//textarea", ['name' => $nameAttribute], $I->cmnt('Check textarea name attribute'));
        $I->seeElement("//textarea", ['data-name' => $nameAttribute], $I->cmnt('Check textarea name attribute'));
        $I->seeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check textarea container class'));
        $I->seeElement("//textarea[contains(@class,'$elementClass')]", [], $I->cmnt('Check textarea element class'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }

    public function test_text_area_field_with_default_value(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $defaultValue = $faker->words(2, true);

        $customName = [
            'textArea' => $elementLabel,
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['textArea'],
        ], true, $customName);

        $this->customizeTextArea($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
            ],
            [
                'defaultValue' => $defaultValue,
            ]);

        $this->preparePage($I, $pageName);
        $I->seeElement("//textarea", ['value' => $defaultValue], $I->cmnt('Check textArea default value'));
        $I->clicked(FieldSelectors::submitButton);
        $I->checkAdminArea([$adminFieldLabel], $I->cmnt('Check textArea adminfield label'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }


}
