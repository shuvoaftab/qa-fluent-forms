<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class CountryListCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_country_list(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $placeholder = $faker->words(3, true);
        $requiredMessage = $faker->words(2, true);
        $validationMessage = $faker->words(4, true);

        $defaultValue = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $duplicateValidationMessage = $faker->words(4, true);
        $prefixLabel = $faker->words(2, true);
        $suffixLabel = $faker->words(3, true);
        $nameAttribute = $faker->firstName();

        $customName = [
            'countryList' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['countryList'],
        ], true, $customName);

        $this->customizeCountryList($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
                'requiredMessage' => $requiredMessage,
                'smartSearch' => true,
            ],
            [
                'nameAttribute' => $nameAttribute,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
//                'defaultValue' => $this->generatedData(['Country' => ['country'=> true]])['Country'],
                'countryList' => false,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, prefix label, suffix label and required message'));

        $I->canSeeElementInDOM("//select",['placeholder' => $placeholder], $I->cmnt('Check addr1 placeholder'));
        $I->canSeeElementInDOM("//select", ['name' => $nameAttribute], $I->cmnt('Check Country List Field name attribute'));
        $I->canSeeElementInDOM("//select", ['data-name' => $nameAttribute], $I->cmnt('Check Country List Field name attribute'));
        $I->canSeeElementInDOM("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Country List Field container class'));
        $I->canSeeElementInDOM("//select[contains(@class,'$elementClass')]", [], $I->cmnt('Check Country List Field element class'));
        $I->canSeeElementInDOM("//div", ['data-content' => $helpMessage], $I->cmnt('Check Country List Field help message'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }
}
