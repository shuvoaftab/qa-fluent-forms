<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class MultipleChoiceCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_multiselect_field(AcceptanceTester $I)
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
        $optionLabel1 = $faker->words(2, true);
        $optionLabel2 = $faker->words(2, true);
        $optionLabel3 = $faker->words(2, true);
        $optionValue1 = $faker->words(3, true);
        $optionValue2 = $faker->words(3, true);
        $optionValue3 = $faker->words(3, true);
        $optionCalcValue1 = $faker->numberBetween(1, 100);
        $optionCalcValue2 = $faker->numberBetween(1, 100);
        $optionCalcValue3 = $faker->numberBetween(1, 100);
        $maxSelection = $faker->numberBetween(1, 3);
        $optionPhoto1 = $faker->imageUrl();
        $optionPhoto2 = $faker->imageUrl();
        $optionPhoto3 = $faker->imageUrl();
        $nameAttribute = $faker->firstName();

        $customName = [
            'multipleChoice' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['multipleChoice'],
        ], true, $customName);

        $this->customizeMultipleChoice($I, $elementLabel,
            [
//                'adminFieldLabel' => $adminFieldLabel,
                'options' => [
                    [
                        'label'=> $optionLabel1,
                        'value' => $optionValue1,
                        'calcValue' => $optionCalcValue1,
//                        'photo' => $faker->imageUrl(),
                    ],
                    [
                        'label'=> $optionLabel2,
                        'value' => $optionValue2,
                        'calcValue' => $optionCalcValue2,
//                        'photo' => $faker->imageUrl(),

                    ],
                    [
                        'label'=> $optionLabel3,
                        'value' => $optionValue3,
                        'calcValue' => $optionCalcValue3,
//                        'photo' => $faker->imageUrl(),

                    ],
                ],
                'shuffleOption' => true,
                'maxSelection' => $maxSelection,
                'requiredMessage' => $requiredMessage,
            ],
            [
                'defaultValue' => $defaultValue,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, required message'));

        $I->canSeeElement("//input[contains(@name,$nameAttribute)]", [], $I->cmnt('Check MultipleChoice name attribute'));
        $I->canSeeElement("//input[contains(@data-name,$nameAttribute)]",[], $I->cmnt('Check MultipleChoice data-name attribute'));
        $I->canSeeElement("//input[contains(@class,$containerClass)]", [], $I->cmnt('Check MultipleChoice container class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check MultipleChoice help message'));
        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink'));

    }
}
