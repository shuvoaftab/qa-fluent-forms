<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class AddressFieldCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_address_field(AcceptanceTester $I)
    {

        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $addFieldElementLabel = $faker->words(2, true);
        $addFieldAdminFieldLabel = $faker->words(2, true);
        $addr1label = $faker->words(2, true);
        $addr1default = $faker->words(3, true);
        $addr1placeholder = $faker->words(2, true);
        $addr1helpMessage = $faker->words(4, true);
        $addr1errorMessage = $faker->words(4, true);

        $addr2label = $faker->words(2, true);
        $addr2default = $faker->words(3, true);
        $addr2placeholder = $faker->words(2, true);
        $addr2helpMessage = $faker->words(4, true);
        $addr2errorMessage = $faker->words(4, true);

        $citylabel = $faker->words(2, true);
        $citydefault = $faker->words(3, true);
        $cityplaceholder = $faker->words(2, true);
        $cityhelpMessage = $faker->words(4, true);
        $cityerrorMessage = $faker->words(4, true);

        $statelabel = $faker->words(2, true);
        $statedefault = $faker->words(3, true);
        $stateplaceholder = $faker->words(2, true);
        $statehelpMessage = $faker->words(4, true);
        $stateerrorMessage = $faker->words(4, true);

        $ziplabel = $faker->words(2, true);
        $zipdefault = $faker->words(3, true);
        $zipplaceholder = $faker->words(2, true);
        $ziphelpMessage = $faker->words(4, true);
        $ziperrorMessage = $faker->words(4, true);

        $countrylabel = $faker->words(2, true);
        $countrydefault = $this->generatedData(['Country' => ['country'=> true]])['Country'];
        $countryplaceholder = $faker->words(2, true);
        $countryhelpMessage = $faker->words(4, true);
        $countryerrorMessage = $faker->words(4, true);

        $elementClass = $faker->firstNameMale();
        $nameAttribute = $faker->lastName();

        $customName = [
            'addressFields' => $addFieldElementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['addressFields'],
        ], true, $customName);

        $this->customizeAddressFields($I,
            $addFieldElementLabel,
            ['adminFieldLabel' => $addFieldAdminFieldLabel,
                'addressLine1' => [
                    'label' => $addr1label,
//                    'default' => $addr1default,
                    'placeholder' => $addr1placeholder,
                    'helpMessage' => $addr1helpMessage,
                    'required' => $addr1errorMessage,
                ],
                'addressLine2' => [
                    'label' => $addr2label,
//                    'default' => $addr2default,
                    'placeholder' => $addr2placeholder,
                    'helpMessage' => $addr2helpMessage,
                    'required' => $addr2errorMessage,
                ],
                'city' => [
                    'label' => $citylabel,
//                    'default' => $citydefault,
                    'placeholder' => $cityplaceholder,
                    'helpMessage' => $cityhelpMessage,
                    'required' => $cityerrorMessage,
                ],
                'state' => [
                    'label' => $statelabel,
//                    'default' => $statedefault,
                    'placeholder' => $stateplaceholder,
                    'helpMessage' => $statehelpMessage,
                    'required' => $stateerrorMessage,
                ],
                'zip' => [
                    'label' => $ziplabel,
//                    'default' => $zipdefault,
                    'placeholder' => $zipplaceholder,
                    'helpMessage' => $ziphelpMessage,
                    'required' => $ziperrorMessage,
                ],
                'country' => [
                    'label' => $countrylabel,
//                    'default' => $countrydefault,
                    'placeholder' => $countryplaceholder,
                    'helpMessage' => false,
                    'required' => $countryerrorMessage,
                ],
            ],
            [   'elementClass' => $elementClass,
                'nameAttribute' => $nameAttribute,
            ]
        );
        $this->preparePage($I, $pageName);
        $I->clicked(FieldSelectors::submitButton);
        $I->seeText([

            $addr1label,
            $addr1errorMessage,

            $addr2label,
            $addr2errorMessage,

            $citylabel,
            $cityerrorMessage,

            $statelabel,
            $stateerrorMessage,

            $ziplabel,
            $ziperrorMessage,

            $countrylabel,
            $countryerrorMessage,

        ], $I->cmnt('Check label and error message for each fields'));

        $I->canSeeElement("(//input[@name='{$nameAttribute}[address_line_1]'])[1]", ['placeholder' => $addr1placeholder], $I->cmnt('Check addr1 placeholder'));
        $I->canSeeElement("(//input[@name='{$nameAttribute}[address_line_2]'])[1]", ['placeholder' => $addr2placeholder], $I->cmnt('Check addr2 placeholder'));
        $I->canSeeElement("(//input[@name='{$nameAttribute}[city]'])[1]", ['placeholder' => $cityplaceholder], $I->cmnt('Check city placeholder'));
        $I->canSeeElement("(//input[@name='{$nameAttribute}[state]'])[1]", ['placeholder' => $stateplaceholder], $I->cmnt('Check state placeholder'));
        $I->canSeeElement("(//input[@name='{$nameAttribute}[zip]'])[1]", ['placeholder' => $zipplaceholder], $I->cmnt('Check zip placeholder'));
        $I->seeElement("//select", ['placeholder' => $countryplaceholder], $I->cmnt('Check country placeholder'));

        $I->canSeeElement("//div[contains(@class,'$elementClass')]", [], $I->cmnt('Check address field element class'));

        echo $I->cmnt("All test cases went through.",'yellow','', array('blink'));

    }


}
