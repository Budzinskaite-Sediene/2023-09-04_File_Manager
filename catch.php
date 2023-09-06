<?php

// Funkcija, kuri sugeneruoja atsitiktinį 3 raidžių stringą

function generateRandomString() {

    $letters = 'abcdefghijklmnopqrstuvwxyz';

    $randomString = '';

    for ($i = 0; $i < 3; $i++) {

        $randomString .= $letters[rand(0, strlen($letters) - 1)];

    }

    return $randomString;

}

 

// Nuskaitome išsaugotą reikšmę iš failo "skaičius.txt"

$filename = 'skaičius.txt';

$savedString = file_get_contents($filename);

 

// Sukuriamas kintamasis, kurį naudosime tikrinti, ar sugeneruotas stringas sutampa

$generatedString = '';

 

// Sukuriamas kintamasis, kuriame saugosime visus sugeneruotus stringus

$allGeneratedStrings = [];

 

// Generuojame stringus ir tikriname, ar jie sutampa su išsaugota reikšme

do {

    $generatedString = generateRandomString();

    $allGeneratedStrings[] = $generatedString;

} while ($generatedString !== $savedString);

 

// Išvedame visus sugeneruotus stringus

foreach ($allGeneratedStrings as $index => $string) {

    echo "Sugeneruotas stringas $index: $string<br>";

}

 

echo "Viso sugeneruota stringų: " . count($allGeneratedStrings);

?>