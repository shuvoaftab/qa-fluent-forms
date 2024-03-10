<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class EmailFieldCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_email_field(AcceptanceTester $I)
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
            'email' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email'],
        ], true, $customName);

        $this->customizeEmail($I, $elementLabel,
            [
//            'adminFieldLabel' => $adminFieldLabel,
            'placeholder' => $placeholder,
            'requiredMessage' => $requiredMessage,
            'validationMessage' => $validationMessage,
            ],
            [
//            'defaultValue' => $defaultValue,
            'containerClass' => $containerClass,
            'elementClass' => $elementClass,
            'helpMessage' => $helpMessage,
            'duplicateValidationMessage' => $duplicateValidationMessage,
            'prefixLabel' => $prefixLabel,
            'suffixLabel' => $suffixLabel,
            'nameAttribute' => $nameAttribute,
        ]);

        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $prefixLabel,
            $suffixLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label, prefix label, suffix label and required message'));

        $I->canSeeElement("//input", ['placeholder' => $placeholder], $I->cmnt('Check email placeholder'));
        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check email name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check email name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check email container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check email element class'));
        $I->canSeeElement("//div", ['data-content' => $helpMessage], $I->cmnt('Check email help message'));

        // changing attribute type to 'something' to disable browser html tooltip validation
        $I->executeJS("
            var emailInput = document.querySelector('input[type=\"email\"]');
            if (emailInput) {
                emailInput.setAttribute('type', 'something');
            }");
        $I->wait(1);

        $fillableDataArr = [
            $elementLabel => 'firstName',
        ];
        $fakeData = $this->generatedData($fillableDataArr);

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->wait(1);
        $I->clicked(FieldSelectors::submitButton);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $validationMessage,
        ], $I->cmnt('Check email validation message'));

        $I->amOnPage('/' . $pageName);

        $fillableDataArr = [
            $elementLabel => 'email',
        ];
        $fakeData = $this->generatedData($fillableDataArr);

        $sameEmail = null;
        $emailField = null;
        foreach ($fakeData as $selector => $value) {
            $sameEmail = $value;
            $emailField = $selector;
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }

        $I->clicked(FieldSelectors::submitButton);
        $I->wait(1);
        $I->amOnPage('/' . $pageName);

        $I->filledField(FluentFormsSelectors::fillAbleArea($emailField), $sameEmail);

        $I->clicked(FieldSelectors::submitButton);

        $I->seeText([
            $duplicateValidationMessage,
        ], $I->cmnt('Check email duplicate validation message'));

        echo $I->cmnt("All tests went through. ",'yellow','',array('blink') );
    }
    #[Group('generalFields')]
    public function test_email_field_with_default_value(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);

        $defaultValue = $faker->safeEmail();

        $customName = [
            'email' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email'],
        ], true, $customName);

        $this->customizeEmail($I, $elementLabel,
            [
            'adminFieldLabel' => $adminFieldLabel,
            ],
            [
            'defaultValue' => $defaultValue,
        ]);

        $this->preparePage($I, $pageName);
        $I->seeElement("//input", ['value' => $defaultValue]);
        $I->clicked(FieldSelectors::submitButton);
        $I->checkAdminArea([$adminFieldLabel]);
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }



}
