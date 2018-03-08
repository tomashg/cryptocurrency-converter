<?php

if (isset($_POST['amount'], $_POST['conversionFrom'], $_POST['conversionTo'])) {
    //pobieranie danych z formularza
    $amount = $_POST['amount'];
    $conversionFrom = $_POST['conversionFrom'];
    $conversionTo = $_POST['conversionTo'];

    //json
    $currency = @file_get_contents('https://api.abucoins.com/products/ticker');
    //$currency = @file_get_contents('test.json');
    $currencyArray= json_decode($currency);

    try {
        if (!is_numeric($amount))
            throw new Exception("This is not a number");

        if ($conversionFrom == $conversionTo) {
            throw new Exception("Convertible not possible. Same units");
        }

    } catch(Exception $e) {

        echo json_encode(array(
            'error' => array(
                'msg' => $e->getMessage(),
                'code' => $e->getCode(),
            ),
        ));
        exit();
    }

    function convert($currencyArray, $conversionFrom, $conversionTo, $amount) {

        foreach($currencyArray as $value) {
            if ($value->product_id == $conversionFrom . "-" .$conversionTo) {
                $score = $amount * ($value->price+0);
                return $score;
            }

            if ($value->product_id == $conversionTo . "-" .$conversionFrom) {
                $score = $amount / ($value->price+0);
                return $score;
            }
        }
        return false;
    }
    //konwersja bezposrednia
    $score = convert($currencyArray, $conversionFrom, $conversionTo, $amount);

    //konwersja posrednia
    if ($score == false){
        try {
            $conversionToTmp = "BTC";
            $score = convert($currencyArray, $conversionFrom, $conversionToTmp, $amount);

            if (!$score)
                throw new Exception("Conversion is not possible. Not enough data");

            $conversionFromTmp = "BTC";
            $score = convert($currencyArray, $conversionFromTmp, $conversionTo, $score);
        } catch(Exception $e) {

            echo json_encode(array(
                'error' => array(
                    'msg' => $e->getMessage(),
                    'code' => $e->getCode(),
                ),
            ));
            exit();
        }
    }
    
    echo json_encode(round($score,5));
    //nie jestem pewny czy zawsze musi byc zaokraglnie do 5 miejsc alternatywnie
    ///echo json_encode($score);
}
?>
