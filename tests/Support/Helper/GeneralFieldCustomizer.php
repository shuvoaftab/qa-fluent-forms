<?php

namespace Tests\Support\Helper;

use PhpParser\Node\Expr\Exit_;
use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\GeneralFields;

trait GeneralFieldCustomizer
{
    function convertToIndexArray($customName): array
    {
        $indexArray = [];
        foreach ($customName as $key => $value) {
            if (is_array($value)) {
                $indexArray = array_merge($indexArray, $value);
            } else {
                $indexArray[] = $value;
            }
        }
        return $indexArray;
    }
//    public function buildArrayWithKey(array $customName): array
//    {
//        $new = [];
//        foreach ($customName as $key => $value) {
//            if ($key !== 'email') {
//                if (is_array($value)) {
//                    foreach ($value as $item) {
//                        $new[$item] = $item;
//                    }
//                } else {
//                    $new[$value] = $value;
//                }
//            }
//        }
//        return $new;
//    }
    public function buildArrayWithKey(array $customName): array
    {
        $new = [];
        foreach ($customName as $key => $value) {
            if ($key !== 'email') {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $new[$item] = $item;
                    }
                } elseif ($key === 'nameFields') {
                    $new['First Name'] = 'First Name';
                    $new['Last Name'] = 'Last Name';
                } else {
                    $new[$value] = $value;
                }
            }
        }
        return $new;
    }

    public function customizeNameFields
    (AcceptanceTester $I,
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
            'firstName' => false,
            'middleName' => false,
            'lastName' => false
        ];

        $advancedOptionsDefault = [
            'containerClass' => false,
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
        if (isset($basicOperand) && $basicOperand['adminFieldLabel']) {
            $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label');
        }

        $nameFieldLocalFunction = function (AcceptanceTester $I, $whichName, $nameArea,){
            // Name Fields
            if (isset($whichName)) {

                $name = $whichName;

                if ($nameArea == 1){
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[1]", 'To expand First Name field');
                }elseif ($nameArea == 2){
                    $I->clicked("(//span[@class='el-checkbox__inner'])[2]", 'To enable Middle Name field');
                    $I->clickByJS("(//i[contains(@class,'el-icon-caret-bottom')])[2]", 'To expand Middle Name field');
                }elseif ($nameArea == 3){
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[3]", 'To expand Last Name field');
                }
                $fieldData = [
                    'Label' => $name['label'] ?? null,
                    'Default' => $name['default'] ?? null,
                    'Placeholder' => $name['placeholder'] ?? null,
                    'Custom' => $name['required'] ?? null,
                    'Help Message' => $name['helpMessage'] ?? null,
                ];

                foreach ($fieldData as $key => $value) {
                    // Check if "Default" has a value and "Placeholder" is empty, or vice versa.
                    if (($key == 'Default' && isset($fieldData['Placeholder']) && empty($fieldData['Placeholder'])) ||
                        ($key == 'Placeholder' && isset($fieldData['Default']) && empty($fieldData['Default']))) {
                        continue; // Skip this iteration of the loop.
                    }

                    if ($key == "Custom") {
                        $I->clicked(GeneralFields::isRequire($nameArea));
                        if ($I->checkElement("(//div[contains(@class, 'is-checked') and @role='switch'])[1]")){
                            $I->clickByJS("(//div[contains(@class, 'is-checked') and @role='switch'])[1]",'Enable custom error message');
                        }
                    }
                    $I->filledField(GeneralFields::nameFieldSelectors($nameArea, $key), $value ?? "");
                }
            }
        };
        // calling local function, reverse order for scrolling issue
        $nameFieldLocalFunction($I, $basicOperand['lastName'], 3,);
        $nameFieldLocalFunction($I, $basicOperand['middleName'], 2,);
        $nameFieldLocalFunction($I, $basicOperand['firstName'], 1,);

        // Label Placement (Hidden Label)
        if ($isHiddenLabel) {
            $I->clicked("(//span[normalize-space()='Hide Label'])[1]");
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->clicked(GeneralFields::advancedOptions);
            $I->fillField("(//span[normalize-space()='Container Class']/following::input[@type='text'])[1]",
                $advancedOperand['containerClass'] ?? $fieldName);

            $I->filledField("(//span[normalize-space()='Name Attribute']/following::input[@type='text'])[1]",
                $advancedOperand['nameAttribute'] ?? $fieldName);
            }

        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeEmail(
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
            'validationMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'duplicateValidationMessage' => false,
            'prefixLabel' => false,
            'suffixLabel' => false,
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

            $basicOperand['placeholder'] //Placeholder
            ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
            : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

        if ($basicOperand['validationMessage']) { // Validation Message
//            $I->clickByJS(GeneralFields::radioSelect('Validate Email', 1),'Mark custom from Validate Email because by default it is global');
            if ($I->checkElement("(//div[normalize-space()='Validate Email']/following::div[contains(@class, 'is-checked') and @role='switch'])")){
                $I->clickByJS("(//div[normalize-space()='Validate Email']/following::div[contains(@class, 'is-checked') and @role='switch'])",'Enable custom error message');
            }
            $I->filledField(GeneralFields::customizationFields('Validate Email'), $basicOperand['validationMessage'], 'Fill As Email Validation Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['defaultValue'] // Default Value
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

            if ($advancedOperand['duplicateValidationMessage']) { // Duplicate Validation Message
                $I->clicked(GeneralFields::checkboxSelect(),'Select Duplicate Validation Message');
                $I->filledField(GeneralFields::customizationFields('Validation Message for Duplicate'),
                    $advancedOperand['duplicateValidationMessage'], 'Fill As Duplicate Validation Message');
            }

            $advancedOperand['prefixLabel'] // Prefix Label
            ? $I->filledField(GeneralFields::customizationFields('Prefix Label'), $advancedOperand['prefixLabel'], 'Fill As Prefix Label')
            : null;

            $advancedOperand['suffixLabel'] // Suffix Label
            ? $I->filledField(GeneralFields::customizationFields('Suffix Label'), $advancedOperand['suffixLabel'], 'Fill As Suffix Label')
            : null;

            $advancedOperand['nameAttribute'] // Name Attribute
            ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
            : null;

        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeSimpleText(
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
            'prefixLabel' => false,
            'suffixLabel' => false,
            'nameAttribute' => false,
            'maxLength' => false,
            'uniqueValidationMessage' => false,
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

            $advancedOperand['prefixLabel'] // Prefix Label
                ? $I->filledField(GeneralFields::customizationFields('Prefix Label'), $advancedOperand['prefixLabel'], 'Fill As Prefix Label')
                : null;

            $advancedOperand['suffixLabel'] // Suffix Label
                ? $I->filledField(GeneralFields::customizationFields('Suffix Label'), $advancedOperand['suffixLabel'], 'Fill As Suffix Label')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            $advancedOperand['maxLength'] // Max Length
                ? $I->filledField("//input[@type='number']", $advancedOperand['maxLength'], 'Fill As Max text Length')
                : null;

            // Unique Validation Message
            if ($advancedOperand['uniqueValidationMessage']) {
                $I->clicked(GeneralFields::checkboxSelect(),'Validate as Unique');
                $I->filledField(GeneralFields::customizationFields('Validation Message for Duplicate'),
                    $advancedOperand['uniqueValidationMessage'], 'Fill As Unique Validation Message');
            }
        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeMaskInput(
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
            'maskInput' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'prefixLabel' => false,
            'suffixLabel' => false,
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

            $basicOperand['adminFieldLabel']   // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder'] // Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
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

            $advancedOperand['prefixLabel'] // Prefix Label
                ? $I->filledField(GeneralFields::customizationFields('Prefix Label'), $advancedOperand['prefixLabel'], 'Fill As Prefix Label')
                : null;

            $advancedOperand['suffixLabel'] // Suffix Label
                ? $I->filledField(GeneralFields::customizationFields('Suffix Label'), $advancedOperand['suffixLabel'], 'Fill As Suffix Label')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            if (isset($advancedOperand['uniqueValidationMessage'])) { // Unique Validation Message
                $I->clicked(GeneralFields::checkboxSelect(), 'Validate as Unique');
                $I->clickByJS(GeneralFields::radioSelect('Error Message', 2),'Select Required');
                $I->filledField(GeneralFields::customizationFields('Validation Message for Duplicate'), $advancedOperand['uniqueValidationMessage'], 'Fill As Unique Validation Message');
            }
        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeTextArea(
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

    public function customizeAddressFields(
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
            'addressLine1' => false,
            'addressLine2' => false,
            'city' => false,
            'state' => false,
            'zip' => false,
            'country' => false,
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
        if (isset($basicOperand) && $basicOperand['adminFieldLabel']) {
            $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label');
        }

        // this function will be called locally to fill address fields
        $addressFieldLocalFunction = function (AcceptanceTester $I, $whichName, $nameArea) {
            // Address Fields
            if (isset($whichName)) {
                $name = $whichName;

                if ($nameArea == 1){ // address line 1
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[1]", 'To expand Address Line 1 area');
                }elseif ($nameArea == 2){ // address line 2
                    $I->clickByJS("(//i[contains(@class,'el-icon-caret-bottom')])[2]", 'To expand Address Line 2 area');
                }elseif ($nameArea == 3){ // city
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[3]", 'To expand City area');
                }elseif ($nameArea == 4) { // state
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[4]", 'To expand State area');
                }elseif ($nameArea == 5) { // zip
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[5]", 'To expand Zip area');
                }elseif ($nameArea == 6) { // country
                    $I->clicked("(//i[contains(@class,'el-icon-caret-bottom')])[6]", 'To expand Country area');
                }

                $fieldData = [
                    'Label' => $name['label'] ?? false,
                    'Default' => $name['default'] ?? false,
                    'Placeholder' => $name['placeholder'] ?? false,
                    'Help Message' => $name['helpMessage'] ?? false,
                    'Custom' => $name['required'] ?? false,
                ];

                foreach ($fieldData as $key => $value) {
                    // Check if "Default" has a value and "Placeholder" is empty, or vice versa.
//                    if (($key == 'Default' && isset($fieldData['Placeholder']) && empty($fieldData['Placeholder'])) ||
//                        ($key == 'Placeholder' && isset($fieldData['Default']) && empty($fieldData['Default']))) {
//                        continue; // Skip this iteration of the loop.
//                    }
                    if ($key == "Custom") {
                        $I->clicked(GeneralFields::isRequire($nameArea));
                        if ($I->checkElement("(//div[contains(@class, 'is-checked') and @role='switch'])[1]")){
                            $I->clickByJS("(//div[contains(@class, 'is-checked') and @role='switch'])[1]",'Enable custom error message');
                        }
                    }

//                    if ($key == "Error Message") {
//                        $I->clickByJS(GeneralFields::isRequire($nameArea));
//                        $I->clickByJS(GeneralFields::isRequire($nameArea,4));
//                    }

                    if ($nameArea == 6 && $key == 'Default' && !empty($value)){
                        $I->clicked("//input[@id='settings_country_list']",'Expand country list');
                        $I->clickByJS("//span[normalize-space()='$value']");

                    }elseif ($nameArea == 6 && $key == 'Help Message'){
                        continue;
                    }else{
                        if ($value){
                            $I->filledField(GeneralFields::nameFieldSelectors($nameArea, $key), $value);
                        }
                    }
                }
            }
        };

        // calling local function, reverse order for scrolling issue
        $addressFieldLocalFunction($I, $basicOperand['country'], 6,);
        $addressFieldLocalFunction($I, $basicOperand['zip'], 5,);
        $addressFieldLocalFunction($I, $basicOperand['state'], 4,);
        $addressFieldLocalFunction($I, $basicOperand['city'], 3,);
        $addressFieldLocalFunction($I, $basicOperand['addressLine2'], 2,);
        $addressFieldLocalFunction($I, $basicOperand['addressLine1'], 1,);


        // Label Placement (Hidden Label)
        if ($isHiddenLabel) {
            $I->clicked("(//span[normalize-space()='Hide Label'])[1]");
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->clicked(GeneralFields::advancedOptions);

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

    public function customizeCountryList(
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
            'smartSearch' => false,
            'placeholder' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'containerClass' => false,
            'elementClass' => false,
            'defaultValue' => false,
            'countryList' => false,
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

            $basicOperand['smartSearch'] // smartSearch
                ? $I->clicked("//span[@class='checkbox-label']", 'Select Smart Search')
                : null;

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

            if ($advancedOperand['defaultValue']) { // Default Value
                $defaultValue = $advancedOperand['defaultValue'];
                $I->clicked("//input[@id='settings_country_list']",'Expand country list');
                $I->clickByJS("//span[normalize-space()='$defaultValue']");
                }
            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("//textarea[@class='el-textarea__inner']", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeNumericField(
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
            'minValue' => false,
            'maxValue' => false,
            'digits' => false,
            'numberFormat' => false,

        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'step' => false,
            'prefixLabel' => false,
            'suffixLabel' => false,
            'nameAttribute' => false,
            'calculation' => false,
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

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            $basicOperand['minValue']   // Min Value
                ? $I->filledField("(//input[@type='number'])[2]", $basicOperand['minValue'], 'Fill As Min Value')
                : null;

            $basicOperand['maxValue']   // Max Value
                ? $I->filledField("(//input[@type='number'])[3]", $basicOperand['maxValue'], 'Fill As Max Value')
                : null;

            $basicOperand['digits']   // Digits
                ? $I->filledField("(//input[@type='number'])[4]", $basicOperand['digits'], 'Fill As Digits')
                : null;
        }
        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clickByJS(GeneralFields::advancedOptions, 'Expand advanced options');
            $I->wait(2);

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['step']      // Step
                ? $I->filledField(GeneralFields::customizationFields('Step'), $advancedOperand['step'], 'Fill As Step')
                : null;

            $advancedOperand['prefixLabel'] // Prefix Label
                ? $I->filledField(GeneralFields::customizationFields('Prefix Label'), $advancedOperand['prefixLabel'], 'Fill As Prefix Label')
                : null;

            $advancedOperand['suffixLabel'] // Suffix Label
                ? $I->filledField(GeneralFields::customizationFields('Suffix Label'), $advancedOperand['suffixLabel'], 'Fill As Suffix Label')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

        }
        $I->clicked(FluentFormsSelectors::saveForm);
    }

    public function customizeDropdown(
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
            'options' => false,
            'shuffleOption' => false,
            'searchableOption' => false,
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
        // adminFieldLabel
        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

            if ($basicOperand['options']) { // configure options

                global $removeField;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['options'] as $fieldContents) {

                    $value = $fieldContents['value'] ?? null;
                    $label = $fieldContents['label'] ?? null;
                    $calcValue = $fieldContents['calcValue'] ?? null;

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
                        $I->clickByJS(FluentFormsSelectors::addField($fieldCounter), 'Add Field');
                    }
                    $fieldCounter++;
                    $removeField += 1;
                }
            }
            $I->clicked(FluentFormsSelectors::removeField($removeField));

            if ($basicOperand['shuffleOption']) { // Shuffle Option
                $I->clicked("(//span[@class='el-checkbox__inner'])[3]", 'Select Shuffle Option');
            }
            if ($basicOperand['searchableOption']) { // Searchable Option
                $I->clicked("(//span[@class='el-checkbox__inner'])[4]", 'Select Searchable Option');
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

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
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

    public function customizeRadioField(
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
            'shuffleOption' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
            'layout' => false,
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
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['options'] as $fieldContents) {

                    $value = $fieldContents['value'] ?? null;
                    $label = $fieldContents['label'] ?? null;
                    $calcValue = $fieldContents['calcValue'] ?? null;
                    $photo = $fieldContents['photo'] ?? null;

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
//                    if (isset($photo)) {
//                        if ($fieldCounter === 1) {
//                            $I->clicked("(//span[@class='el-checkbox__inner'])[3]", 'Select photo Values');
//                        }
//                        $I->attachFile("", $photo, 'Fill As calc Value');
//                    }

                    if ($fieldCounter >= 2) {
                        $I->clickByJS(FluentFormsSelectors::addField($fieldCounter), 'Add Field');
                    }
                    $fieldCounter++;
                    $removeField += 1;
                }
            }
            $I->clicked(FluentFormsSelectors::removeField($removeField));

            if ($basicOperand['shuffleOption']) { // Shuffle Option
                $I->clicked("(//span[@class='el-checkbox__inner'])[4]", 'Select Shuffle Option');
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

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

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

    /**
     * ```
     * $basicOptionsDefault = [
     * 'adminFieldLabel' => false,
     * 'options' => false
     * ];
     * ```
     *
     * @param AcceptanceTester $I
     * @param $fieldName
     * @param array|null $basicOptions
     * @param array|null $advancedOptions
     * @return void
     */
    public function customizeCheckBox(
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
            'shuffleOption' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
            'layout' => false,
            'inventorySettings' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //

        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']  // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['options']) { // configure options
                global $removeField;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['options'] as $fieldContents) {

                    $value = $fieldContents['value'] ?? null;
                    $label = $fieldContents['label'] ?? null;
                    $calcValue = $fieldContents['calcValue'] ?? null;
                    $photo = $fieldContents['photo'] ?? null;

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
//                    if (isset($photo)) {
//                        if ($fieldCounter === 1) {
//                            $I->clicked("(//span[@class='el-checkbox__inner'])[3]", 'Select photo Values');
//                        }
//                        $I->attachFile("", $photo, 'Fill As calc Value');
//                    }

                    if ($fieldCounter >= 2) {
                        $I->clickByJS(FluentFormsSelectors::addField($fieldCounter), 'Add Field');
                    }
                    $fieldCounter++;
                    $removeField += 1;
                }
            }
            $I->clicked(FluentFormsSelectors::removeField($removeField));

            if ($basicOperand['shuffleOption']) { // Shuffle Option
                $I->clicked("(//span[@class='el-checkbox__inner'])[4]", 'Select Shuffle Option');
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

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            if ($advancedOperand["inventorySettings"]) { // Inventory Settings
                $I->clickByJS("//div[normalize-space()='Inventory Settings']/following::span[normalize-space()='Enable']");
                $stockCounter = 1;
                foreach ($advancedOperand['inventorySettings'] as $value) {
                    $I->filledField("(//input[@type='number'])[$stockCounter]", $value);
                    $stockCounter++;
                }
            }
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeMultipleChoice(
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
            'shuffleOption' => false,
            'maxSelection' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
            'layout' => false,
            'inventorySettings' => false,
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //

        if (isset($basicOperand)) {
            $basicOperand['adminFieldLabel']  // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['options']) { // configure options
                global $removeField;
                $removeField = 1;
                $fieldCounter = 1;

                foreach ($basicOperand['options'] as $fieldContents) {

                    $value = $fieldContents['value'] ?? null;
                    $label = $fieldContents['label'] ?? null;
                    $calcValue = $fieldContents['calcValue'] ?? null;
//                    $photo = $fieldContents['photo'] ?? null;

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
//                    if (isset($photo)) {
//                        if ($fieldCounter === 1) {
//                            $I->clicked("(//span[@class='el-checkbox__inner'])[3]", 'Select photo Values');
//                        }
//                        $I->attachFile("", $photo, 'Fill As calc Value');
//                    }


                    if ($fieldCounter >= 2) {
                        $I->clickByJS(FluentFormsSelectors::addField($fieldCounter), 'Add Field');
                    }
                    $fieldCounter++;
                    $removeField += 1;
                }
            }
            $I->clicked(FluentFormsSelectors::removeField($removeField));

            if ($basicOperand['shuffleOption']) { // Shuffle Option
                $I->clicked("(//span[@class='el-checkbox__inner'])[3]", 'Select Shuffle Option');
            }
            if ($basicOperand['maxSelection']) { // Max Selection
                $I->filledField("//div[@prop='max_selection']//input[@type='number']", $basicOperand['maxSelection'], 'Fill As Max Selection');
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

            $advancedOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $advancedOperand['defaultValue'], 'Fill As Default Value')
                : null;

            $advancedOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $advancedOperand['containerClass'], 'Fill As Container Class')
                : null;

            $advancedOperand['elementClass'] // Element Class
                ? $I->filledField(GeneralFields::customizationFields('Element Class'), $advancedOperand['elementClass'], 'Fill As Element Class')
                : null;

            $advancedOperand['helpMessage'] // Help Message
                ? $I->filledField("(//textarea[@class='el-textarea__inner'])", $advancedOperand['helpMessage'], 'Fill As Help Message')
                : null;

            $advancedOperand['nameAttribute'] // Name Attribute
                ? $I->filledField(GeneralFields::customizationFields('Name Attribute'), $advancedOperand['nameAttribute'], 'Fill As Name Attribute')
                : null;

            if ($advancedOperand["inventorySettings"]) { // Inventory Settings
                $I->clickByJS("//div[normalize-space()='Inventory Settings']/following::span[normalize-space()='Enable']");
                $stockCounter = 1;
                foreach ($advancedOperand['inventorySettings'] as $value) {
                    $I->filledField("(//input[@type='number'])[$stockCounter]", $value);
                    $stockCounter++;
                }
            }
        }
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeWebsiteUrl(
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
            'validationMessage' => false,
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
        // adminFieldLabel
        if (isset($basicOperand)) {

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            if ($basicOperand['validationMessage']) { //validation Message
//                $I->clicked(GeneralFields::radioSelect('Validate URL'),'Select Required');
                $I->clickByJS("(//span[@class='el-radio__inner'])[3]",'Select validate message type');
                if ($I->checkElement("//div[normalize-space()='Validate URL']/following::div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[normalize-space()='Validate URL']/following::div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Validate URL'), $basicOperand['validationMessage'], 'Fill As validate Message');
            }
        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

            $advancedOperand['defaultValue'] // Default Value
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
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeTimeDate(
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
            'dateFormat' => false,
            'requiredMessage' => false,
        ];

        $advancedOptionsDefault = [
            'defaultValue' => false,
            'containerClass' => false,
            'elementClass' => false,
            'helpMessage' => false,
            'nameAttribute' => false,
            'advancedDateConfiguration' => false,
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

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

//            $basicOperand['dateFormat'] //Date Format
//                ? $I->selectOption(GeneralFields::customizationFields('Date Format'), $basicOperand['dateFormat'], 'Select Date Format')
//                : null;

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

            $advancedOperand['defaultValue'] // Default Value
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
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }

    public function customizeImageUpload(
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
            'buttonText' => false,
            'adminFieldLabel' => false,
            'requiredMessage' => false,
            'maxFileSize' => false,
            'maxFileCount' => false,
            'allowedImages' => false,
            'fileLocationType' => false,
        ];

        $advancedOptionsDefault = [
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
        // adminFieldLabel
        if (isset($basicOperand)) {

            $basicOperand['buttonText'] // Button Text
                ? $I->filledField(GeneralFields::customizationFields('Button Text'), $basicOperand['buttonText'], 'Fill As Button Text')
                : null;

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            if ($basicOperand['maxFileSize']) { //Max File Size
                if (is_array($basicOperand['maxFileSize']) && isset($basicOperand['maxFileSize']['unit'])) {
                    $I->fillByJS("//input[@placeholder='Select']", $basicOperand['maxFileSize']['unit'],'Select Unit');
                    $I->filledField("(//input[@type='number'])[1]", $basicOperand['maxFileSize']['size'], 'Fill As Max File Size');
                }else{
                    $I->filledField("(//input[@type='number'])[1]", $basicOperand['maxFileSize'], 'Fill As Max File Size');
                }
            }

            if ($basicOperand['maxFileCount']) { //Max File Count
                $I->filledField("(//input[@type='number'])[2]", $basicOperand['maxFileCount'], 'Fill As Max File Count');
            }

            if ($basicOperand['allowedImages']) { //Allowed Images

                $basicOperand['allowedImages'] === 'JPG'
                    ? $I->clicked("//span[normalize-space()='Allowed Images']/following::span[normalize-space()='JPG']", 'Select JPG')
                    : null;
                $basicOperand['allowedImages'] === 'PNG'
                    ? $I->clicked("//span[normalize-space()='Allowed Images']/following::span[normalize-space()='PNG']",'Select PNG')
                    : null;
                $basicOperand['allowedImages'] === 'GIF'
                    ? $I->clicked("//span[normalize-space()='Allowed Images']/following::span[normalize-space()='GIF']", 'Select GIF')
                    : null;
            }

            if ($basicOperand['fileLocationType']) { //File Location Type
                $I->selectOption(GeneralFields::customizationFields('File Location Type'), $basicOperand['fileLocationType'], 'Select File Location Type');
            }

        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

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
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeFileUpload(
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
            'buttonText' => false,
            'adminFieldLabel' => false,
            'requiredMessage' => false,
            'maxFileSize' => false,
            'maxFileCount' => false,
            'allowedFiles' => false,
            'fileLocationType' => false,
        ];

        $advancedOptionsDefault = [
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
            $basicOperand['buttonText'] // Button Text
                ? $I->filledField(GeneralFields::customizationFields('Button Text'), $basicOperand['buttonText'], 'Fill As Button Text')
                : null;

            $basicOperand['adminFieldLabel'] // adminFieldLabel
                ? $I->filledField(GeneralFields::adminFieldLabel, $basicOperand['adminFieldLabel'], 'Fill As Admin Field Label')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            if ($basicOperand['maxFileSize']) { //Max File Size
                if (is_array($basicOperand['maxFileSize']) && isset($basicOperand['maxFileSize']['unit'])) {
                    $I->fillByJS("//input[@placeholder='Select']", $basicOperand['maxFileSize']['unit'],'Select Unit');
                    $I->filledField("(//input[@type='number'])[1]", $basicOperand['maxFileSize']['size'], 'Fill As Max File Size');
                }else {
                    $I->filledField("(//input[@type='number'])[1]", $basicOperand['maxFileSize'], 'Fill As Max File Size');
                }
            }

            if ($basicOperand['maxFileCount']) { //Max File Count
                $I->filledField("(//input[@type='number'])[2]", $basicOperand['maxFileCount'], 'Fill As Max File Count');
            }

            if ($basicOperand['allowedFiles']) { //Allowed files

                $basicOperand['allowedFiles'] === 'image'
                    ? $I->clicked("//span[normalize-space()='Images (jpg, jpeg, gif, png, bmp)']", 'Select image files')
                    : null;
                $basicOperand['allowedFiles'] === 'audio'
                    ? $I->clicked("//span[contains(text(),'Audio (mp3, wav, ogg, oga')]",'Select audio files')
                    : null;
                $basicOperand['allowedFiles'] === 'video'
                    ? $I->clicked("//span[contains(text(),'Video (avi, divx')]", 'Select video files')
                    : null;
                $basicOperand['allowedFiles'] === 'pdf'
                    ? $I->clicked("//span[normalize-space()='PDF (pdf)']", 'Select PDF files')
                    : null;
                $basicOperand['allowedFiles'] === 'docs'
                    ? $I->clicked("//span[contains(text(),'Docs (doc, ppt, pps')]", 'Select Docs files')
                    : null;
                $basicOperand['allowedFiles'] === 'zip'
                    ? $I->clicked("//span[normalize-space()='Zip Archives (zip, gz)']", 'Select Zip files')
                    : null;
                $basicOperand['allowedFiles'] === 'executableFiles'
                    ? $I->clicked("//span[normalize-space()='Executable Files (exe)']", 'Select Executable Files')
                    : null;
                $basicOperand['allowedFiles'] === 'csv'
                    ? $I->clicked("//span[normalize-space()='CSV (csv)']", 'Select CSV files')
                    : null;
            }

            if ($basicOperand['fileLocationType']) { //File Location Type
                $I->selectOption(GeneralFields::customizationFields('File Location Type'), $basicOperand['fileLocationType'], 'Select File Location Type');
            }

        }

        //                                           Advanced options                                              //

        if (isset($advancedOperand)) {
            $I->scrollTo(GeneralFields::advancedOptions);
            $I->clicked(GeneralFields::advancedOptions,'Expand advanced options');
            $I->wait(2);

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
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizeCustomHtml(
        AcceptanceTester $I,
        $fieldName,
        ?array $basicOptions = null,
        ?array $advancedOptions = null,
        ?bool $isHiddenLabel = false
    ): void
    {
        $I->clickByJS("(//div[contains(@class,'item-actions-wrapper')])[1]");
//        $I->clickOnExactText($fieldName);

        $basicOperand = null;
        $advancedOperand = null;

        $basicOptionsDefault = [
            'htmlCode' => false,
            'containerClass' => false,
        ];

        $advancedOptionsDefault = [
        ];

        if (!is_null($basicOptions)) {
            $basicOperand = array_merge($basicOptionsDefault, $basicOptions);
        }

        if (!is_null($advancedOptions)) {
            $advancedOperand = array_merge($advancedOptionsDefault, $advancedOptions);
        }

        //                                           Basic options                                              //
        if (isset($basicOperand)) {

            if ($basicOperand['htmlCode']) { //description
                $I->waitForElementVisible("//iframe[contains(@id,'wp_editor')]",5);
                $I->switchToIFrame("//iframe[contains(@id,'wp_editor')]");
                $I->filledField("body p:nth-child(1)", $basicOperand['htmlCode'], 'Fill As description');
                $I->switchToIFrame();
            }

            $basicOperand['containerClass'] // Container Class
                ? $I->filledField(GeneralFields::customizationFields('Container Class'), $basicOperand['containerClass'], 'Fill As Container Class')
                : null;
        }

        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');

    }

    public function customizePhone(
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
            'validationMessage' => false,
            'autoCountrySelection' => false,
            'defaultCountry' => false,
            'countryList' => false,
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

            $basicOperand['placeholder'] //Placeholder
                ? $I->filledField(GeneralFields::placeholder, $basicOperand['placeholder'], 'Fill As Placeholder')
                : null;

            $basicOperand['defaultValue'] // Default Value
                ? $I->filledField(GeneralFields::defaultField, $basicOperand['defaultValue'], 'Fill As Default Value')
                : null;

            if ($basicOperand['requiredMessage']) { // Required Message
                $I->clicked(GeneralFields::radioSelect('Required',1),'Mark Yes from Required because by default it is No');
                if ($I->checkElement("//div[contains(@class, 'is-checked') and @role='switch']")){
                    $I->clickByJS("//div[contains(@class, 'is-checked') and @role='switch']",'Enable custom error message');
                }
                $I->filledField(GeneralFields::customizationFields('Custom Error Message'), $basicOperand['requiredMessage'], 'Fill As custom Required Message');
            }

            if ($basicOperand['validationMessage']) { // Validation Message
                $I->clickByJS(GeneralFields::radioSelect('Validate Phone Number', 1),'Mark custom from Validate phone because by default it is global');
                $I->clickByJS(GeneralFields::radioSelect('Validate Phone Number', 4),'Mark custom from Validate phone because by default it is global');
                $I->filledField(GeneralFields::customizationFields('Validate Phone Number'), $basicOperand['validationMessage'], 'Fill As Email Validation Message');
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
        $I->clickByJS(FluentFormsSelectors::saveForm);
        $I->seeSuccess('The form is successfully updated.');
    }



}