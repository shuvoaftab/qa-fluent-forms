<?php

namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Codeception\Attribute\Skip;
use Codeception\Example;
use Tests\Support\AcceptanceTester;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\Platformly;
use Tests\Support\Selectors\FieldSelectors;
use Tests\Support\Selectors\FluentFormsAllEntries;
use Tests\Support\Selectors\FluentFormsSelectors;

class IntegrationPlatformlyCest
{
    use IntegrationHelper, Platformly;
    public function _before(AcceptanceTester $I): void
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }
    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     */
    #[Group('Integration','all')]
    public function test_platformly_push_data(AcceptanceTester $I, Example $example): void
    {
        $this->prepareForm($I,__FUNCTION__, ['generalFields' => ['email', 'nameFields', 'phone']]);

        $this->configurePlatformly($I,"Platformly");
        $this->mapPlatformlyFields($I,'Select a Field or Type Custom value','yes', [], [], [], [], '');

        $this->preparePage($I,__FUNCTION__);

        $I->wait(1);
        $I->filledField(FieldSelectors::first_name, ($example['first_name']));
        $I->filledField(FieldSelectors::last_name, ($example['last_name']));
        $I->filledField(FieldSelectors::email, ($example['email']));
        $I->filledField(FieldSelectors::phone, ($example['phone']));
        $I->click(FieldSelectors::submitButton);

//        $remoteData = "";
//        if ($I->checkSubmissionLog(['success', $pageName])) {
//            $remoteData = $this->fetchPlatformlyData($I,$example['email']);
//            print_r($remoteData);
//        }

        $referenceData = [
            'first_name' => $example['first_name'],
            'last_name' => $example['last_name'],
            'email' => $example['email'],
            'phone' => $example['phone'],
        ];

        $absentData = array_diff_assoc($referenceData, (array)$remoteData);

        $message = '';
        if (!empty($absentData)) {
            foreach ($absentData as $missingField => $value) {
                $message .= $missingField . ', ';
            }
            $I->fail($message . " is not present to the remote.");
        } else {
            echo ' Hurray!! Integration test pass. All data has been sent to Platform.ly' . "\n";
        }
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     */
//    #[Group('Integration')]
    public function test_platformly_can_push_other_data(AcceptanceTester $I, Example $example): void
    {
        global $pageUrl;
        global $tags;
        $integrationPositionNumber = 12;
        $tags = ['Non_US', 'Asian'];

        $I->deleteExistingForms();
        $I->initiateNewForm();
        $requiredField = [
            'generalFields' => ['email', 'addressFields'],
        ];
        $I->createFormField($requiredField);
        $I->click(FluentFormsSelectors::saveForm);
        $I->wait(1);
        $I->seeSuccess('Form created successfully.');
        $I->renameForm(__FUNCTION__);
        $I->seeSuccess('Form renamed successfully.');

        $this->configurePlatformly($I,$integrationPositionNumber,);

        $otherFieldsArray = [
            2 => '{inputs.address_1.address_line_1}',
            3 => '{inputs.address_1.address_line_2}',
            4 => '{inputs.address_1.city}',
            5 => '{inputs.address_1.state}',
            6 => '{inputs.address_1.zip}',
        ];
        $this->mapPlatformlyFields($I,'Select a Field or Type Custom value','', $otherFieldsArray, [], [], [], '');
        $I->deleteExistingPages();
        $I->createNewPage(__FUNCTION__);
        $I->wantTo('Fill the form with sample data');
        $I->amOnUrl($pageUrl);

        $I->wait(1);

        $I->fillField(FieldSelectors::email, ($example['email']));
        $I->fillField(FieldSelectors::address_line_1, ($example['address_line_1']));
        $I->fillField(FieldSelectors::address_line_2, ($example['address_line_2']));
        $I->fillField(FieldSelectors::city, ($example['city']));
        $I->fillField(FieldSelectors::state, ($example['state']));
        $I->fillField(FieldSelectors::zip, ($example['zip']));
        $I->selectOption(FieldSelectors::country, ($example['country']));
        $I->click(FieldSelectors::submitButton);

        $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
        if (property_exists($remoteData, 'status')) {
            for ($i = 0; $i < 6; $i++) {
                $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
                if (property_exists($remoteData, 'status')) {
                    $I->wait(20);
                } else {
                    break;
                }
            }
        }
        if (property_exists($remoteData, 'status')) {
            $I->fail($remoteData->message);
        }

        $referenceData = [

            'email' => $example['email'],
            'address' => $example['address_line_1'],
            'address2' => $example['address_line_2'],
            'city' => $example['city'],
            'state' => $example['state'],
            'zip' => $example['zip'],
        ];

        $absentData = array_diff_assoc($referenceData, (array)$remoteData);

        $message = '';
        if (!empty($absentData)) {
            foreach ($absentData as $missingField => $value) {
                $message .= $missingField . ', ';
            }
            $I->fail($message . " is not present to the remote.");
        } else {
            echo ' Hurray!! Additional data has been sent to Platform.ly' . "\n";
        }
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     */
//    #[Group('Integration')]
    public function test_platformly_static_tag_apply(AcceptanceTester $I, Example $example): void
    {
        global $pageUrl;

        $formName = 'test_platformly_static_tag_apply';
        $integrationPositionNumber = 12;
        $tags = ['Asian', 'Non_US'];

        $I->deleteExistingForms();
        $I->initiateNewForm();
        $requiredField = [
            'generalFields' => ['email'],
        ];
        $I->createFormField($requiredField);
        $I->click(FluentFormsSelectors::saveForm);
        $I->wait(1);
        $I->seeSuccess('Form created successfully.');
        $I->renameForm(__FUNCTION__);
        $I->seeSuccess('Form renamed successfully.');

        $this->configurePlatformly($I,$integrationPositionNumber);
        $this->mapPlatformlyFields($I,'','', [], $tags, [], [], '');

        $I->deleteExistingPages();
        $I->createNewPage(__FUNCTION__);
        $I->wantTo('Fill the form with sample data');
        $I->amOnUrl($pageUrl);

        $I->wait(1);

        $I->fillField(FieldSelectors::email, ($example['email']));
        $I->click(FieldSelectors::submitButton);

        $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
        if (property_exists($remoteData, 'status')) {
            for ($i = 0; $i < 6; $i++) {
                $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
                if (property_exists($remoteData, 'status')) {
                    $I->wait(20);
                } else {
                    break;
                }
            }
        }
        if (property_exists($remoteData, 'status')) {
            $I->fail($remoteData->message);
        }
        //retrieving tags from remote
        $remoteTag = $remoteData->project[0]->data->tags;
        $remoteTagArray = [];
        foreach ($remoteTag as $tag) {
            $remoteTagArray[] = $tag->name;
        }
        $I->wait(3);

        //checking static tags
        foreach ($tags as $tag) {
            $I->assertContains($tag, $remoteTagArray);
        }
        echo ' Hurray!! Static Tag applied to platform.ly' . "\n";
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     */
//    #[Group('Integration')]
    public function test_platformly_dynamic_tag_apply(AcceptanceTester $I, Example $example): void
    {
        global $pageUrl;
        $integrationPositionNumber = 12;
        $api = '4XIamp9fiLokeugrcmxSLMQjoRyXyStw';
        $projectId = '2919';

        $I->deleteExistingForms();
        $I->initiateNewForm();
        $requiredField = [
            'generalFields' => ['email', 'nameFields'],
        ];
        $I->createFormField($requiredField);
        $I->click(FluentFormsSelectors::saveForm);
        $I->wait(1);
        $I->seeSuccess('Form created successfully.');
        $I->renameForm(__FUNCTION__);
        $I->seeSuccess('Form renamed successfully.');

        $this->configurePlatformly($I,$integrationPositionNumber, $api, $projectId);
        $dynamicTagArray = [
            'European' => ['names[First Name]', 'contains', 'John'],

        ];

        $this->mapPlatformlyFields($I,'','', [], [], $dynamicTagArray, [], '');

        $I->deleteExistingPages();
        $I->createNewPage(__FUNCTION__);
        $I->wantTo('Fill the form with sample data');
        $I->amOnUrl($pageUrl);

        $I->wait(1);

        $I->fillField(FieldSelectors::email, ($example['email']));
        $I->fillField(FieldSelectors::first_name, "John " . ($example['first_name']));
        $I->fillField(FieldSelectors::last_name, ($example['last_name']));
        $I->click(FieldSelectors::submitButton);

        $I->wait(3);
        $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
        if (property_exists($remoteData, 'status')) {
            for ($i = 0; $i < 5; $i++) {
                $remoteData = json_decode($this->fetchPlatformlyData($I,$example['email']));
                if (property_exists($remoteData, 'status')) {
                    $I->wait(20);
                } else {
                    break;
                }
            }
        }
        if (property_exists($remoteData, 'status')) {
            $I->fail($remoteData->message);
        }

        //retrieving tags from remote
        $remoteTag = $remoteData->project[0]->data->tags;
        $remoteTagArray = [];
        foreach ($remoteTag as $tag) {
            $remoteTagArray[] = $tag->name;
        }
        $I->wait(3);

        //checking static tags
        foreach ($dynamicTagArray as $tag => $value) {
            $I->assertContains($tag, $remoteTagArray);
        }
        echo ' Hurray!! Dynamic Tag applied to platform.ly' . "\n";
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldData
     */
//    #[Group('Integration')]
    public function test_platformly_activated_when_satisfy_all_conditions(AcceptanceTester $I, Example $example): void
    {
        global $pageUrl;

        $formName = 'test_platformly_dynamic_tag_apply';
        $integrationPositionNumber = 12;

        $I->deleteExistingForms();
        $I->initiateNewForm();
        $requiredField = [
            'generalFields' => ['email', 'nameFields'],
        ];
        $I->createFormField($requiredField);
        $I->click(FluentFormsSelectors::saveForm);
        $I->wait(1);
        $I->seeSuccess('Form created successfully.');
        $I->renameForm(__FUNCTION__);
        $I->seeSuccess('Form renamed successfully.');

        $this->configurePlatformly($I,$integrationPositionNumber);
        $dynamicTagArray = [
            'names[First Name]' => ['starts with', 'John'],
            'names[Last Name]' => ['equal', 'Doe'],
            'Email' => ['contains', '@gmail.com'],
        ];

        $this->mapPlatformlyFields($I,'','', [], [], [], $dynamicTagArray, '');

        $I->deleteExistingPages();
        $I->createNewPage(__FUNCTION__);
        $I->wantTo('Fill the form with sample data');
        $I->amOnUrl($pageUrl);

        $I->wait(1);
        $I->fillField(FieldSelectors::email, ($example['email']));
        $I->fillField(FieldSelectors::first_name, "John " . ($example['first_name']));
        $I->fillField(FieldSelectors::last_name, 'Doe');
        $I->click(FieldSelectors::submitButton);
        $I->assertStringContainsStringIgnoringCase('Success', $I->checkAPICallStatus('Success', FluentFormsAllEntries::logSuccessStatus));
    }
    #[skip]
    public function formPreparation_platformly_activated_when_satisfy_any_condition(AcceptanceTester $I): void
    {
        global $pageUrl;
        $integrationPositionNumber = 12;

        $I->deleteExistingForms();
        $I->initiateNewForm();
        $requiredField = [
            'generalFields' => ['email', 'nameFields'],
        ];
        $I->createFormField($requiredField);
        $I->click(FluentFormsSelectors::saveForm);
        $I->wait(1);
        $I->seeSuccess('Form created successfully.');
        $I->renameForm(__FUNCTION__);
        $I->seeSuccess('Form renamed successfully.');

        $this->configurePlatformly($I,$integrationPositionNumber);
        $dynamicTagArray = [
            'names[First Name]' => ['starts with', 'John'],
            'names[Last Name]' => ['not equal', 'Doe'],
            'Email' => ['contains', '@gmail.com'],
        ];

        $this->mapPlatformlyFields($I,'','', [], [], [], $dynamicTagArray, 'Any');

        $I->deleteExistingPages();
        $I->createNewPage(__FUNCTION__);
    }

    /**
     * @dataProvider \Tests\Support\Factories\DataProvider\FormData::fieldDataForConditionalForm
     */
//    #[Group('Integration')]
    public function test_platformly_activated_when_satisfy_any_condition(AcceptanceTester $I, Example $example): void
    {
        if ($example['id'] == 1) {
            $this->formPreparation_platformly_activated_when_satisfy_any_condition($I);
        }
        global $pageUrl;

        $I->wantTo('Fill the form with sample data');
        $I->amOnUrl($pageUrl);
        $I->wait(1);

        $I->fillField(FieldSelectors::email, ($example['email']));
        $I->fillField(FieldSelectors::first_name, ($example['first_name']));
        $I->fillField(FieldSelectors::last_name, ($example['last_name']));
        $I->click(FieldSelectors::submitButton);

        if ($example['id'] == 1) {
            $I->dontSee(FluentFormsAllEntries::noLogFound);
            $I->assertStringContainsStringIgnoringCase('Success', $I->checkAPICallStatus('Success', FluentFormsAllEntries::logSuccessStatus));
        }
        if ($example['id'] == 2) {
            $I->dontSee(FluentFormsAllEntries::logSuccessStatus);
            $I->assertStringContainsStringIgnoringCase('Sorry, No Logs found!', $I->checkAPICallStatus('Sorry', FluentFormsAllEntries::noLogFound));
        }
    }

}
