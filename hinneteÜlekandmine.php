<?php

// Moodle'i API juurde pääsemiseks vajalikud parameetrid
$token = 'aa361a3ea3a50b6b222cd9796c220582'; // Tõend (token)
$siteUrl = 'http://127.0.0.1/moodle'; // Moodle URL
$format = 'json'; // Andmete edastamise formaat

// Tee CSV admetega hinnete ülekandmiseks failini
$csvFile = 'CSV_FAILI_TEE';

// CSV failist andmete lugemine
$csvData = array_map('str_getcsv', file($csvFile));

// Eemaldatakse esimene rida (päis)
$headers = array_shift($csvData);


$courseId = 9; // Kursuse identifikaator

// Funktsioon ülesande activityid leidmiseks ülesande nime järgi
function findAssignmentActivityId($assignmentName, $contents) {
    if (!empty($contents)) {
        foreach ($contents as $section) {
            foreach ($section['modules'] as $module) {
                if ($module['name'] === $assignmentName) {
                    return $module['id'];
                }
            }
        }
    }
    return null;
}

// Funktsioon komponendi leidmiseks activityid järgi
function findComponentByActivityId($activityId, $contents) {
    if (!empty($contents)) {
        foreach ($contents as $section) {
            foreach ($section['modules'] as $module) {
                if ($module['id'] == $activityId) {
                    return $module['modname'];
                }
            }
        }
    }
    return '';
}

// Läbitakse CSV andmed rida-realt
foreach ($csvData as $row) {
    $userId = $row[0]; // Kasutaja ID
    $assignmentName = $row[1]; // Ülesande nimi
    $grade = $row[2]; // Hinne

    // Moodle'i API päringu parameetrid registreeritud kursusele kasutajate leidmiseks
    $paramsParticipants = array(
        'wstoken' => $token,
        'moodlewsrestformat' => $format,
        'wsfunction' => 'core_enrol_get_enrolled_users',
        'courseid' => $courseId,
    );

    // Moodle'i API päringu tegemine registreeritud kursusele kasutajate saamiseks
    $responseParticipants = file_get_contents($siteUrl . '/webservice/rest/server.php?' . http_build_query($paramsParticipants));
    $enrolledUsers = json_decode($responseParticipants, true);

    // Vigade kontroll
    // Kui päringus tekkis viga (nt. puudub juurdepääs või vale parameeter),
    // siis tagastab Moodle API erandi (exception). Kui erand on olemas,
    // prinditakse veateade ja jätkatakse järgmise kasutajaga.  
    if (isset($enrolledUsers['exception'])) {
        echo "API veateade (registreeritud kasutajad): " . $enrolledUsers['message'] . "\n";
        continue;
    }

    // Kontroll, kas kasutaja on kursusele registreeritud
    $enrolledUserIds = array_column($enrolledUsers, 'id');

    if (!in_array($userId, $enrolledUserIds)) {
        echo "Kasutaja ID-ga: $userId ei ole registreeritud sellel kursusel.\n";
        continue; 
    }


    // Moodle'i API päringu parameetrid kursuse sisu saamiseks
    $paramsContents = array(
        'wstoken' => $token,
        'moodlewsrestformat' => $format,
        'wsfunction' => 'core_course_get_contents',
        'courseid' => $courseId,
    );

    // Moodle'i API päringu tegemine kursuse sisu saamiseks
    $responseContents = file_get_contents($siteUrl . '/webservice/rest/server.php?' . http_build_query($paramsContents));
    $contents = json_decode($responseContents, true);

    // Vigade kontroll kursuse sisu vastuses
    // Kui päringus tekkis viga (nt. puudub juurdepääs või vale parameeter),
    // siis tagastab Moodle API erandi (exception). Kui erand on olemas,
    // prinditakse veateade ja jätkatakse järgmise kasutajaga.
    if (isset($contents['exception'])) {
        echo "API veateade (kursuse sisu): " . $contents['message'] . "\n";
        continue;
    }

    // Ülesande activityid leidmine
    $activityId = findAssignmentActivityId($assignmentName, $contents);
    $component = findComponentByActivityId($activityId, $contents);
    if ($activityId !== null) {

        // Moodle'i API päringu parameetrid hinnete uuendamiseks
        $paramsUpdate = array(
            'wstoken' => $token,
            'moodlewsrestformat' => $format,
            'wsfunction' => 'core_grades_update_grades',
            'source' => 'csv_file',
            'courseid' => $courseId,
            'component' => $component,
            'activityid' => $activityId,
            'itemnumber' => 0,
            'grades[0][studentid]' => $userId,
            'grades[0][grade]' => $grade,
        );

        // Moodle'i API päringu tegemine hinnete uuendamiseks
        $urlUpdate = $siteUrl . '/webservice/rest/server.php';  // Määratakse URL, kuhu saata HTTP POST päring
        $options = array(
            // Määratakse päis, meetod ja sisu
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n", // Määratakse päis, mis näitab, et sisu tüüp on x-www-form-urlencoded
                'method' => 'POST', // Määratakse meetodiks POST
                'content' => http_build_query($paramsUpdate), //Määratakse sisu, mis kodeeritakse URL-i vormingus, kasutades parameetreid

            ),
        );

        // Loob HTTP konteksti, kasutades määratud valikuid
        $context = stream_context_create($options);

        // Saadab HTTP POST päringu määratud URL-ile, kasutades loodud konteksti
        $responseUpdate = file_get_contents($urlUpdate, false, $context);

       // Muudab API vastuse stringi täisarvuks
        $responseCode = intval($responseUpdate);

        // Moodle'i API vastuse töötlemine
        if ($responseCode === 0) {
            echo "Hinne edukalt uuendatud kasutaja ID-ga: $userId\n";
        } else {
            echo "Hinne uuendamine ebaõnnestus kasutaja ID-ga: $userId\n";
        }
    } else {
        echo "Ei õnnestunud leida tegevuse ID-d ülesandele: $assignmentName\n";
    }
}

?>
