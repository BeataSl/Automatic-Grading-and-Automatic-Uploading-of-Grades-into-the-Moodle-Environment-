<?php

// Lisage siia Praktikum 2 tööde kausta tee
$folder_path = 'TEE_PRAKTIKUMI1_KASUTANI';
// Lisage siia paroolidega faili tee
$passwords_file_path = 'TEE_PAROOLIDE_FAILINI_passwords.txt';


// Avatakse fail tulemustega CSV formaadis 
$result_csv_file = fopen('resultPraktikum2.csv', 'w');
// Kirjutatakse CSV failile päis
fputcsv($result_csv_file, ['userid', 'assignName', 'grade']);

// Loetakse paroolid failist
$passwords = [];
$passwordLines = file($passwords_file_path, FILE_IGNORE_NEW_LINES);

// Läbitakse iga rida paroolide failist
foreach ($passwordLines as $line) {
    // Jagatakse iga rida sõnadeks ja salvestatakse id ja parool vastavalt
    list($id, $password) = preg_split('/\s+/', trim($line));
    // Salvestatakse id ja parooli paar paroolide massiivi
    $passwords[$id] = $password;
}

// Läbitakse kõik failid kaustas
foreach (scandir($folder_path) as $filename) {
    $file_path = $folder_path . '/' . $filename;


    // Jätkatakse juhul, kui fail on "." või ".." kaust
    // "." tähistab jooksva kausta ja ".." tähistab vanemkausta,
    // neid pole vaja töödelda.
    if ($filename === '.' || $filename === '..') {
        continue;
    }

    // Loetakse üliõpilase faili read
    $studentFileLines = file($file_path, FILE_IGNORE_NEW_LINES);
    $userid = '';

    // Kasutaja ID leidmine faili esimesest reast
    preg_match('/\d+/', $studentFileLines[0], $matches);


    // Määratakse kasutaja ID
    if (!empty($matches)) {
        $userid = $matches[0];
    }
    else {
        // Viga, kui kasutaja ID-d ei leita
        $userid = "ERROR";
        echo "'userid' ei ole leitud failis: $filename";
    }

    // Eemaldatakse esimene rida (päis)
    array_shift($studentFileLines);


    // Ülesande nimi
    $assign_name = 'Praktikum 2';

    // Loetakse vastused
    $unique_answers = [];

    // Läbitakse iga üliõpilase faili rida
    foreach ($studentFileLines as $line) {
        $words = preg_split('/[\s,]+/', $line); // Jagatakse rida sõnadeks tühikute ja koma abil

        // Iga sõna lisatakse vastuste massiivi
        foreach ($words as $word) {
            if (!empty($word)) {
                $unique_answers[] = $word;
            }
        }
    }

    // Õigete vastuste kontroll
    $correct_answers1 = ['siin', 'on', 'õiged', 'vastused', 'jälle', 'ka']; // Õiged vastused 1a ja 1b ülesannetes
    $correct_answers2 = ['puu', 'mustikas', 'tee', 'kohv', 'kass', 'koer', 'raamat', 'kruus', 'ilm']; // Õiged vastused 2 ülesandes

    // Õigete vastuste loendurid 
    $found_count1 = 0;
    $found_count2 = 0;

    // Kontrollitakse iga õiget vastust esimeses vastuste loendis ja suurendatakse loendurit vastavalt
    foreach ($correct_answers1 as $correct_answer) {
        if (in_array($correct_answer, $unique_answers)) {
            $found_count1++;
        }
    }
    // Kontrollitakse iga õiget vastust teises vastuste loendis ja suurendatakse loendurit vastavalt
    foreach ($correct_answers2 as $correct_answer) {
        if (in_array($correct_answer, $unique_answers)) {
            $found_count2++;
        }
    }

    // Esimese ülesande hinne arvutamine
    $max_grade1 = 1;
    $grade1 = round($found_count1 / count($correct_answers1), 2);

    // Teise ülesande hinne arvutamine
    $max_grade2 = 0.5;
    $min_correct_answers2 = 3;
    $grade2 = 0;
    if ($found_count2 >= $min_correct_answers2) {
        $grade2 = $max_grade2;
    }
    else {
        $grade2 = round($found_count2 / $min_correct_answers2 * $max_grade2, 2);
    }

    // Kolmanda ülesande hinne arvutamine
    $grade3 = 0;
    if (isset($passwords[$userid]) && in_array($passwords[$userid], $unique_answers, true)) {
        $grade3 = 2;
    }

    // Arvutatakse lõpphinne
    $total_grade = $grade1 + $grade2 + $grade3;

    // Kirjutatakse CSV failile õpilase andmed ja hinne
    fputcsv($result_csv_file, [$userid, $assign_name, $total_grade]);
}
// Suletakse CSV fail
fclose($result_csv_file);
?>
