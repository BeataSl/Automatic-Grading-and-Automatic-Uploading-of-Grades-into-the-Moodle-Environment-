<?php

// Lisage siia Praktikum 1 tööde kausta tee
$folder_path = 'TEE_PRAKTIKUMI1_KASUTANI';

// Avatakse fail tulemustega CSV formaadis 
$result_csv_file = fopen('resultPraktikum1.csv', 'w');

// Kirjutatakse CSV failile päis
fputcsv($result_csv_file, ['userid', 'assignName', 'grade']);


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

    // Eemaldatakse esimene rida (päis)
    array_shift($studentFileLines);

    // Läbitakse kõik ülejäänud read
    foreach ($studentFileLines as $line) {
        $row = str_getcsv($line);
        $userid = $row[0]; // Õpilase ID
        $assign_name = 'Praktikum 1'; // Ülesande nimi
        $grade = $row[1]; // Hinne

        // Kirjutatakse CSV failile õpilase andmed
        fputcsv($result_csv_file, [$userid, $assign_name, $grade]);
    }
}

// Suletakse CSV fail
fclose($result_csv_file);

?>
