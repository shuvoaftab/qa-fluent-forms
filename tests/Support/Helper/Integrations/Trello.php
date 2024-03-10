<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Tests\Support\Selectors\FluentFormsSelectors;
use Tests\Support\Selectors\FluentFormsSettingsSelectors;
use Unirest;

trait Trello
{
    use IntegrationHelper, UserRegistration;

    public function configureTrello(AcceptanceTester $I, $integrationName): void
    {
        $this->turnOnIntegration($I,$integrationName);
        $isTrelloConfigured = $I->checkElement(FluentFormsSettingsSelectors::APIDisconnect);

        if (!$isTrelloConfigured) {
            $I->fillField(
                FluentFormsSelectors::commonFields("Trello access Key", "access token Key"),
                getenv("TRELLO_ACCESS_KEY")
            );
            $I->clicked(FluentFormsSettingsSelectors::APISaveButton);
        }
        $this->configureApiSettings($I,"Trello");
    }


    public function mapTrelloField(AcceptanceTester $I): void
    {
        $I->waitForElement(FluentFormsSelectors::feedName, 30);
        $I->fillField(FluentFormsSelectors::feedName, 'Trello');
        $I->clickByJS(FluentFormsSelectors::dropdown('Trello Configuration'));
        $I->clickOnText('fluentforms','Trello Configuration');

        $I->clickByJS(FluentFormsSelectors::dropdown('Select List'));
        $I->clickOnText('To Do','Select List');

        $I->clickByJS(FluentFormsSelectors::dropdown('Select Card Label'));
        $I->clickOnText('blue','Select Card Label');

        $I->clickByJS(FluentFormsSelectors::dropdown('Select Members'));
        $I->clickOnText('Test WordPress','Select Members');

        $I->filledField(FluentFormsSelectors::commonFields(
            'Card Title','Select a Field or Type Custom value'),"{inputs.description}");
        $I->filledField(FluentFormsSelectors::commonFields(
            'Card Content','Select a Field or Type Custom value'),"{inputs.description_1}");

        $I->clicked(FluentFormsSelectors::saveButton("Save Feed"));
        $I->seeSuccess("Integration successfully saved");
    }
    public function fetchTrelloData(AcceptanceTester $I, $titleToSearch): array
    {
        $expectedData = $this->retryFetchingData($I,[$this, 'fetchData'], $titleToSearch,4);
        if (empty($expectedData)) {
            $expectedData = null;
        }
        return $expectedData;
    }
    public function fetchData($titleToSearch): array
    {
        $headers = array(
            'Accept' => 'application/json'
        );
        $query = array(
            'query' => $titleToSearch,
            'key' => getenv("TRELLO_API_KEY"),
            'token' => getenv("TRELLO_ACCESS_TOKEN"),
        );

        $response = Unirest\Request::get(
            'https://api.trello.com/1/search', $headers, $query
        );

        $cards = $response->body->cards;

        $title= null;
        $cardContent = null;
        foreach ($cards as $card) {
            $cardContent = $card->desc;
            $title = $card->name;
        }
        return ['title'=> $title, 'cardContent' => $cardContent];
    }


}
