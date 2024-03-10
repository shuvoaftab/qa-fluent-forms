<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class MobileFieldCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_mobile_field(AcceptanceTester $I)
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
            'phone' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['phone'],
        ], true, $customName);

        $this->customizePhone($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'placeholder' => $placeholder,
//                'defaultValue' => $defaultValue,
                'requiredMessage' => $requiredMessage,
                'validationMessage' => $validationMessage,
                'autoCountrySelection' => false,
                'defaultCountry' => false,
                'countryList' => false,
            ],
            [
                'nameAttribute' => $nameAttribute,
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
            ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, prefix label, suffix label and required message'));

        $I->canSeeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check Mobile Field placeholder'));
        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check Mobile Field name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check Mobile Field name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Mobile Field container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check Mobile Field element class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check Mobile Field help message'));
        $I->canSeeElement("//input", ['type' => 'tel'], $I->cmnt('Check Mobile Field type'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));
    }
}
