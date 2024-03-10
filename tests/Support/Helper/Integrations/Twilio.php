<?php

namespace Tests\Support\Helper\Integrations;

use Tests\Support\AcceptanceTester;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

trait Twilio
{
    public function configure(AcceptanceTester $I, string $integrationName)
    {
        // TODO: Implement configure() method.
    }

    public function mapFields(AcceptanceTester $I, array $fieldMapping, array $listOrService)
    {
        // TODO: Implement mapFields() method.
    }

    public function fetchRemoteData(AcceptanceTester $I, string $emailToFetch)
    {
//        curl -X GET "https://serverless.twilio.com/v1/Services/MG38d267e3d0aadc4e44d2c36dfc445b77/Environments/SM84fc6169fa32a6140499312da6c32b23/Logs/" \
//-u ACfbf01b26cc777f40d806a4cebc47add9:5a40e7f9a24aa20c5d4058519e3b5c46
//
//    curl -X GET "https://serverless.twilio.com/v1/Services/MG38d267e3d0aadc4e44d2c36dfc445b77/Environments?PageSize=20" \
//-u ACfbf01b26cc777f40d806a4cebc47add9:5a40e7f9a24aa20c5d4058519e3b5c46
//
//    curl -X GET "https://serverless.twilio.com/v1/Services?PageSize=20" \
//-u ACfbf01b26cc777f40d806a4cebc47add9:5a40e7f9a24aa20c5d4058519e3b5c46
//
//    curl -X GET "https://monitor.twilio.com/v1/Alerts/" \
//-u ACfbf01b26cc777f40d806a4cebc47add9:5a40e7f9a24aa20c5d4058519e3b5c46

        // TODO: Implement fetchRemoteData() method.
    }

    public function fetchData($searchByEmail)
    {
        $url = 'https://api.pipedrive.com/v1/users/find';

        // Set up query parameters
        $query = http_build_query([
            'term' => $searchTerm,
            'search_by_email' => $searchByEmail ? 1 : 0,
        ]);

        // Append the query to the URL
        $url .= '?' . $query;

        // Replace with your Pipedrive API token
        $apiToken = 'YOUR_PIPEDRIVE_API_TOKEN';

        // Initialize cURL session
        $curl = curl_init($url);

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiToken,
        ]);

        // Make the GET request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
            // Handle the error as needed
        }

        // Close cURL session
        curl_close($curl);

        // Decode the JSON response into an array
        $data = json_decode($response, true);

        return $data;



    }
}