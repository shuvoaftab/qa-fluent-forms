<?php

namespace Tests\Support\Selectors;

use Tests\Support\Factories\DataProvider\FormData;

class FieldSelectors
{
    const first_name = "//input[contains(@id,'_first_name_')]";
    const last_name = "//input[contains(@id,'_last_name_')]";
    const email = "//input[contains(@id,'email')]";
    const address_line_1 = "//input[contains(@id,'address_line_1_')]";
    const address_line_2 = "//input[contains(@id,'address_line_2_')]";
    const city = "//input[contains(@id,'city_')]";
    const state = "//input[contains(@id,'state_')]";
    const zip = "//input[contains(@id,'zip_')]";
    const country = "//select[contains(@id,'country_')]";
    const phone = "//input[contains(@id,'phone')]";
    const dateTime = "//input[contains(@id,'_datetime')]";
    const password = "//input[contains(@id,'password')]";
    const reTypePassword = "//input[contains(@id,'password_')]";


    const submitButton = "//button[normalize-space()='Submit Form']";

    private static array $keyMap = [
        'first_name' => self::first_name,
        'last_name' => self::last_name,
        'email' => self::email,
        'address_line_1' => self::address_line_1,
        'address_line_2' => self::address_line_2,
        'city' => self::city,
        'state' => self::state,
        'zip' => self::zip,
        'country' => self::country,
        'phone' => self::phone,
        'dateTime' => self::dateTime,
        'password' => self::password,
        'reTypePassword' => self::reTypePassword,
    ];

    public static function generalFieldDataPool(): array
    {
        $formData = new FormData();
        $fieldData = $formData->fieldData()[0]; // Get the first entry from fieldData()

        $preparedArray = [];
        foreach (self::$keyMap as $key => $xpathExpression) {
            if (array_key_exists($key, $fieldData)) {
                $preparedArray[$xpathExpression] = $fieldData[$key];
            }
        }
        return $preparedArray;
    }

    public static function getFieldDataArray(array $keys): array
    {
        $shortCodePool = self::generalFieldDataPool();
        $preparedArray = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, self::$keyMap)) {
                $xpathExpression = self::$keyMap[$key];
                if (array_key_exists($xpathExpression, $shortCodePool)) {
                    $preparedArray[$xpathExpression] = $shortCodePool[$xpathExpression];
                }
            }
        }

        return $preparedArray;
    }
}
