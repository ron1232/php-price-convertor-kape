<?php

class PriceConvertor {
    public $currency_list = ["USD" => "$", "JPY" => "¥", "GBP" => "£", "EUR" => "€", "CAD" => "$", "AUD" => "$", "SEK" => "kr", "SGD" => "S$", "MXN" => "$", "NZD" => "$", "DKK" => "kr", "BRL" => "R$", "NOK" => "kr", "HKD" => "$", "CLP" => "$",
    "THB" => "฿", "ZAR" => "R", "INR" => "₹", "COP" => "$"];

    private $returnedPrice = "";

    public function __construct($price, $currency) {
        if(!array_key_exists($currency, $this->currency_list)) {
            $this->returnedPrice =  "$" . $this->roundUpPrice($price);
            return;
        }

        $result = $this->callToAPI($currency, $price);

        $new_price = $this->roundUpPrice($result["result"]);

        $this->returnedPrice = $this->currency_list[$currency] . $new_price;
    }

    public function __toString(){
        return $this->returnedPrice;   
    }

    private function countDigits($MyNum){
        return strlen(explode('.', (string)$MyNum)[0]);
    }

    private function callToAPI($to, $amount) {
        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "http://api.apilayer.com/exchangerates_data/convert?to=$to&from=USD&amount=$amount",
          CURLOPT_HTTPHEADER => [
            "Content-Type: text/plain",
            "apikey: eEfWd9MHj8z3feskmK68zRQs7sKCwj4a"
          ],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ]);
        
        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    private function roundUpPrice($price) {
        $formatted_price = (int)$price;
        $digits = $this->countDigits($price);

        if($digits === 3 || $digits === 2) {
            return (double)$formatted_price . ".99";
        }

        if($digits === 4) {
            return ceil($formatted_price / 10) * 10;
        }

        if($digits >= 5) {
            return ceil($formatted_price / 100) * 100; 
        }

        return $formatted_price;
    }
}