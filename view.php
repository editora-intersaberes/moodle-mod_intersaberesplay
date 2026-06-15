<?php
require_once('../../config.php');
require_once('lib.php');
require_once('Service/PlayService.php');

$id = required_param('id', PARAM_INT); // course_module ID

$cm = get_coursemodule_from_id('intersaberesplay', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$intersaberesplay = $DB->get_record('intersaberesplay', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Cabeçalho da página
$PAGE->set_url('/mod/intersaberesplay/view.php', ['id' => $cm->id]);
$PAGE->set_title($intersaberesplay->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($intersaberesplay->name);

// **Aqui podemos pegar usuário logado**
global $USER;

global $SITE;

// Dados para enviar
$postData = [
    'user_id' => htmlentities($USER->id),
    'name' => htmlentities($USER->firstname . ' ' . $USER->lastname),
    'email' => filter_var($USER->email, FILTER_SANITIZE_EMAIL),
    'user_name' => htmlentities($USER->username),
    'support_name' =>get_config('moodle', 'supportname'),
    'support_email' => get_config('moodle', 'supportemail'),
    'course_id' => $COURSE->id,
    'course_name' => $COURSE->fullname,
    'activity_id' => $cm->id,
    'activity_name' => $cm->name,
    'site' => $SITE->fullname,
    'site_url' => $CFG->wwwroot,
    'id_curso_instituicao' => $intersaberesplay->curso,
    'id_disciplina_instituicao' => $intersaberesplay->disciplina,
    'token' => get_config('mod_intersaberesplay', 'token'),
    'mostrar_em_nova_pagina' => $intersaberesplay->targetblank
];


$playService = new PlayService();
$resposta = $playService->acessarViaPlugin($postData);

echo $resposta;

echo $OUTPUT->footer();
