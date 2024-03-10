<?php


namespace Tests\AdvancedFields;

use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class NetPrompterScoreCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    public function test_net_prompter_score(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $promoterEndText = $faker->words(3, true);
        $requiredMessage = $faker->words(2, true);

        $promoterStartText = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $nameAttribute = $faker->firstName();

        $customName = [
            'netPromoter' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['netPromoter'],
        ], true, $customName);

        $this->customizeNetPrompterScore($I, $elementLabel,
            [
                'adminFieldLabel' => $adminFieldLabel,
                'requiredMessage' => $requiredMessage,
                'promoterStartText' => $promoterStartText,
                'promoterEndText' => $promoterEndText,
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
            $promoterStartText,
            $promoterEndText,
        ], $I->cmnt('Check element label, required message'));

        $I->canSeeElement("//td[1]/input[@name='$nameAttribute']", [], $I->cmnt('Check Net Prompter Score name attribute'));
        $I->canSeeElement("//div[@data-content='$helpMessage']", [], $I->cmnt('Check Net Prompter Score help message'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Net Prompter Score container class'));

        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check Net Prompter Score element class'));


        echo $I->cmnt("All test cases went through. ", 'yellow', '', array('blink'));

    }
}
