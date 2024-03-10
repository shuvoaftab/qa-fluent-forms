<?php


namespace Tests\AdvancedFields;

use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class RichTextCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    public function test_rich_text_field(AcceptanceTester $I)
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
            'richText' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['richText'],
        ], true, $customName);

        $this->customizeRichTextField($I, $elementLabel,
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
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label and required message'));

        $I->canSeeElementInDOM("//textarea", ['placeholder' => $placeholder], $I->cmnt('Check Rich Text placeholder'));
        $I->canSeeElementInDOM("//textarea", ['rows' => $rows, 'cols' => $columns], $I->cmnt('Check Rich Text rows and columns'));
        $I->canSeeElementInDOM("//textarea", ['maxlength' => $maxLength], $I->cmnt('Check Rich Text maxlength'));
        $I->canSeeElementInDOM("//textarea", ['name' => $nameAttribute], $I->cmnt('Check Rich Text name attribute'));
        $I->canSeeElementInDOM("//textarea", ['data-name' => $nameAttribute], $I->cmnt('Check Rich Text name attribute'));
        $I->canSeeElementInDOM("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Rich Text container class'));
        $I->canSeeElementInDOM("//textarea[contains(@class,'$elementClass')]", [], $I->cmnt('Check Rich Text element class'));

        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));
    }
}
