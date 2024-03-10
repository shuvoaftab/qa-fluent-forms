<?php


namespace Tests\AdvancedFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\AdvancedFieldCustomizer;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class HiddenFieldCest
{
    use IntegrationHelper, AdvancedFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('advancedFields','all')]
    public function test_hidden_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);

        $defaultValue = $faker->words(2, true);

        $nameAttribute = $faker->firstName();

        $customName = [
            'hiddenField' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'advancedFields' => ['hiddenField'],
        ], true, $customName);


        $this->customizeHiddenField($I, $elementLabel,
            [],
            [
                'adminFieldLabel' => $adminFieldLabel,
                'defaultValue' => $defaultValue,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);

        $I->canSeeElementInDOM("//input", ['name' => $nameAttribute], $I->cmnt('Check Hidden Field name attribute'));
        $I->canSeeElementInDOM("//input", ['data-name' => $nameAttribute], $I->cmnt('Check Hidden Field name attribute'));
        $I->canSeeElementInDOM("//input", ['value' => $defaultValue], $I->cmnt('Check Hidden Field value'));
        $I->canSeeElementInDOM("//input", ['type' => 'hidden'], $I->cmnt('Check Hidden Field type'));

        echo $I->cmnt("All test cases went through. ",'yellow','',array('blink') );
    }
}
