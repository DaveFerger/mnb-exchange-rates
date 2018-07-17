<?php

namespace daveferger\MNBExchangeRates;

class ExchangeRates
{

    protected $_currencies,
        $_start_date,
        $_end_date;

    public function __construct($currencies = ['EUR'], $start_date = null, $end_date = null)
    {
        $this->setCurrencies($currencies);

        if($start_date === null)
            $start_date = date('Y-m-d');
        $this->setStartDate($start_date);

        if($end_date=== null)
            $end_date = date('Y-m-d');
        $this->setEndDate($end_date);
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->_end_date;
    }

    /**
     * @param mixed $end_date
     */
    public function setEndDate($end_date)
    {
        $this->_end_date = $end_date;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->_start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date)
    {
        $this->_start_date = $start_date;
    }

    /**
     * @return array
     */
    public function getCurrencies()
    {
        return $this->_currencies;
    }

    /**
     * @param string|array $currencies
     * @throws \Exception
     */
    public function setCurrencies($currencies)
    {
        if (is_string($currencies)) {
            $currencies = [$currencies];
        }

        foreach ($currencies as $currency) {
            if (!is_string($currency) || strlen($currency) !== 3) {
                throw new \Exception('Currency must be ISO code 4217 code');
            }
        }

        $this->_currencies = $currencies;
    }


    function getResults()
    {

        $client = new \SoapClient("http://www.mnb.hu/arfolyamok.asmx?wsdl");
        $response = $client->__soapCall("GetCurrentExchangeRates", []);

        $doc = new \DOMDocument;
        $doc->loadXML($response->GetCurrentExchangeRatesResult);
        $xpath = new \DOMXPath($doc);

        $query = '//MNBCurrentExchangeRates/Day/Rate[@curr=\'' . implode("'|'", $this->getCurrencies()) . '\']';
        $entries = $xpath->query($query);

        if ($entries->length) {
            return $currency . ": " . $entries->item(0)->nodeValue;
        } else {
            return "Nem tölthető be az árfolyam.";
        }
    }
}

?>
