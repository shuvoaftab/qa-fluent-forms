<?php

namespace Tests\Support\Helper;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\GeneralFields;

trait AdvancedFieldCustomizer
{
    public function customizeHiddenField(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickByJS("(//div[contains(@class,'item-actions-wrapper')])[1]");

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
        ];

        $advancedOptionsDefault = [
            'adminFieldLabel' => false,
            'defaultValue' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {

            $advancedOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $advancedOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeSectionBreak(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'description' => false,
        ];

        $advancedOptionsDefault = [
            'elementClass' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        if (isset($basicOperand)) {

            if ($basicOperand['description']) { //description
                $I->waitForElementVisible("//iframe[contains(@id,'wp_editor')]",5);
                $I->switchToIFrame("//iframe[contains(@id,'wp_editor')]");
                $I->filledField("body p:nth-child(1)", $basicOperand['description'], 'Fill As description');
                $I->switchToIFrame();
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeShortCode(
        AcceptanceTester $I,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickByJS("(//div[contains(@class,'item-actions-wrapper')])[1]");

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'shortcode' => false,
        ];

        $advancedOptionsDefault = [
            'elementClass' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        if (isset($basicOperand)) {

            $basicOperand['shortcode'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Shortcode'), $basicOperand['shortcode'], 'Fill As Shortcode')
                : null;
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeTnC(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
//        $I->clickOnExactText($fieldName);
        $I->clickByJS("(//div[contains(@class,'item-actions-wrapper')])[1]");

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'requiredMessage' => false,
            'termsNConditions' => false,
            'showCheckbox' => false,
        ];

        $advancedOptionsDefault = [
            'containerClass' => false,
            'elementClass' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        if (isset($basicOperand)) {

            if ($basicOperand['adminFieldLabel']) { //adminFieldLabel
                $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label');
            }

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            if ($basicOperand['termsNConditions']) { //Terms & Conditions
                $I->waitForElementVisible("//iframe[contains(@id,'wp_editor')]",5);
                $I->switchToIFrame("//iframe[contains(@id,'wp_editor')]");
                $I->filledField("body p:nth-child(1)", $basicOperand['termsNConditions'], 'Fill As Terms & Conditions');
                $I->switchToIFrame();
            }
            if ($basicOperand['showCheckbox']) { //Show Checkbox
                $I->clicked("//label[@class='el-checkbox']", 'Enable checkbox');
            }
        }
    //                                             Advanced options                                                   //

        if (isset($advancedOperand)) {
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;
            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;
            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeActionHook()
    {

    }

    public function customizeFormStep()
    {

    }

    public function customizeRating(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'options' => false,
            'showText' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'helpMessage' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        // adminFieldLabel
        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['options']) { // configure options

                global $removeField;
                $addField = 1;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['options'] as $fieldContents) {

                    $label = $fieldContents['label'] ?? null;
                    $value = $fieldContents['value'] ?? null;

                    $label
                        ? $I->filledField("(//input[@type='text'])[" . ($fieldCounter + 2) . "]", $label, 'Fill As Label')
                        : null;

                    if (isset($value)) {
                        if ($fieldCounter === 1) {
                            $I->clicked("(//span[@class='el-checkbox__inner'])[1]", 'Select Show Values');
                        }
                        $I->filledField("(//input[@type='text'])[" . ($fieldCounter + 3) . "]", $value, 'Fill As Value');
                    }

                    if ($addField >= 5) {
                        $I->clickByJS(FluentFormsSelectors::addField($addField), 'Add Field no '.$addField);
                    }
                    $fieldCounter+=2;
                    $addField++;
                    $removeField += 1;
                }
                $I->clicked(FluentFormsSelectors::removeField($removeField));
            }

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clickByJS(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }

        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeCheckAbleGrid(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
//        print_r($basicOptions);
//        dd($advancedOptions);

        $I->clickOnExactText($fieldName);
        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'fieldType' => false,
            'gridColumns' => false,
            'gridRows' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'containerClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //

        if (isset($basicOperand)) { // adminFieldLabel
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['fieldType'] === 'radio') { // Field Type
                $I->clickByJS("//span[normalize-space()='Radio']", 'Select Field Type ' .$basicOperand['fieldType']);
            }

            if ($basicOperand['gridColumns']) { // configure Columns

                global $removeField;
                $addField = 1;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['gridColumns'] as $fieldContents) {

                    $label = $fieldContents['label'] ?? null;
                    $value = $fieldContents['value'] ?? null;

                    $label
                        ? $I->filledField("(//span[normalize-space()='Grid Columns']/following::input[@type='text'])[" . ($fieldCounter) . "]", $label, 'Fill As Label')
                        : null;

                    if (isset($value)) {
                        if ($fieldCounter === 1) {
                            $I->clicked("(//span[@class='el-checkbox__inner'])[1]", 'Select Show Values of Grid Columns');
                        }
                        $I->filledField("(//span[normalize-space()='Grid Columns']/following::input[@type='text'])[" . ($fieldCounter + 1) . "]", $value, 'Fill As Value');
                    }

                    if ($addField >= 1) {
                        $I->clickByJS("(//span[normalize-space()='Grid Columns']/following::i[contains(@class,'el-icon-plus')])[$addField]", 'Add Field no '.$addField. ' to Grid Columns');
                    }
                    $fieldCounter+=2;
                    $addField++;
                    $removeField += 1;
                }
                $I->clicked(FluentFormsSelectors::removeField($removeField));
            }

            if ($basicOperand['gridRows']) { // configure Columns

                global $removeField;
                $addField = 1;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['gridRows'] as $fieldContents) {

                    $label = $fieldContents['label'] ?? null;
                    $value = $fieldContents['value'] ?? null;

                    $label
                        ? $I->filledField("(//span[normalize-space()='Grid Rows']/following::input[@type='text'])[" . ($fieldCounter) . "]", $label, 'Fill As Label')
                        : null;

                    if (isset($value)) {
                        if ($fieldCounter === 1) {
                            $I->clicked("(//span[@class='el-checkbox__inner'])[2]", 'Select Show Values of Grid Rows');
                        }
                        $I->filledField("(//span[normalize-space()='Grid Rows']/following::input[@type='text'])[" . ($fieldCounter + 1) . "]", $value, 'Fill As Value');
                    }

                    if ($addField >= 1) {
                        $I->clickByJS("(//span[normalize-space()='Grid Rows']/following::i[contains(@class,'el-icon-plus')])[$addField]", 'Add Field no '.$addField. ' to Grid Rows');
                    }
                    $fieldCounter+=2;
                    $addField++;
                    $removeField += 1;
                }
                $I->clicked("(//span[normalize-space()='Grid Rows']/following::i[contains(@class,'el-icon-minus')])[$removeField]", 'Remove Field no '.$removeField);
            }


            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clickByJS(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }

        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeGDPRAgreement(
        AcceptanceTester $I,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
//        $I->clickOnExactText($fieldName);
        $I->clickByJS("(//div[contains(@class,'item-actions-wrapper')])[1]");

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'description' => false,
            'validationMessage' => false,
            'containerClass' => false,
        ];

        $advancedOptionsDefault = [
            'elementClass' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        if (isset($basicOperand)) {

            if ($basicOperand['adminFieldLabel']) { //adminFieldLabel
                $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label');
            }

            if ($basicOperand['description']) { //description
                $I->filledField("//textarea[@class='el-textarea__inner']", $basicOperand['description'], 'Fill As Admin Field Label');
            }

            if ($basicOperand['validationMessage']) { // validation Message
                $I->clicked(GeneralFields::radioSelect('Required Validation Message',2),'Mark Yes from Required because by default it is No');
                $I->filledField(GeneralFields::customizationFields('Required Validation Message'), $basicOperand['validationMessage'], 'Fill As custom Required Message');
            }

            $basicOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $basicOperand['containerClass'], 'Fill As Container Class')
                : null;

        }
//        dd("here");

        //                                             Advanced options                                                   //

        if (isset($advancedOperand)) {
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;
            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizePassword(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'placeholder' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //

        if (isset($basicOperand)) {

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder']
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

//            if ($basicOperand['requiredMessage']) { // Required Message
//                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
//                $I->clickByJS(GeneralFields::radioSelect('Error Message', 2),'Mark custom from Required because by default it is global');
//                $I->filledField(GeneralFields::customizationFields('Required'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
//            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['defaultValue']    // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("//textarea[@class='el-textarea__inner']", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

        }
        $I->clicked(FluentFormsSelectors::saveForm);

    }

    public function customiseRangeSlider(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'defaultValue' => false,
            'minValue' => false,
            'maxValue' => false,
            'step' => false,
            'requiredMessage' => false,

        ];

        $advancedOptionsDefault = [
            'nameAttribute' => false,
            'helpMessage' => false,
            'containerClass' => false,
            'elementClass' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        // adminFieldLabel
        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $basicOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $basicOperand['minValue']   // Min Value
                ? $I->filledField("(//input[@type='number'])[1]", $basicOperand['minValue'], 'Fill As Min Value')
                : null;

            $basicOperand['maxValue']   // Max Value
                ? $I->filledField("(//input[@type='number'])[2]", $basicOperand['maxValue'], 'Fill As Max Value')
                : null;

            $basicOperand['step']      // Step
                ? $I->filledField(GeneralFields::customizationFields('Step'), $basicOperand['step'], 'Fill As Step')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

        }
        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clickByJS(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;


        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeNetPrompterScore(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'requiredMessage' => false,
            'promoterStartText' => false,
            'promoterEndText' => false,
        ];

        $advancedOptionsDefault = [
            'nameAttribute' => false,
            'helpMessage' => false,
            'containerClass' => false,
            'elementClass' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        // adminFieldLabel
        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            $basicOperand['promoterStartText']      // Promoter Start Text
                ? $I->filledField(GeneralFields::customizationFields('Promoter Start Text'), $basicOperand['promoterStartText'], 'Fill as net promoter start text')
                : null;

            $basicOperand['promoterEndText']      // Promoter End Text
                ? $I->filledField(GeneralFields::customizationFields('Promoter End Text'), $basicOperand['promoterEndText'], 'Fill as net promoter end text')
                : null;



        }
        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clickByJS(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

        }
        $I->clicked(FluentFormsSelectors::saveForm);

    }

    // this is getter setter for index and next index for repeat field
    private static int $nextIndex = 1;
    private static int $index = 0;
    public static function getNextIndex(): int {
        return self::$nextIndex;
    }
    public static function getIndex(): int {
        return self::$index;
    }
    public static function incrementNextIndex(): void {
        self::$nextIndex++;
    }
    public static function incrementIndex(): void {
        self::$index++;
    }
    public function customizeRepeatField(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'repeatFieldColumns' => false,
        ];

        $advancedOptionsDefault = [
            'containerClass' => false,
            'nameAttribute' => false,
            'maxRepeatInputs' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }


        //                                           Basic options                                              //
        if (isset($basicOperand) && $basicOperand['adminFieldLabel']) {
            $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label');
        }

        if (isset($basicOperand) && $basicOperand['repeatFieldColumns']) {
            $columnType = $basicOperand['repeatFieldColumns'];

                if (isset($columnType['textField'])){
                    echo AdvancedFieldCustomizer::getIndex();
                    echo AdvancedFieldCustomizer::getNextIndex();
                    $addColumn = AdvancedFieldCustomizer::getIndex() ?? 1;
                    $nextColumn = AdvancedFieldCustomizer::getNextIndex() ?? 1;

                    if(AdvancedFieldCustomizer::getIndex() >= 1){
                        $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-plus')])[$addColumn]", "Add column " .$addColumn);
                    }
                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Expand column '.$nextColumn);
                    $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-arrow-up')])[$nextColumn]",'Expand Field type in column ' . $nextColumn);
                    $I->clickOnExactText('Text Field','Field Type',null,1,"Select field type");

                    $fieldData = [
                        'Label' => $columnType['textField']['label'] ?? false,
                        'Default' => $columnType['textField']['default'] ?? false,
                        'Placeholder' => $columnType['textField']['placeholder'] ?? false,
                        'Custom' => $columnType['textField']['required'] ?? false,
                    ];

                    foreach ($fieldData as $key => $value) {
                        if ($key == "Label") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As textField Label');
                        }
                        if ($key == "Default") {
                            $I->filledField(GeneralFields::indexedDefaultField($nextColumn), $value, 'Fill As textField Default');
                        }
                        if ($key == "Placeholder") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As textField Placeholder');
                        }
                        if ($key == "Custom") {
                            $I->clicked(GeneralFields::isRequire($nextColumn,1));
                            if ($I->checkElement(GeneralFields::errorMessageType($nextColumn,1))){
                                $I->clickByJS(GeneralFields::errorMessageType($nextColumn,1),'Enable custom error message');
                            }
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As custom error message');
                        }
                    }

//                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Collapse column '.$nextColumn);
                    AdvancedFieldCustomizer::incrementNextIndex();
                    AdvancedFieldCustomizer::incrementIndex();
                }

                if (isset($columnType['emailField'])){
                    echo AdvancedFieldCustomizer::getIndex();
                    echo AdvancedFieldCustomizer::getNextIndex();
                    $addColumn = AdvancedFieldCustomizer::getIndex() ?? 1;
                    $nextColumn = AdvancedFieldCustomizer::getNextIndex() ?? 1;

                    if(AdvancedFieldCustomizer::getIndex() >= 1){
                        $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-plus')])[$addColumn]", "Add column $addColumn");
                    }
                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Expand column '.$nextColumn);
                    $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-arrow-up')])[$nextColumn]",'Expand Field type in column '.$nextColumn);
                    $I->clickOnExactText('Email Field','Field Type',null,1,"Select field type");

                    $fieldData = [
                        'Label' => $columnType['emailField']['label'] ?? false,
                        'Default' => $columnType['emailField']['default'] ?? false,
                        'Placeholder' => $columnType['emailField']['placeholder'] ?? false,
                        'Custom' => $columnType['emailField']['required'] ?? false,
                        'Validate Email' => $columnType['emailField']['validateEmail'] ?? false,
                    ];

                    foreach ($fieldData as $key => $value) {
                        if ($key == "Label") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn, $key), $value, 'Fill As emailField Label');
                        }
                        if ($key == "Default") {
                            $I->filledField(GeneralFields::indexedDefaultField($nextColumn), $value, 'Fill As emailField Default');
                        }
                        if ($key == "Placeholder") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn, $key), $value, 'Fill As emailField Placeholder');
                        }
                        if ($key == "Custom") {
                            $I->clicked(GeneralFields::isRequire($nextColumn,1));
                            if ($I->checkElement(GeneralFields::errorMessageType($nextColumn,1))){
                                $I->clickByJS(GeneralFields::errorMessageType($nextColumn,1),'Enable emailField custom error message');
                            }
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn, $key), $value, 'Fill As emailField custom error message');
                        }

                        if ($key == "Validate Email") {
                            if ($I->checkElement("(//div[normalize-space()='Validate Email']/following::div[contains(@class, 'is-checked') and @role='switch'])")){
                                $I->clickByJS("(//div[normalize-space()='Validate Email']/following::div[contains(@class, 'is-checked') and @role='switch'])",'Enable custom error message');
                            }
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn, $key), $value, 'Fill As Email Validation Message');
                        }
                    }
//                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Collapse column '.$nextColumn);
                    AdvancedFieldCustomizer::incrementNextIndex();
                    AdvancedFieldCustomizer::incrementIndex();
                }

                if (isset($columnType['numericField'])){
                    echo AdvancedFieldCustomizer::getIndex();
                    echo AdvancedFieldCustomizer::getNextIndex();
                    $addColumn = AdvancedFieldCustomizer::getIndex() ?? 1;
                    $nextColumn = AdvancedFieldCustomizer::getNextIndex() ?? 1;

                    if(AdvancedFieldCustomizer::getIndex() >= 1){
                        $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-plus')])[$addColumn]", "Add column $addColumn");
                    }
                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Expand column '.$nextColumn);
                    $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-arrow-up')])[$nextColumn]",'Expand Field type in column '.$nextColumn);
                    $I->clickOnExactText('Numeric Field','Field Type',null,1,"Select field type");

                    $fieldData = [
                        'Label' => $columnType['numericField']['label'] ?? false,
                        'Default' => $columnType['numericField']['default'] ?? false,
                        'Placeholder' => $columnType['numericField']['placeholder'] ?? false,
                        'Custom' => $columnType['numericField']['required'] ?? false,
                    ];

                    foreach ($fieldData as $key => $value) {
                        if ($key == "Label") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As numericField Label');
                        }
                        if ($key == "Default") {
                            $I->filledField(GeneralFields::indexedDefaultField($nextColumn), $value, 'Fill As numericField Default');
                        }
                        if ($key == "Placeholder") {
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As numericField Placeholder');
                        }
                        if ($key == "Custom") {
                            $I->clicked(GeneralFields::isRequire($nextColumn,1));
                            if ($I->checkElement(GeneralFields::errorMessageType($nextColumn,1))){
                                $I->clickByJS(GeneralFields::errorMessageType($nextColumn,1),'Enable numericField custom error message');
                            }
                            $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As numericField custom error message');
                        }
                    }
//                    $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Collapse column '.$nextColumn);
                    AdvancedFieldCustomizer::incrementNextIndex();
                    AdvancedFieldCustomizer::incrementIndex();
                }

            if (isset($columnType['selectField'])){
                echo AdvancedFieldCustomizer::getIndex();
                echo AdvancedFieldCustomizer::getNextIndex();
                $addColumn = AdvancedFieldCustomizer::getIndex() ?? 1;
                $nextColumn = AdvancedFieldCustomizer::getNextIndex() ?? 1;
                if(AdvancedFieldCustomizer::getIndex() >= 1){
                    $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-plus')])[$addColumn]", "Add column $addColumn");
                }
                $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Expand column '.$nextColumn);
                $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-arrow-up')])[$nextColumn]",'Expand Field type in column '.$nextColumn);
                $I->clickOnExactText('Select Field','Field Type',null,1,"Select field type");

                $fieldData = [
                    'Label' => $columnType['selectField']['label'] ?? false,
                    'Placeholder' => $columnType['selectField']['placeholder'] ?? false,
                    'Options' => $columnType['selectField']['options'] ?? null,
                    'Custom' => $columnType['selectField']['required'] ?? false,
                ];

                foreach ($fieldData as $labelName => $labelValue) {
                    if ($labelName == "Label") {
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$labelName), $labelValue, 'Fill As selectField Label');
                    }

                    if ($labelName == "Placeholder") {
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$labelName), $labelValue, 'Fill As selectField Placeholder');
                    }

                    if ($labelName == "Options") {
                        global $removeField;
                        $removeField = 1;
                        $fieldCounter = 1;

                        foreach ($labelValue as $fieldContents) {

                            $value = $fieldContents['value'];
                            $label = $fieldContents['label'];
                            $calcValue = $fieldContents['calcValue'];

                            $label
                                ? $I->filledField("(//input[@placeholder='label'])[$fieldCounter]", $label, 'Fill As Label')
                                : null;

                            if (isset($value)) {
                                if ($fieldCounter === 1) {
                                    $I->clicked("(//span[@class='el-checkbox__inner'])[1]", 'Select Show Values');
                                }
                                $I->filledField("(//input[@placeholder='value'])[$fieldCounter]", $value, 'Fill As Value');
                            }
                            if (isset($calcValue)) {
                                if ($fieldCounter === 1) {
                                    $I->clicked("(//span[@class='el-checkbox__inner'])[2]", 'Select Calc Values');
                                }
                                $I->filledField("(//input[@placeholder='calc value'])[$fieldCounter]", $calcValue, 'Fill As calc Value');
                            }

                            if ($fieldCounter >= 2) {
                                $I->clickByJS(GeneralFields::addFieldInSection($nextColumn, $fieldCounter), 'Add Field');
                            }
                            $fieldCounter++;
                            $removeField += 1;
                        }
                        $I->clicked(GeneralFields::removeFieldInSection($nextColumn, $removeField));
                    }

                    if ($labelName == "Custom") {
                        $I->clicked(GeneralFields::isRequire($nextColumn,1));
                        if ($I->checkElement(GeneralFields::errorMessageType($nextColumn,1))){
                            $I->clickByJS(GeneralFields::errorMessageType($nextColumn,1),'Enable selectField custom error message');
                        }
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$labelName), $labelValue, 'Fill As selectField custom error message');
                    }
                }

//                $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Collapse column '.$nextColumn);
                AdvancedFieldCustomizer::incrementNextIndex();
                AdvancedFieldCustomizer::incrementIndex();
            }

            if (isset($columnType['maskInputField'])){
                echo AdvancedFieldCustomizer::getIndex();
                echo AdvancedFieldCustomizer::getNextIndex();
                $addColumn = AdvancedFieldCustomizer::getIndex() ?? 1;
                $nextColumn = AdvancedFieldCustomizer::getNextIndex() ?? 1;
                if(AdvancedFieldCustomizer::getIndex() >= 1){
                    $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-plus')])[$addColumn]", "Add column $addColumn");
                }
                $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Expand column '.$nextColumn);
                $I->clicked("(//span[@class='ff-repeater-setting-label']/following::i[contains(@class,'el-icon-arrow-up')])[$nextColumn]",'Expand Field type in column '.$nextColumn);
                $I->clickOnExactText('Input Mask Field','Field Type',null,1,"Select field type");

                $fieldData = [
                    'Label' => $columnType['maskInputField']['label'] ?? false,
                    'Default' => $columnType['maskInputField']['default'] ?? false,
                    'Placeholder' => $columnType['maskInputField']['placeholder'] ?? false,
                    'Mask Input' => $columnType['maskInputField']['maskInput'] ?? false,
                    'Custom' => $columnType['maskInputField']['required'] ?? false,
                ];

                foreach ($fieldData as $key => $value) {
                    if ($key == "Label") {
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As maskInputField Label');
                    }
                    if ($key == "Default") {
                        $I->filledField(GeneralFields::indexedDefaultField($nextColumn), $value, 'Fill As maskInputField Default');
                    }
                    if ($key == "Placeholder") {
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As maskInputField Placeholder');
                    }
                    if ($key == "Mask Input") {
                        $I->clicked("(//span[normalize-space()='Mask Input']/following::i[contains(@class,'el-icon-arrow-up')])", 'Expand Mask Input');
                        $I->clickOnExactText($value,'Mask Input',null,1,"Select Mask Input");
                    }
                    if ($key == "Custom") {
                        $I->clicked(GeneralFields::isRequire($nextColumn,1));
                        if ($I->checkElement(GeneralFields::errorMessageType($nextColumn,1))){
                            $I->clickByJS(GeneralFields::errorMessageType($nextColumn,1),'Enable maskInputField custom error message');
                        }
                        $I->filledField(GeneralFields::sectionWiseFields($nextColumn,$key), $value, 'Fill As maskInputField custom error message');
                    }
                }

//                $I->clicked("(//div[@class='ff-repeater-title'])[$nextColumn]",'Collapse column '.$nextColumn);
                AdvancedFieldCustomizer::incrementNextIndex();
                AdvancedFieldCustomizer::incrementIndex();
            }
        }

        // Label Placement (Hidden Label)
        if ($isHiddenLabel) {
            $I->clicked("(//span[normalize-space()='Hide Label'])[1]");
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->clicked(GeneralFields::advancedOptions);

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            $advancedOperand['maxRepeatInputs'] // Name Attribute
                ? $I->filledField("(//input[@type='number'])[1]", $advancedOperand['maxRepeatInputs'], 'Fill As Max Repeat inputs')
                : null;
        }

        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizePostCptSelection(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {


    }

    public function customizeRichTextField(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'placeholder' => false,
            'rows' => false,
            'columns' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
            'maxLength' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        // adminFieldLabel
        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;
            $basicOperand['rows'] //Rows
                ? $I->filledField(GeneralFields::customizationFields('Rows'), $basicOperand['rows'], 'Fill As Rows')
                : null;

            $basicOperand['columns'] //Columns
                ? $I->filledField(GeneralFields::customizationFields('Columns'), $basicOperand['columns'], 'Fill As Columns')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])[2]", $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])[3]", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            $advancedOperand['maxLength'] // Max Length
                ? $I->filledField("//input[@type='number']", $advancedOperand['maxLength'], 'Fill As Max text Length')
                : null;
        }
        $I->clicked(FluentFormsSelectors::saveForm);

    }

    public function customizeColorPickerField(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'adminFieldLabel' => false,
            'placeholder' => false,
            'defaultValue' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'nameAttribute' => false,
            'helpMessage' => false,
            'containerClass' => false,
            'elementClass' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //

        if (isset($basicOperand)) {

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;
            $basicOperand['placeholder']
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;
            $basicOperand['defaultValue']    // Default Value
                ? $I->filledField(GeneralFields::defaultField, $basicOperand['defaultValue'], 'Fill As Default Value')
                : null;
            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("//textarea[@class='el-textarea__inner']", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;
            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;
            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;
        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }



}