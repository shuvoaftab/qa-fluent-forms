<?php
namespace Tests\Integrations;

use Codeception\Attribute\Skip;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\GlobalPageSelec;

class FluentFormCest {
    /**
     * @author Ibrahim Sharif
     * @param AcceptanceTester $I
     * @return void
     * This function will run before every test function
     */
    public function _before(AcceptanceTester $I): void {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

//    public function _after(AcceptanceTester $I): void
//    {
//        $I->wpLogout();
//    }

    /**
     * @author Ibrahim Sharif
     * @param AcceptanceTester $I
     * @return void
     * This function will install all the required plugins, skip if already installed
     */
    public function Install_required_plugins(AcceptanceTester $I): void {
        $I->wantTo('Install required plugins');
        $I->amOnPage(GlobalPageSelec::pluginPage);

        if (!$I->tryToSee('Fluent Forms')) {
            $I->installPlugin("fluentform.zip");
        }
        if (!$I->tryToSee('Fluent Forms PDF Generator')) {
            $I->installPlugin("fluentforms-pdf.zip");
        }
        if (!$I->tryToSee('Fluent Forms Pro')) {
            $I->installPlugin("fluentformpro.zip");

            $I->activateFluentFormPro();
        }
        $I->amOnPage(GlobalPageSelec::pluginPage);
        $I->see('Fluent Forms Pro Add On Pack');
        $I->see('Fluent Forms');
        $I->see('Fluent Forms PDF Generator');
    }

    //************************************************* Main test function start here *************************************************//

    //************************************************* Main test function end here *************************************************//

    /**
     * @author Ibrahim Sharif
     * @param AcceptanceTester $I
     * @return void
     * This function will uninstall all the plugins after the end of the test
     */
    #[Skip('Because I do not want to uninstall plugins when test is done')]
    public function UninstallPlugins(AcceptanceTester $I): void {
        $I->wantTo('Clean up plugins');

        $I->amOnPage(GlobalPageSelec::fFormLicensePage);
        $I->removeFluentFormProLicense();

        $I->amOnPage(GlobalPageSelec::pluginPage);
        $I->uninstallPlugin("Fluent Forms Pro Add On Pack");
        $I->uninstallPlugin("FluentForms PDF");
        $I->uninstallPlugin("FluentForm");

    }
}
