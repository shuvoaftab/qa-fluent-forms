<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;

interface IntegrationInterface
{
    public function configure(AcceptanceTester $I, string $integrationName);
    public function mapFields(AcceptanceTester $I, array $fieldMapping, array $listOrService=null);
    public function fetchRemoteData(AcceptanceTester $I, string $emailToFetch);
    public function fetchData(string $emailToFetch);

}