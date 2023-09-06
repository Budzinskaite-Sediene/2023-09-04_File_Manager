<?php
$randomString = '';
$letters = 'abcdefghijklmnopqrstuvwxyz';
for ($i = 0; $i < 3; $i++) {
    $randomString .= $letters[rand(0, strlen($letters) - 1)];
}

$filename = 'skaičius.txt';
$file = fopen($filename, 'w');
if ($file) {
    fwrite($file, $randomString);
    fclose($file);
    echo 'Failas "skaičius.txt" sukurtas ir į jį įrašytas atsitiktinis stringas: ' . $randomString;
} else {
    echo 'Nepavyko sukurti failo.';
}
?>