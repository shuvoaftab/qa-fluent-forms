<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class SectionBreakCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_section_break_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $description = $faker->words(3, true);
        $elementClass = $faker->userName();

        $customName = [
            'sectionBreak' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['sectionBreak'],
        ], true, $customName);

        $this->customizeSectionBreak($I, $elementLabel,
            [
                'description' => $description,
            ],
            [
                'elementClass' => $elementClass,
            ]);

        $this->preparePage($I, $pageName);
//        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $elementLabel,
            $description,
        ], $I->cmnt('Check element label, and description'));

        $I->canSeeElement("//div[contains(@class,'$elementClass')]", [], $I->cmnt('Check Section Break Field element class'));

        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }
}
