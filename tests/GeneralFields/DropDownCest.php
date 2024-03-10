<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class DropDownCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;

    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_dropdown_field(AcceptanceTester $I)
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
        $nameAttribute = $faker->firstName();


        $customName = [
            'dropdown' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['dropdown'],
        ], true, $customName);

        $this->customizeDropdown($I, $elementLabel,
            [
//                'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'options' => [
                                [
                                'label'=> $optionLabel1,
                                'value' => $optionValue1,
//                                'calcValue' => $optionCalcValue1
                                ],
                                [
                                'label'=> $optionLabel2,
                                'value' => $optionValue2,
//                                'calcValue' => $optionCalcValue2
                                ],
                                [
                                'label'=> $optionLabel3,
                                'value' => $optionValue3,
//                                'calcValue' => $optionCalcValue3
                                ],
                ],
                'shuffleOption' => true,
                'searchableOption' => true,
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
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, required message'));

        $I->canSee($placeholder, null, $I->cmnt('Check DropDown placeholder'));

        $I->canSeeElementInDOM("//div/select[contains(@name,$nameAttribute)]", [], $I->cmnt('Check DropDown name attribute'));
        $I->canSeeElementInDOM("//div/select[contains(@data-name,$nameAttribute)]",[], $I->cmnt('Check DropDown data-name attribute'));
        $I->canSeeElementInDOM("//div[contains(@class,$containerClass)]", [], $I->cmnt('Check DropDown container class'));
        $I->canSeeElementInDOM("//div/select[contains(@class,$elementClass)]", [], $I->cmnt('Check DropDown element class'));
        $I->canSeeElementInDOM("//div", ['data-content' => $helpMessage], $I->cmnt('Check DropDown help message'));
        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink'));
    }
}
