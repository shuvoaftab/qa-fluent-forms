<?php


namespace Tests\Integrations;

use Codeception\Attribute\Group;
use Tests\Support\AcceptanceTester;
use Tests\Support\Factories\DataProvider\DataGenerator;
use Tests\Support\Helper\GeneralFieldCustomizer;
use Tests\Support\Helper\Integrations\IntegrationHelper;
use Tests\Support\Helper\Integrations\PostCpt;

class IntegrationPostCptCest
{
    use IntegrationHelper, PostCpt, GeneralFieldCustomizer, DataGenerator;
    public function _before(AcceptanceTester $I)
    {
        $I->loadDotEnvFile();
        $I->loginWordpress();
    }

    // tests
    #[Group('Integration', 'native', 'all')]
    public function test_post_creation_using_cpt(AcceptanceTester $I)
    {
        // Generate a unique page name
        $pageName = __FUNCTION__ . '_' . rand(1, 100);

        // Turn on the integration for Post/CPT Creation
        $this->turnOnIntegration($I, "Post/CPT Creation");

        // Define custom field names
        $customName = [
            'postTitle'    => 'Post Title',
            'postContent'  => 'Post Content',
            'postExcerpt'  => 'Post Excerpt',
        ];

        // Prepare the form
        $this->prepareForm(
            $I,
            $pageName,
            [
                'postFields' => ['postTitle', 'postContent', 'postExcerpt'],
            ],
            'yes',
            $customName,
            true,
            true
        );
        $this->configurePostCpt($I);
        $fillAbleDataArr = $this->buildArrayWithKey($customName);
        $this->mapPostCptFields($I, $fillAbleDataArr);

        $this->preparePage($I, $pageName);


    }
}
