<?php


namespace Tests\GeneralFields;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Selectors\FieldSelectors;

class FileUploadCest
{
    use IntegrationHelper, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('generalFields','all')]
    public function test_file_upload_field(AcceptanceTester $I)
    {
        $pageName = __FUNCTION__ . '_' . rand(1, 100);
        $faker = \Faker\Factory::create();

        $elementLabel = $faker->words(2, true);
        $adminFieldLabel = $faker->words(2, true);
        $buttonText = $faker->words(3, true);
        $requiredMessage = $faker->words(3, true);
        $maxFileSize = $faker->numberBetween(4, 6);
        $maxFileCount = $faker->numberBetween(1, 4);


        $defaultValue = $faker->words(2, true);
        $containerClass = $faker->firstName();
        $elementClass = $faker->userName();
        $helpMessage = $faker->words(4, true);
        $prefixLabel = $faker->words(2, true);
        $suffixLabel = $faker->words(3, true);
        $nameAttribute = $faker->firstName();
        $imageName = $faker->firstName();

        $generatedImage = $I->downloadImage("tests/Support/Data",
            500,500,null,true,false,$imageName,false,'jpeg');

        $customName = [
            'fileUpload' => $elementLabel,
        ];

        $this->prepareForm($I, $pageName, [
            'generalFields' => ['fileUpload'],
        ], true, $customName);

        $this->customizeFileUpload($I, $elementLabel,
            [
                'buttonText' => $buttonText,
                'adminFieldLabel' => $adminFieldLabel,
                'requiredMessage' => $requiredMessage,
                'maxFileSize' => ['unit' => 'KB', 'size' => $maxFileSize,],
                'maxFileCount' => $maxFileCount,
                'allowedFiles' => 'image',
                'fileLocationType' => false,
            ],
            [
                'containerClass' => $containerClass,
                'elementClass' => $elementClass,
                'helpMessage' => $helpMessage,
                'nameAttribute' => $nameAttribute,
            ]);

        $this->preparePage($I, $pageName);

//        $I->attachFile("//input[@name='$nameAttribute']", $generatedImage,'Upload Image');

        $I->clicked(FieldSelectors::submitButton);
        $I->clicked(FieldSelectors::submitButton);

        $I->runShellCommand($I, "rm -rf tests/Support/Data/$generatedImage"); // remove image after uploading

        $I->seeText([
            $elementLabel,
            $requiredMessage,
        ], $I->cmnt('Check element label and required message'));

        $I->canSeeElement("//input", ['name' => $nameAttribute], $I->cmnt('Check FileUploadCest name attribute'));
        $I->canSeeElement("//input", ['data-name' => $nameAttribute], $I->cmnt('Check FileUploadCest name attribute'));
        $I->canSeeElement("//div[contains(@class,'$containerClass')]", [], $I->cmnt('Check FileUploadCest container class'));
        $I->canSeeElement("//input[contains(@class,'$elementClass')]", [], $I->cmnt('Check FileUploadCest element class'));
        echo $I->cmnt("All test cases went through. ", 'yellow','',array('blink'));
        
    }
}
