<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;


class IntegrationInventoryModuleCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;

    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
//        $I->runShellCommand($I,["php vendor/bin/codecept clean"]);
    }

    // tests
    #[Group('Integration','native','all')]
    public function test_inventory_module(AcceptanceTester $I): void
    {
        $pageName = __FUNCTION__.'_'.rand(1,100);

        $this->turnOnIntegration($I, "Inventory Module");
        $customName = [
            'nameFields' => 'Name',
            'email' => 'Email Address',
            'checkBox' => ['T-shirt']
        ];
        $this->prepareForm($I, $pageName, ['generalFields' => ['nameFields','email','checkBox']],
            true, $customName);

        $index = $this->convertToIndexArray($customName);

        $options = ['Small Size','Medium Size', 'Large Size'];
        $this->customizeCheckBox($I, $index[2],
            ['adminFieldLabel' => 'T-shirt Inventory', 'options' => $options,],
            ['inventorySettings' => [1,1,1],]);

        $this->preparePage($I, $pageName);

        $I->restartSession();
        $I->amOnPage('/' . $pageName);
        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }

        foreach ($options as $value) {
            $I->clickOnExactText($value);
        }
        $I->clicked(FieldSelectors::submitButton);
        $I->dontSee("This Item is Stock Out");

        $I->restartSession();
        $I->amOnPage('/' . $pageName);
        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }

        foreach ($options as $value) {
            $I->clickOnExactText($value);
        }
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText(['This Item is Stock Out']);

    }
}
