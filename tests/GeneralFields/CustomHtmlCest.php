<?php


namespace Tests\GeneralFields;

use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;

class CustomHtmlCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    public function test_custom_html(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $description = $faker->words(10, true);
        $containerClass = $faker->userName();

        $customName = [
            'customHtml' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['customHtml'],
        ], false, $customName);

        $this->customizeCustomHtml($I, $elementLabel,
            [
                'htmlCode' => $description,
                'containerClass' => $containerClass,
            ],
            [
//                'containerClass' => $containerClass,
            ]);

        $this->preparePage($I, $pageName);
//        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([
            $description,
        ], $I->cmnt('Check description'));

        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check Custom Html Container class'));

        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));

    }
}
