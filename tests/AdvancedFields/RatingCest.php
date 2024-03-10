<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Faker\Factory;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class RatingCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_rating_field(AcceptanceTester $I)
    {

        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $placeholder = $faker->words(3, true);
        $requiredMessage = $faker->words(2, true);

        $defaultValue = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $optionLabel1 = $faker->words(2, true);
        $optionLabel2 = $faker->words(2, true);
        $optionLabel3 = $faker->words(2, true);
        $optionLabel4 = $faker->words(2, true);
        $optionLabel5 = $faker->words(2, true);
        $optionLabel6 = $faker->words(2, true);
        $optionValue1 = $faker->words(3, true);
        $optionValue2 = $faker->words(3, true);
        $optionValue3 = $faker->words(3, true);
        $optionValue4 = $faker->words(3, true);
        $optionValue5 = $faker->words(3, true);
        $optionValue6 = $faker->words(3, true);
        $nameAttribute = $faker->firstName();

        $customName = [
            'rating' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['rating'],
        ], true, $customName);

        $this->customizeRating($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'options' => [
                    [
                        'label'=> $optionLabel1,
                        'value' => $optionValue1,
                    ],
                    [
                        'label'=> $optionLabel2,
                        'value' => $optionValue2,
                    ],
                    [
                        'label'=> $optionLabel3,
                        'value' => $optionValue3,
                    ],
                    [
                        'label'=> $optionLabel4,
                        'value' => $optionValue4,
                    ],
                    [
                        'label'=> $optionLabel5,
                        'value' => $optionValue5,
                    ],
                    [
                        'label'=> $optionLabel6,
                        'value' => $optionValue6,
                    ],
                ],
                'requiredMessage' => $requiredMessage,
            ],
            [
                'helpMessage' => $helpMessage,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, required message'));

        $I->canSeeElement("//input[contains(@name,$nameAttribute)]", [], $I->cmnt('Check Rating field name attribute'));
        $I->canSeeElement("//input[contains(@data-name,$nameAttribute)]",[], $I->cmnt('Check Rating field data-name attribute'));
        $I->canSeeElement("//input[contains(@class,$containerClass)]", [], $I->cmnt('Check Rating field container class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check Rating field help message'));
        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink'));

        // some problem with this test case


    }
}
