<?php

namespace Moloni;

/**
 * Multiple tools for handling recurring tasks
 * Class Tools
 * @package Moloni
 */
class Tools
{

    /**
     * @param string $string
     * @param int $productId
     * @param int $variationId
     * @return string
     */
    public static function createReferenceFromString($string, $productId = 0, $variationId = 0)
    {
        $reference = '';
        $name = explode(' ', $string);

        foreach ($name as $word) {
            $reference .= '_' . mb_substr($word, 0, 3);
        }

        if ((int)$productId > 0) {
            $reference .= '_' . $productId;
        }

        if ((int)$variationId > 0) {
            $reference .= '_' . $variationId;
        }

        return $reference;
    }

    /**
     * Get a tax id given a tax rate
     * As a fallback if we don't find a tax with the same rate we return the company default
     * @param $taxRate
     * @return mixed
     * @throws Error
     */
    public static function getTaxIdFromRate($taxRate)
    {
        $defaultTax = 0;
        $taxesList = Curl::simple('taxes/getAll', []);
        if (!empty($taxesList) && is_array($taxesList)) {
            foreach ($taxesList as $tax) {
                if ((int)$tax['active_by_default'] === 1) {
                    $defaultTax = $tax['tax_id'];
                }

                if ((float)$tax['value'] === (float)$taxRate) {
                    return $tax['tax_id'];
                }
            }
        }

        return $defaultTax;
    }

    /**
     * Get full tax Object given a tax rate
     * As a fallback if we don't find a tax with the same rate we return the company default
     * @param $taxRate
     * @return mixed
     * @throws Error
     */
    public static function getTaxFromRate($taxRate)
    {
        $defaultTax = 0;
        $taxesList = Curl::simple('taxes/getAll', []);
        if (!empty($taxesList) && is_array($taxesList)) {
            foreach ($taxesList as $tax) {
                if ((int)$tax['active_by_default'] === 1) {
                    $defaultTax = $tax;
                }

                if ((float)$tax['value'] === (float)$taxRate) {
                    return $tax;
                }
            }
        }

        return $defaultTax;
    }

    /**
     * @param $countryCode
     * @return string
     * @throws Error
     */
    public static function getCountryIdFromCode($countryCode)
    {
        $countriesList = Curl::simple('countries/getAll', []);
        if (!empty($countriesList) && is_array($countriesList)) {
            foreach ($countriesList as $country) {
                if (strtoupper($country['iso_3166_1']) === strtoupper($countryCode)) {
                    return $country['country_id'];
                    break;
                }
            }
        }

        return '1';
    }

    /**
     * @param int $from
     * @param int $to
     * @return float
     * @throws Error
     */
    public static function getCurrencyExchangeRate($from, $to)
    {
        $currenciesList = Curl::simple('currencyExchange/getAll', []);
        if (!empty($currenciesList) && is_array($currenciesList)) {
            foreach ($currenciesList as $currency) {
                if ((int)$currency['from'] === $from && (int)$currency['to'] === $to) {
                    return (float)$currency['value'];
                }
            }
        }

        return 1;
    }

    /**
     * @param string $currencyCode
     * @return int
     * @throws Error
     */
    public static function getCurrencyIdFromCode($currencyCode)
    {
        $currenciesList = Curl::simple('currencies/getAll', []);
        if (!empty($currenciesList) && is_array($currenciesList)) {
            foreach ($currenciesList as $currency) {
                if ($currency['iso4217'] === mb_strtoupper($currencyCode)) {
                    return $currency['currency_id'];
                }
            }
        }

        return 1;
    }

    /**
     * @param $input
     * @return string
     */
    public static function zipCheck($input)
    {
        $zipCode = trim(str_replace(' ', '', $input));
        $zipCode = preg_replace('/[^0-9]/', '', $zipCode);
        if (strlen($zipCode) == 7) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . $zipCode[5] . $zipCode[6];
        }
        if (strlen($zipCode) == 6) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . $zipCode[5] . '0';
        }
        if (strlen($zipCode) == 5) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . '00';
        }
        if (strlen($zipCode) == 4) {
            $zipCode = $zipCode . '-' . '000';
        }
        if (strlen($zipCode) == 3) {
            $zipCode = $zipCode . '0-' . '000';
        }
        if (strlen($zipCode) == 2) {
            $zipCode = $zipCode . '00-' . '000';
        }
        if (strlen($zipCode) == 1) {
            $zipCode = $zipCode . '000-' . '000';
        }
        if (strlen($zipCode) == 0) {
            $zipCode = '1000-100';
        }
        if (self::finalCheck($zipCode)) {
            return $zipCode;
        }

        return '1000-100';
    }

    /**
     * Validate a Zip Code format
     * @param string $zipCode
     * @return bool
     */
    private static function finalCheck($zipCode)
    {
        $regexp = "/[0-9]{4}\-[0-9]{3}/";
        if (preg_match($regexp, $zipCode)) {
            return (true);
        }

        return (false);
    }


}
