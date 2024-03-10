<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\Integrations\Discord;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationDiscordCest
{
    use Discord, IntegrationHelper, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration','all')]
    public function test_discord_notification(AcceptanceTester $I)
    {
//        $jhcg = $this->fetchDiscordData($I,["hepywuziwa@mailinat.com"]);
//        dd($jhcg);


        $pageName = __FUNCTION__.'_'.rand(1,100);
        $customName=[
            'email' => 'Email Address',
            'nameFields'=>'Name',
            'simpleText'=>'Message',
        ];
        $this->prepareForm($I, $pageName, [
            'generalFields' => ['email','nameFields','simpleText'],
        ],true ,$customName);
        $this->configureDiscord($I, "Discord");

        $this->mapDiscordFields($I);
        $this->preparePage($I,$pageName);

//        $I->amOnPage("test_airtable_push_data_57/");
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
            $remoteData = $this->fetchDiscordData($I, $fakeData['Email Address']);
            print_r($remoteData);
        }

        if (!empty($remoteData)) {
            $I->checkValuesInArray($remoteData, [
                $fakeData['Last Name'],
                $fakeData['First Name'],
                $fakeData['Email Address'],
            ]);
            echo " Hurray.....! Data found in Discord";
        }else{
            $I->fail("Could not fetch data from Discord " . PHP_EOL. $remoteData);
        }



    }
}
