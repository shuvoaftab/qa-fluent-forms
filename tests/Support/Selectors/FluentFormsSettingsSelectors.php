<?php
namespace Tests\Support\Selectors;

class FluentFormsSettingsSelectors
{
    // common
    public static function apiField(string $followingText, int $index = 1): string
    {
        return "(//label[normalize-space()='{$followingText}']/following::input[contains(@class,'el-input__inner')])[{$index}]";
    }
//    const apiField = "//input[contains(@class,'el-input__inner')]";
    const APISaveButton = "//button[contains(@class,'el-button--primary')]";
    const APIDisconnect = "//button[contains(@class,'el-button--danger')]";


    //Mailchimp
    const MailchimpApiKey ="//label[normalize-space()='Mailchimp API Key']";
    const MailchimpApiKeyField ="//label[normalize-space()='Mailchimp API Key']/following::input[@class='el-input__inner']";


    //Platformly
    const PlatformlyApiKey = "//label[normalize-space()='Platformly API Key']";
    const PlatformlyApiKeyField = "(//input[@placeholder='API Key'])";
    const PlatformlyProjectID = "(//input[@placeholder='Project ID'])";

    //Zoho crm
    const accountUrl = "//label[normalize-space()='Account URL']/following::input[@placeholder='Select']";
    const zohoCrmClientId = "//input[@placeholder='Zoho CRM Client ID']";
    const zohoCrmClientSecret = "//input[@placeholder='Zoho CRM Client Secret']";

    //Google sheet
    const googleSheetAccessCodeField = "//input[@placeholder='Access Code']";
    const getAccessCode = "//a[normalize-space()='Get Google Sheet Access Code']";
    const googleUserEmail = "//input[@id='identifierId']";
    const googleNext = "//span[normalize-space()='Next']";
    const googlePass = "//input[@name='password']";
    const googleContinue = "//span[normalize-space()='Continue']";
    const grabCode = "//textarea[@id='code']";







}
