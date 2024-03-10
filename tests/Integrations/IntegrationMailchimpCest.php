<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use DateTime;
use Exception;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\ShortCodes;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Mailchimp;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationMailchimpCest
{
    use IntegrationHelper, Mailchimp, ShortCodes;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile(); $I->loginWordpress();
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     *
     * @throws Exception
     */
    #[Group('Integration','all')]
    public function test_mailchimp_push_data(AcceptanceTester $I): void
    {
        $pageName = __FUNCTION__.'_'.rand(1,100);

        $listOrService =['Mailchimp List'=>getenv('MAILCHIMP_LIST_NAME')];
        $customName=[
            'email'=>'Email Address',
            'nameFields'=>'Name',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields'],
        ],true ,$customName);
        $this->configureMailchimp($I, "Mailchimp");
        $fieldMapping=[
            'First Name'=>'First Name',
            'Last Name'=>'Last Name',
        ];
        $this->mapMailchimpFields($I,$fieldMapping,$listOrService);

        // Disabling IP Logging inorder to prevent the test from failing for IP log issue
        $I->amOnPage("wp-admin/admin.php?page=fluent_forms_settings#settings");
        $I->toggleOn($I, "Disable IP Logging");
        $I->clicked(FluentFormsSelectors::saveButton("Save Settings"));

        $this->preparePage($I,$pageName);
//        $I->amOnPage('/' . __FUNCTION__)
        $fillAbleDataArr = FieldSelectors::getFieldDataArray(['first_name', 'last_name', 'email']);
        foreach ($fillAbleDataArr as $selector => $value) {
            if ($selector == FieldSelectors::country) {
                $I->selectOption($selector, $value);
            } elseif ($selector == FieldSelectors::dateTime) {
                $dateTime = new DateTime($value);
                $formattedDate = $dateTime->format('d/m');
                $I->filledField($selector, $formattedDate);
            }else {
                $I->filledField($selector, $value);
            }
        }
        $I->clicked(FieldSelectors::submitButton);
        $remoteData = $this->fetchMailchimpData($I,$fillAbleDataArr["//input[contains(@id,'email')]"]);
        if (empty($remoteData)) {
            $I->amOnPage('/' . $pageName);
            $fillAbleDataArr = FieldSelectors::getFieldDataArray(['first_name', 'last_name', 'email']);
            foreach ($fillAbleDataArr as $selector => $value) {
                if ($selector == FieldSelectors::country) {
                    $I->selectOption($selector, $value);
                } elseif ($selector == FieldSelectors::dateTime) {
                    $dateTime = new DateTime($value);
                    $formattedDate = $dateTime->format('d/m');
                    $I->fillByJS($selector, $formattedDate);
                }else {
                    $I->fillByJS($selector, $value);
                }
            }
            $I->clicked(FieldSelectors::submitButton);

            $remoteData = "";
            if ($I->checkSubmissionLog(['success', $pageName])) {
                $remoteData = $this->fetchMailchimpData($I,$fillAbleDataArr["//input[contains(@id,'email')]"]);
                print_r($remoteData);
            }


        }
        if(!empty($remoteData)){
            $I->assertString([
                $fillAbleDataArr["//input[contains(@id,'email')]"] => $remoteData->email_address,
                $fillAbleDataArr["//input[contains(@id,'_first_name_')]"] => $remoteData->merge_fields->FNAME,
                $fillAbleDataArr["//input[contains(@id,'_last_name_')]"] => $remoteData->merge_fields->LNAME,
            ]);
        }else{
            $I->fail("No data found in Mailchimp". PHP_EOL. $remoteData);
        }
    }
}
