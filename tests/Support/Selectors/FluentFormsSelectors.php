<?php

namespace Tests\Support\Selectors;
class FluentFormsSelectors
{
    const fFormPage = '/wp-admin/admin.php?page=fluent_forms';
    const createFirstForm = "(//button[contains(@class,'el-button el-button--primary el-button--large')])[1]";
    const addNewForm = "//button[contains(@class,'el-button el-button--primary')][1]";
    const blankForm = "(//div[contains(@class,'ff_card ff_card_form_action ff_card_shadow_lg hover-zoom')])[1]";
    const cptForm = "(//h6[normalize-space()='Create A Post Form'])[1]";
    const saveForm = "//button[@id='saveFormData']";
    const mouseHoverMenu = "(//td[contains(@class,'el-table__cell')])[2]";
    const deleteBtn = "//a[normalize-space()='Delete']";
    const confirmBtn = "[class='el-popover el-popper']:last-child button:last-child";
    const formSettings = "//span[contains(@class,'ff_edit')]//a[contains(text(),'Settings')]";
    const allIntegrations = "//a[@data-route_key='/all-integrations']";
    const addNewIntegration = "//button[normalize-space()='Add New Integration']";
    const searchIntegration = "//input[@placeholder='Search Integration']";
    const searchResult = "[class$='el-dropdown-menu__item']:nth-child(2)";


    //general fields
    public static function fieldCustomise($fieldNumber): string
    {
        return "(//div[@class='item-actions-wrapper hover-action-middle'])[$fieldNumber]";
    }

    const generalSection = "(//h5[normalize-space()='General Fields'])[1]";
    const generalFields = [
        'nameFields' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Name Fields']",
        'email' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Email']",
        'simpleText' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Simple Text']",
        'maskInput' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Mask Input']",
        'textArea' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Text Area']",
        'addressFields' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Address Fields']",
        'countryList' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Country List']",
        'numericField' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Numeric Field']",
        'dropdown' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Dropdown']",
        'radioField' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Radio Field']",
        'checkBox' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Check Box']",
        'multipleChoice' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Multiple Choice']",
        'websiteUrl' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Website URL']",
        'timeDate' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Time & Date']",
        'imageUpload' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Image Upload']",
        'fileUpload' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='File Upload']",
        'customHtml' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Custom HTML']",
        'phone' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Phone/Mobile']",
    ];

    const advancedSection = "(//h5[normalize-space()='Advanced Fields'])[1]";
    const advancedFields = [
        'hiddenField' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Hidden Field']",
        'sectionBreak' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Section Break']",
        'reCaptcha' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='reCaptcha']",
        'hCapcha' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='hCaptcha']",
        'turnstile' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Turnstile']",
        'shortCode' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Shortcode']",
        'tnc' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Terms & Conditions']",
        'actionHook' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Action Hook']",
        'formStep' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Form Step']",
        'rating' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Ratings']",
        'checkableGrid' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Checkable Grid']",
        'gdpr' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='GDPR Agreement']",
        'passwordField' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Password']",
        'customSubBtn' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Custom Submit Button']",
        'rangeSlider' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Range Slider']",
        'netPromoter' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Net Promoter Score']",
        'chainedSelect' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Chained Select']",
        'colorPicker' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Color Picker']",
        'repeat' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Repeat Field']",
        'post_cpt' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Post/CPT Selection']",
        'richText' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Rich Text Input']",
        'save_resume' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Save & Resume']",

    ];

    public static function selectContainer($containerNumber): string
    {
        return "(//i[contains(text(),'+')])[$containerNumber]";
    }

    const containerSection = "(//h5[normalize-space()='Container'])[1]";

    const containers = [
        'oneColumn' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='One Column Container']",
        'twoColumns' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Two Column Container']",
        'threeColumns' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Three Column Container']",
        'fourColumns' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Four Column Container']",
        'fiveColumns' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Five Column Container']",
        'sixColumns' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Six Column Container']",

    ];
    const paymentSection = "(//h5[normalize-space()='Payment Fields'])[1]";
    const paymentFields = [
        'paymentItem' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Payment Item']",
        'subscription' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Subscription']",
        'customPaymentAmount' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Custom Payment Amount']",
        'itmQuantity' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Item Quantity']",
        'paymentMethod' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Payment Method']",
        'paymentSummary' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Payment Summary']",
        'coupon' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Coupon']",
    ];

    const postSection = "(//h5[normalize-space()='Post Fields'])[1]";
    const postFields = [
        'postTitle' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Post Title']",
        'postContent' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Post Content']",
        'postExcerpt' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Post Excerpt']",
        'featuredImage' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Featured Image']",
        'postUpdate' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Post Update']",
    ];

    const taxonomySection = "(//h5[normalize-space()='Taxonomy Fields'])[1]";
    const taxonomyFields = [
        'categories' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Categories']",
        'tags' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Tags']",
        'formats' => "//div[contains(@class,'vddl-draggable btn-element')]//span[normalize-space()='Formats']",
    ];

    //common
    public static function fillAbleArea($label): string
    {
        return "(//label[normalize-space()='$label']/following::input | //label[normalize-space()='$label']/following::textarea)[1]";
    }
    const mapEmailDropdown = "(//div[@class='el-select']//i[contains(@class,'el-select__caret') or //input[@placeholder='Select a Field']])[1]";
    const mapEmail = "(//span[contains(text(),'Email')])[1]";
//    const saveButton("Save Feed") = "//span[normalize-space()='Save Feed' and //button[contains(@class,'el-button--primary')]]";
    const integrationFeed = "(//div[contains(@class, 'ff_card_head_group')] | //button[@title='Show Video'])[1]";
    const feedName = "//input[@placeholder='Your Feed Name' or @placeholder='Your Feed Title']";
    const SegmentDropDown = "(//i[contains(@class,'el-select__caret el-input__icon el-icon-arrow-up')])[1]";
    const Segment = "(//div[@x-placement='bottom-start']//ul[contains(@class,'el-scrollbar__view el-select-dropdown__list')])[1]";

    public static function saveButton($buttonText): string
    {
        return "//span[normalize-space()='$buttonText' and //button[contains(@class,'el-button--primary')]]";
    }
    public static function commonFields($referenceText, $actionText ): string
    {
        return "(//label[normalize-space()='$referenceText']/following::input[@placeholder='$actionText'] | //textarea[@placeholder='$actionText'])[1]";
    }
    public static function dropdown(string $text, $index=1): string
    {
        return "((//*[@placeholder='$text' or normalize-space()='$text'])/following::i[contains(@class,'el-select__caret')])[{$index}]";
    }
    public static function shortcodeDropdown(string $text, $sectionText = 'Map Fields', $index=1): string
    {
        return "(//label[normalize-space()='{$sectionText}']/following::label[normalize-space()='{$text}']/following::button | //label[normalize-space()='{$sectionText}']/following::label[normalize-space()='{$text}']/following::i)[{$index}]";
    }
    public static function addDynamicTagField($index): string
    {
        return "(//span[normalize-space()='Enable Dynamic Tag Selection' or 
        normalize-space()='Enable Dynamic Tag Input']/following::i[contains(@class,'el-icon-plus')])[{$index}]";
    }
    public static function removeDynamicTagField($index): string
    {
        return "(//span[normalize-space()='Enable Dynamic Tag Selection' or 
        normalize-space()='Enable Dynamic Tag Input']/following::i[contains(@class,'el-icon-minus')])[{$index}]";
    }

    public static function addMappingField($text, $index): string
    {
        return "(//span[normalize-space()='$text'] | //label[normalize-space()='$text'])/following::i[contains(@class,'el-icon-plus')][$index]";
    }
    public static function removeMappingField($text, $index): string
    {
        return "(//span[normalize-space()='$text'] | //label[normalize-space()='$text'])/following::i[contains(@class,'el-icon-minus')][$index]";
    }

    public static function ifClause($index): string
    {
        return "(//span[normalize-space()='Enable Dynamic Tag Selection' or normalize-space()='Enable Dynamic Tag Input']/following::input[@placeholder='Select'])[{$index}]";
    }
    public static function addConditionalField($index): string
    {
        return "(//span[normalize-space()='Enable conditional logic']/following::i[contains(@class,'el-icon-plus')])[{$index}]";
    }
    public static function removeConditionalField($index): string
    {
        return "(//span[normalize-space()='Enable conditional logic']/following::i[contains(@class,'el-icon-minus')])[{$index}]";
    }
    public static function conditionalFieldValue($index): string
    {
        return "(//input[@placeholder='Enter a value'])[{$index}]";
    }
    public static function dynamicTagValue($index): string
    {
        return "(//input[@placeholder='Enter a value'])[{$index}]";
    }
    public static function radioButton($text): string
    {
        return "(//span[normalize-space()='$text']/preceding-sibling::span[contains(@class,'el-checkbox__input')]//span[contains(@class,'el-checkbox__inner')])[1]";
    }
    const enableDynamicTag = "//span[@class='el-checkbox__input']//span[contains(@class,'el-checkbox__inner')]";
    const conditionalLogicUnchecked = "//div[@class='ff-filter-fields-wrap']//span[@class='el-checkbox__inner']";
    const conditionalLogicChecked = "//div[@class='ff-filter-fields-wrap']//label[@class='el-checkbox is-checked']";
    const selectNotificationOption = "//select[contains(@class,'ff-select ff-select-small ml-1 mr-1')]";



    // Mailchimp


    const mailchimpStaticTag = "//label[normalize-space()='Tags']/following::input[@placeholder='Select a Field or Type Custom value']";

    public static function dynamicTagField($index): string
    {
        return "(//input[@placeholder='Tag'])[{$index}]";
    }
    public static function openConditionalFieldLabel($index): string
    {
        return "//span[normalize-space()='Enable conditional logic']/following::input[@placeholder='Select'][{$index}]";
    }
    const mailchimpNote = "//label[normalize-space()='Note']/following::textarea[@placeholder='Select a Field or Type Custom value']";


    // Platformly
    public static function mapField($index): string
    {
        return "(//input[@placeholder='Select a Field or Type Custom value'])[{$index}]";
    }
    public static function addField($index): string
    {
        return "(//i[contains(@class,'el-icon-plus')])[{$index}]";
    }

    public static function removeField($index): string
    {
        return "(//i[contains(@class,'el-icon-minus')])[{$index}]";
    }

    public static function openFieldLabel($index): string
    {
        return "(//input[@placeholder='Select'])[{$index}]";
    }

    public static function jsForFieldLabelFromBottom($index): string
    {
        return "document.querySelector(\"div[x-placement='bottom-start'] li:nth-child($index)\").click();";
    }

    public static function jsForFieldLabelFromTop($index): string
    {
        return "document.querySelector(\"div[x-placement='top-start'] li:nth-child($index)\").click();";
    }

    public static function fieldLabel($index): string
    {
        return "(//input[@placeholder='Field Label'])[{$index}]";
    }
    public static function fieldValue($index): string
    {
        return "(//tbody/tr/td/div[contains(@class,'field_general')]/div/div[contains(@class,'el-input-group--append')]/input[contains(@class,'el-input__inner')])[$index]";
    }

    const contactTag = "//input[contains(@class,'el-select')]";
    const dynamicTagChecked = "//div[@class='ff_field_routing']//span[contains(@class,'is-checked')]";

    public static function setTag($index): string
    {
        return "(//input[@placeholder='Set Tag'])[{$index}]";
    }

    // Google Sheets
    const spreadSheetID = "//input[@placeholder='Spreadsheet ID']";
    const workSheetName = "//input[@placeholder='Worksheet Name']";

    // Trello





}