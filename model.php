<?php
date_default_timezone_set('Asia/Manila');

$dataFile = 'queue_data.json';

$patients = [];
if (file_exists($dataFile)) {
    $patients = json_decode(file_get_contents($dataFile), true);
}

function savePatient($name, $mobile, $dept) {
    global $patients, $dataFile;

    $newPatient = [
        'id'       => count($patients) + 1,
        'queue_no' => $dept . '-' . rand(100, 999),
        'name'     => $name,
        'mobile'   => $mobile,
        'dept'     => $dept,
        'status'   => 'Waiting',
        'time'     => date('Y-m-d H:i:s')
    ];

    $patients[] = $newPatient;
    file_put_contents($dataFile, json_encode($patients));

    return $newPatient;
}
?>