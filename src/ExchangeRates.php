<?php

class ExchangeRates {

    function getResults($currency = 'EUR') {

        $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?wsdl");
        $response = $client->__soapCall("GetCurrentExchangeRates", array());

        $doc = new DOMDocument;
        $doc->loadXML($response->GetCurrentExchangeRatesResult);
        $xpath = new DOMXPath($doc);

        $query = "//MNBCurrentExchangeRates/Day/Rate[@curr='$currency']";
        $entries = $xpath->query($query);

        if ($entries->length) {
            return $currency.": ".$entries->item(0)->nodeValue;
        } else {
            return "Nem tölthető be az árfolyam.";  
        }
    }
}
    
?>
