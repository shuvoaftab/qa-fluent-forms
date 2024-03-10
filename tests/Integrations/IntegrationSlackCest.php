<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Slack;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationSlackCest
{
    use Slack, IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_slack_push_data(AcceptanceTester $I): void
    {
//        $kjf = $this->fetchSlackData($I,"Dolorem vitae quis et laborum molestiae eos qui.");
//        dd($kjf);

        $pageName = __FUNCTION__.'_'.rand(1,100);

        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
            'simpleText'=>'Message',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email', 'nameFields','simpleText'],
        ],true ,$customName);
        $this->configureSlack($I, "Slack","Fluentform submission notification","Fluentform submission received");
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_slack_push_data_28");
        $fillAbleDataArr = [
            'Email Address'=>'email',
            'First Name'=>'firstName',
            'Last Name'=>'lastName',
            'Message'=> 'sentence',
        ];
        $fakeData = $this->generatedData($fillAbleDataArr);
//        print_r($fakeData);
//        exit();

        foreach ($fakeData as $selector => $value) {
            $I->tryToFilledField(FluentFormsSelectors::fillAbleArea($selector), $value);
        }
        $I->clicked(FieldSelectors::submitButton);

        $remoteData = "";
        if ($I->checkSubmissionLog(['success', $pageName])) {
            $remoteData = $this->fetchSlackData($I, $fakeData['Message']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                    $fakeData['Last Name'],
                    $fakeData['First Name'],
                    $fakeData['Email Address'],
                ]);
            echo " Hurray.....! Data found in slack";
        }else{
            $I->fail("Could not fetch data from slack");
        }
    }
}
