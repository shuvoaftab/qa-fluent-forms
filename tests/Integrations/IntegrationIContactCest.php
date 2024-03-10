<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IContact;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationIContactCest
{
    use IntegrationHelper, IContact, DataGenerator, GeneralFieldCustomizer;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_iContact_push_data(AcceptanceTester $I): void
    {
//        $cjnj= $this->fetchIContactData($I,'qa@wpmanageninja.com');
//        print_r($cjnj);
//        exit();

        $pageName = __FUNCTION__.'_'.rand(1,100);
        $listOrService =['iContact List'=>getenv('ICONTACT_CONTACT_LIST')];
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureIContact($I, "iContact");

        $fieldMapping= $this->buildArrayWithKey($customName);
        $this->mapIContactFields($I,$fieldMapping,$listOrService);
        $this->preparePage($I,$pageName);

        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
//        print_r($fakeData);

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchIContactData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Email Address'],
                $fakeData['First Name'],
                $fakeData['Last Name'],
            ]);
            echo " Hurray.....! Data found in iContact";
        }else{
            $I->fail("Could not fetch data from iContact". PHP_EOL. $remoteData);
        }


//        if (isset($remoteData) and !empty($remoteData)) {
//            $email = $remoteData['email'];
//            $firstName = $remoteData['firstName'];
//            $lastName = $remoteData['lastName'];
//
//            $I->assertString([
//                $fakeData['Email Address'] => $email,
//                $fakeData['First Name'] => $firstName,
//                $fakeData['Last Name'] => $lastName,
//            ]);
//        }else{
//            $I->fail("Data not found");
//        }
    }
}
