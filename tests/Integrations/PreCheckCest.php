<?php
namespace Tests\Integrations;
use Tests\Support\AcceptanceTester;
use Tests\Support\Helper\Integrations\IntegrationHelper;


class PreCheckCest
{
    use IntegrationHelper;

    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }


    public function check_test(AcceptanceTester $I, $isApi=true, $isNative=false)
    {
        $I->amOnPage("wp-admin/admin.php?page=fluent_forms_transfer#apilogs");
        $I->seeTextCaseInsensitive(["success","test_zapier_push_data_37"],"//tbody/tr[1]");



//        $I->amOnPage("wp-admin/admin.php?page=fluent_forms_all_entries");
//        $I->clicked("(//span[contains(text(),'View')])[1]");
//        if ($isApi)
//        {
//            $I->clicked("(//span[normalize-space()='API Calls'])[1]");
//            $I->waitForElementVisible("(//span[contains(@class,'log_status')])",10);
//            return $I->grabTextFrom("(//div[@class='wpf_entry_details'])[3]");
//
//        }
//        if ($isNative)
//        {
//            $I->waitForElementVisible("(//div[@class='wpf_entry_details'])[3]");
//            return $I->grabTextFrom("(//div[@class='wpf_entry_details'])[3]");
//        }
//        $gg = $I->checkAPICallStatus("Success","(//span[contains(@class,'log_status')])");
//        dd($gg);




    }


}