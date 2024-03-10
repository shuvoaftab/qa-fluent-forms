<?php

namespace Tests\Support\Helper\Integrations;

use Exception;
use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsAddonsSelectors;
use Tests\Support\Selectors\FluentFormsSelectors;

trait IntegrationHelper
{

    /**
     *
     * [!] Use it as a reference of fields name.
     * prepare array like
     * ```
     * ['generalFields' => ['email', 'nameFields', 'phone']]
     * ```
     * * generalFields
     * ```
     * nameFields
     * email
     * simpleText
     * maskInput
     * textArea
     * addressFields
     * countryList
     * numericField
     * dropdown
     * radioField
     * checkBox
     * multipleChoice
     * websiteUrl
     * timeDate
     * imageUpload
     * fileUpload
     * customHtml
     * phone
     * ```
     *
     * * advancedFields
     * ```
     * hiddenField
     * sectionBreak
     * reCaptcha
     * hCapcha
     * turnstile
     * shortCode
     * tnc
     * actionHook
     * formStep
     * rating
     * checkableGrid
     * gdpr
     * passwordField
     * customSubBtn
     * rangeSlider
     * netPromoter
     * chainedSelect
     * colorPicker
     * repeat
     * post_cpt
     * richText
     * save_resume
     * ```
     *
     * * containers
     * ```
     * oneColumn
     * twoColumns
     * threeColumns
     * fourColumns
     * fiveColumns
     * sixColumns
     * ```
     *
     * * paymentFields
     * ```
     * paymentItem
     * subscription
     * customPaymentAmount
     * itmQuantity
     * paymentMethod
     * paymentSummary
     * coupon
     * ```
     * * postFields
     *  ```
     * postTitle
     * postContent
     * postExcerpt
     * featuredImage
     * postUpdate
     * ```
     * * taxonomyFields
     * ```
     * categories
     * tags
     * formats
     */
    public function prepareForm(AcceptanceTester $I, string $formName, array $requiredField, $isCustomName = false, array $fieldMappingArray = null, bool $isCpt=false, bool $isPostType=null)
    {
        global $formID;
        $isDelete = getenv("DELETE_EXISTING_FORM");
        $isDelete === "yes"
            ? $I->deleteExistingForms()
            : null;
        $isCpt
            ? $I->initiateNewCptForm($isPostType)
            : $I->initiateNewForm();

        $isCustomName
            ? $I->createCustomFormField($requiredField, $fieldMappingArray)
            : $I->createFormField($requiredField);

        $formID = $I->grabTextFrom("button[title='Click to Copy']");
        $I->clicked(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
        $I->renameForm($formName);
        $I->seeSuccess('The form is successfully updated.');
        return $formID;
    }

    public function preparePage(AcceptanceTester $I, string $title = null): void
    {
        global $pageUrl;
        $isDelete = getenv("DELETE_EXISTING_PAGE");

        $isDelete === 'yes'
            ? $I->deleteExistingPages()
            : null;

        $I->createNewPage($title);
        $I->amOnUrl($pageUrl);
    }

    public function turnOnIntegration(AcceptanceTester $I, string $integrationName): void
    {
        $I->retry(4, 200);
        $I->amOnPage(FluentFormsAddonsSelectors::integrationsPage);
        $I->filledField("//input[@placeholder='Search Modules']", $integrationName);

        $isEnabled = $I->checkElement("//div[@role='switch' and contains(@class, 'is-checked')]");
        if (!$isEnabled) {
            $I->retryClickWithLeftButton("//div[@role='switch']");
        }
        $gearIcon = $I->checkElement("//div[contains(@class,'ff_card_footer')]//i[contains(@class,'el-icon-setting')]");
        if ($gearIcon) {
            $I->retryClickWithLeftButton("//div[contains(@class,'ff_card_footer')]//i[contains(@class,'el-icon-setting')]");
        }
    }

    public function configureApiSettings(AcceptanceTester $I, $searchKey): void
    {
        self::takeMeToConfigurationPage($I);
        $I->clicked(FluentFormsSelectors::allIntegrations);
        $I->clicked(FluentFormsSelectors::addNewIntegration);
        $I->filledField(FluentFormsSelectors::searchIntegration, $searchKey);
        $I->clicked(FluentFormsSelectors::searchResult);
    }

    public static function takeMeToConfigurationPage(AcceptanceTester $I): void
    {
        $I->amOnPage(FluentFormsSelectors::fFormPage);
        $I->waitForElement(FluentFormsSelectors::mouseHoverMenu, 10);
        $I->moveMouseOver(FluentFormsSelectors::mouseHoverMenu);
        $I->clicked(FluentFormsSelectors::formSettings);
    }

    public function mapEmailInCommon(AcceptanceTester $I, string $feedName, array $listOrService = null,bool $emailField= true): void
    {
        $I->waitForElementClickable(FluentFormsSelectors::integrationFeed, 20);
        $I->filledField(FluentFormsSelectors::feedName, $feedName);
        if ($listOrService) {
            foreach ($listOrService as $key => $value) {
                $I->retryClicked(FluentFormsSelectors::dropdown($key));
                try {
                    $I->clickOnExactText($value, $key);
                } catch (Exception $e) {
                    $I->retryClickOnText($value, $key);
                }
            }
        }
        if ($emailField){
            $I->clicked(FluentFormsSelectors::mapEmailDropdown);
            $I->clicked(FluentFormsSelectors::mapEmail);
        }
    }

    /**
     * Arr ex; 'Name'=>'{inputs.names}'
     *
     */
//    public function mapCommonFieldsWithLabel(AcceptanceTester $I, $fields, $actionText): void
//    {
//        foreach ($fields as $field => $value) {
//            $I->fillField(FluentFormsSelectors::commonFields($field, $actionText), $value);
//        }
//    }

//    public function assignShortCode(AcceptanceTester $I, array $fieldMappingArray): void
//    {
//        foreach ($fieldMappingArray as $field => $labels) {
//            if (is_array($labels)) {
//                foreach ($labels as $label) {
//                    $I->clicked(FluentFormsSelectors::shortcodeDropdown($label));
//                    try {
//                        $I->clickOnExactText($label, $label);  // Using the field label as the following text
//                    } catch (\Exception $e) {
//                        $I->clickOnText($label, $label);  // Using the field label as the following text
//                    }
//                    $I->tryToPressKey(FluentFormsSelectors::shortcodeDropdown($label), \Facebook\WebDriver\WebDriverKeys::ESCAPE);
//                }
//            } else {
//                $I->clicked(FluentFormsSelectors::shortcodeDropdown($labels));
//                try {
//                    $I->clickOnExactText($labels, $labels);  // Using the field label as the following text
//                } catch (\Exception $e) {
//                    $I->clickOnText($labels, $labels);  // Using the field label as the following text
//                }
//                $I->tryToPressKey(FluentFormsSelectors::shortcodeDropdown($labels), \Facebook\WebDriver\WebDriverKeys::ESCAPE);
//            }
//        }
//    }
    public function assignShortCode(AcceptanceTester $I, array $fieldMappingArray, $sectionText = 'Map Fields'): void
    {
        foreach ($fieldMappingArray as $field => $label) {
            echo $field . " => " . $label . "\n";
            echo FluentFormsSelectors::shortcodeDropdown($field, $sectionText) . "\n";
            $I->wait(1);
            $I->clicked(FluentFormsSelectors::shortcodeDropdown($field, $sectionText), "Clicking on the $field next to $sectionText");
            try {
                $I->clickOnExactText($label, $field, null,1, "Clicking on exact text $label next to  $field ");  // Using the field label as the following text
            } catch (Exception $e) {
                $I->clickOnText($label, $field, null,1, "Clicking on text $label next to $field");  // Using the field label as the following text
            }
            $I->tryToPressKey(FluentFormsSelectors::shortcodeDropdown($label,$sectionText), WebDriverKeys::ESCAPE);
        }
    }

    public function mapDynamicTag(AcceptanceTester $I, $isDropDown, array $dynamicTagArray = null): void
    {
        global $dynamicTagField;
        $dynamicTagField = 1;
        $dynamicTagValue = 1;
        foreach ($dynamicTagArray as $tag => $value) {
            if ($isDropDown == "yes" and !empty($isDropDown)) {
                $I->clicked(FluentFormsSelectors::setTag($dynamicTagField));
                $I->clickOnText($tag);
            } else {
                $I->fillField(FluentFormsSelectors::dynamicTagField($dynamicTagField), $tag);
            }
            $I->click(FluentFormsSelectors::ifClause($dynamicTagValue));
            $I->clickOnText($value[0]);

            $I->click(FluentFormsSelectors::ifClause($dynamicTagValue + 1));
            $I->clickOnText($value[1]);

            $I->fillField(FluentFormsSelectors::dynamicTagValue($dynamicTagField), $value[2]);
            $I->click(FluentFormsSelectors::addDynamicTagField($dynamicTagField));
            $dynamicTagField++;
            $dynamicTagValue += 2;
        }
        $I->click(FluentFormsSelectors::removeDynamicTagField($dynamicTagField));

    }

//    public function missingFieldCheck(AcceptanceTester $I, array $referenceData, array $remoteData): void
//    {
//        $absentData = array_diff_assoc($referenceData, $remoteData);
//
//        $message = '';
//        if (!empty($absentData)) {
//            foreach ($absentData as $missingField => $value) {
//                $message .= $missingField . ', ';
//            }
//            $I->fail($message . " is not present to the remote.");
//        } else {
//            echo ' Hurray!! Additional data has been sent to remote' . "\n";
//        }
//    }

    public function retryFetchingData(AcceptanceTester $I, $fetchFunction, string $searchTerm, int $retries = 3)
    {
        $expectedRow = null;
        for ($i = 0; $i < $retries; $i++) {
            $expectedRow = $fetchFunction($searchTerm, $I, $expectedRow);
            $data = self::searchData($I, $expectedRow, '/' . preg_quote($searchTerm, '/') . '/');
            if (empty($data)) {
                $I->wait(30, 'API response Taking too long, Trying again...');
            } else {
                break;
            }
        }
        return $expectedRow;
    }

    public static function searchData(AcceptanceTester $I, $data, $searchTerm): ?string
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value) && preg_match($searchTerm, $value)) {
                    return $value;
                } elseif (is_array($value) || is_object($value)) {
                    $result = self::searchData($I, $value, $searchTerm);
                    if ($result !== null) {
                        return $result;
                    }
                }
            }
        }
        return null;
    }


}
