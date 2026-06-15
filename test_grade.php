<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/gradelib.php');

require_login();

$courseid   = 2;   // curso válido
$instanceid = 14;  // intersaberesplay.id (NÃO cmid)

grade_update(
    'mod/intersaberesplay',
    $courseid,
    'mod',
    'intersaberesplay',
    $instanceid,
    0,
    null,
    [
        'itemname'  => 'TESTE GRADE',
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax'  => 100,
        'grademin'  => 0
    ]
);

echo 'executado';
