<?php

namespace Tests\Support\Helper\Integrations;

use GuzzleHttp\Exception\ClientException;
use MailchimpMarketing\ApiClient;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Mailchimp
{
    use IntegrationHelper;
    public function configureMailchimp(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
            $isSaveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);
            if (!$isSaveSettings) // Check if the Mailchimp integration is already configured.
            {
                $I->reloadIfElementNotFound(FluentFormsSettingsSelectors::MailchimpApiKeyField);
                $I->retryFilledField(FluentFormsSettingsSelectors::MailchimpApiKeyField, getenv('MAILCHIMP_API_KEY'));
                $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
                $I->seeSuccess("Success");
            }
            $this->configureApiSettings($I,"Mailchimp");
    }

    public function mapMailchimpFields(AcceptanceTester $I, array $customName, array $listOrService=null): void
    {
        $this->mapEmailInCommon($I,"Mailchimp Integration",$listOrService);
        $this->assignShortCode($I,$customName);

        $I->clicked(FluentFormsSelectors::dropdown("Select Interest Category"));
        $I->clickedOnText(getenv('MAILCHIMP_INTEREST_GROUP_NAME'),'Interest Group');
//        $I->waitForElementClickable(FluentFormsSelectors::dropdown("Select Interest"));
        $I->clickByJS(FluentFormsSelectors::dropdown("Select Interest"));
        $I->retryClickOnText(getenv('MAILCHIMP_INTEREST'),'Interest Group');

//        if ($staticTag=='yes' and !empty($staticTag)) {
//            $I->fillField(FluentFormsSelectors::mailchimpStaticTag, $staticTag);
//        }
//        if ($dynamicTag=='yes' and !empty($dynamicTag)) {
//            $this->mapDynamicTag($I,'yes',$dynamicTag);
//        }
        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess('Integration successfully saved');
        $I->wait(1);
    }

    public function fetchMailchimpData(AcceptanceTester $I, $email)
    {
        $client = new ApiClient();
        $client->setConfig([
            'apiKey' => getenv('MAILCHIMP_API_KEY'),
            'server' => getenv('MAILCHIMP_SERVER_PREFIX')
        ]);

        $response= null;
        $exception = [];
        for ($i=0; $i<6; $i++)
        {
            try {
                $response = $client->lists->getListMember(getenv('MAILCHIMP_AUDIENCE_ID'), hash('md5', $email));
                break;
            } catch (ClientException $e) {
                $exception [] = $e->getMessage();
                    $I->wait(30, 'Mailchimp is taking too long to respond. Trying again...');
            }
        }
        if (count($exception) === 8) {
            $response = null;
        }
        return $response;
    }

}
