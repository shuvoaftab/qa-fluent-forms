<?php
namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;

trait Platformly
{
    use IntegrationHelper;

    public function mapPlatformlyFields(AcceptanceTester $I, string $actionText =null, string $optionalField=null, array $otherFields=[], array $staticTag=[], array $dynamicTag=[], array $conditionalLogic=[], string $conditionState=null): void
    {
        $this->mapEmailInCommon($I,"Platformly Integration");

        if (isset($optionalField) and !empty($optionalField)){
            $optionalFieldArr = [
                'First Name' => '{inputs.names.first_name}',
                'Last Name' => '{inputs.names.last_name}',
                'Phone Number' => '{inputs.phone}',
            ];
            foreach ($optionalFieldArr as $key => $value) {
                $I->fillByJS(FluentFormsSelectors::commonFields($key,$actionText),$value);
            }
        }
        if (isset($otherFields) and !empty($otherFields))
        {
            $counter = 1;
            foreach ($otherFields as $fieldValuePosition => $fieldValue)
            {
                $I->clicked(FluentFormsSelectors::openFieldLabel($counter));
                try {
                    $I->executeJS(FluentFormsSelectors::jsForFieldLabelFromTop($fieldValuePosition));
                }catch (\Exception $e){
                    $I->executeJS(FluentFormsSelectors::jsForFieldLabelFromBottom($fieldValuePosition));
                    echo $e->getMessage();
                }
                $I->fillField(FluentFormsSelectors::fieldValue($counter), $fieldValue);
                $I->clicked(FluentFormsSelectors::addField($counter));
                $counter++;
            }
        }
        if (isset($staticTag) and !empty($staticTag)){
            $I->clicked(FluentFormsSelectors::contactTag);
            foreach ($staticTag as $tag)
            {
                $I->clickByJS("//span[normalize-space()='$tag']");
            }
        }
        if (isset($dynamicTag) and !empty($dynamicTag))
        {
            $I->clicked(FluentFormsSelectors::enableDynamicTag);
            $this->mapDynamicTag($I,'yes',$dynamicTag);
        }

        if(isset($conditionalLogic) and !empty($conditionalLogic))
        {
            if(!$I->checkElement(FluentFormsSelectors::conditionalLogicChecked))
            {
                $I->clicked(FluentFormsSelectors::conditionalLogicUnchecked);
            }
                if (isset($conditionState) and !empty($conditionState))
                {
                    $I->selectOption(FluentFormsSelectors::selectNotificationOption,$conditionState);
                }
            global $fieldCounter;
            $fieldCounter = 1;
            $labelCounter = 1;
            foreach ($conditionalLogic as $key => $value)
            {
                $I->click(FluentFormsSelectors::openConditionalFieldLabel($labelCounter));
                $I->clickOnText($key);

                $I->click(FluentFormsSelectors::openConditionalFieldLabel($labelCounter+1));
                $I->clickOnText($value[0]);

                $I->fillField(FluentFormsSelectors::conditionalFieldValue($fieldCounter),$value[1]);
                $I->click(FluentFormsSelectors::addConditionalField($fieldCounter));
                $fieldCounter++;
                $labelCounter+=2;
            }
            $I->click(FluentFormsSelectors::removeConditionalField($fieldCounter));
        }

        $I->clickWithLeftButton(FluentFormsSelectors::saveButton("Save Feed"));
        $I->wait(2);
    }

    /**
     *
     * [!] This is the positions of the integrations in the list.
     * Use the position number to turn on the integration.
     *
     * * User Registration or Update = 1
     * * Landing Pages = 2
     * * Quiz Module = 3
     * * Inventory Module = 4
     * * Post/CPT Creation = 5
     * * Webhooks = 6
     * * Zapier = 7
     * * Mailchimp = 8
     * * Campaign Monitor = 9
     * * GetResponse = 10
     * * ActiveCampaign = 11
     * * Platformly = 12
     * * Trello = 13
     * * Drip = 14
     * * Sendinblue = 15
     * * Zoho = 16
     * * iContact = 17
     * * MooSend = 18
     * * SendFox = 19
     * * ConvertKit = 20
     * * Twilio = 21
     * * ClickSend = 22
     * * Constant Contact = 23
     * * HubSpot = 24
     * * Google Sheets = 25
     * * PipeDrive = 26
     * * MailerLite = 27
     * * GitGist = 28
     * * CleverReach = 29
     * * Salesforce = 30
     * * AmoCRM = 31
     * * OnePageCRM = 32
     * * Airtable = 33
     * * Mailjet = 34
     * * Insightly = 35
     * * Mailster = 36
     * * Automizy = 37
     * * Salesflare = 38
     * * Telegram = 39
     * * Discord = 40
     * * Slack = 41
     *
     *
     *
     * @param AcceptanceTester $I
     * @param $integrationName
     * @return void
     */

    public function configurePlatformly(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);

            $saveSettings = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);

            if (!$saveSettings) // Check if the platformly integration is already configured.
            {
                $I->waitForElement(FluentFormsSettingsSelectors::PlatformlyApiKey,10);
                $I->fillField(FluentFormsSettingsSelectors::PlatformlyApiKeyField,getenv('PLATFORMLY_API_KEY'));
                $I->waitForElement(FluentFormsSettingsSelectors::PlatformlyProjectID,5);
                $I->fillField(FluentFormsSettingsSelectors::PlatformlyProjectID,getenv('PLATFORMLY_PROJECT_ID'));
                $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
            }
            $this->configureApiSettings($I,"Platformly");
    }

    public function fetchPlatformlyData(AcceptanceTester $I, $email): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.platform.ly',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'api_key='.getenv('PLATFORMLY_API_KEY').'&action=fetch_contact&value={"email":"' . $email . '"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $remoteData = json_decode($response);
        if (property_exists($remoteData, 'status')) {
            for ($i = 0; $i < 8; $i++) {
                $remoteData = json_decode($response);
                if (property_exists($remoteData, 'status')) {
                    $I->wait(20,'Platformly is taking too long to respond. Trying again...');
                } else {
                    break;
                }
            }
        }
        if (property_exists($remoteData, 'status')) {
            $I->fail('Contact with '.$email.' not found in Platformly');
        }
        return $remoteData;
    }
}

