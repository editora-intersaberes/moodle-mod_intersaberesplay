<?php

require_once('Service/PlayService.php');


function intersaberesplay_add_instance($data, $mform = null)
{
    global $DB;

    // Adiciona timestamps
    $data->timecreated = time();

    // Garante que campos personalizados existam
    if (!isset($data->curso)) {
        $data->curso = 0;
    }
    if (!isset($data->disciplina)) {
        $data->disciplina = 0;
    }

    if (empty($data->targetblank)) {
        $data->targetblank = 0;
    }

    // Insere registro e retorna o ID
    $data->id = $DB->insert_record('intersaberesplay', $data);

    // intersaberesplay_grade_item_update($data);

    return $data->id;
}

function intersaberesplay_update_instance($data, $mform = null)
{
    global $DB;

    // Atualiza timestamps
    $data->timemodified = time();

    // O Moodle envia o campo "instance" no form ao editar
    $data->id = $data->instance;

    // Garante que campos personalizados existam
    if (!isset($data->curso)) {
        $data->curso = 0;
    }
    if (!isset($data->disciplina)) {
        $data->disciplina = 0;
    }

    if (empty($data->targetblank)) {
        $data->targetblank = 0;
    }

    //  intersaberesplay_grade_item_update($data);

    // Atualiza o registro
    return $DB->update_record('intersaberesplay', $data);
}


/**
 * cria um registro no mdl_grade_items caso não exista
 *
 * @param [type] $intersaberesplay
 * @param [type] $grades
 * @return void
 */
function intersaberesplay_grade_item_update($intersaberesplay, $grades = null)
{
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $playService = new PlayService();
    $resposta = $playService->getPesoMaximoProvas([
        'id_curso_instituicao' =>  $intersaberesplay->curso ?? null,
        'id_disciplina_instituicao' =>  $intersaberesplay->disciplina ?? null,
        'token' => get_config('mod_intersaberesplay', 'token'),
    ]);
    $resposta = json_decode($resposta, true);
    
    $params = [
        'itemname' => $intersaberesplay->name,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax' => $resposta['pesoProva'],
        'grademin' => 0
    ];

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update(
        'mod/intersaberesplay',
        $intersaberesplay->course,
        'mod',
        'intersaberesplay',
        $intersaberesplay->id,
        0,
        $grades,
        $params
    );
}

function intersaberesplay_supports($feature)
{
    switch ($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_MOD_INTRO:
            return false; // <-- Desativa uso de intro
        default:
            return null;
    }
}


/**
 * Deletes an instance of the intersaberesplay module
 *
 * @param int $id ID of the module instance
 * @return bool true if successful, false otherwise
 */
function intersaberesplay_delete_instance($id)
{
    global $DB;

    // Verifica se existe
    if (!$record = $DB->get_record('intersaberesplay', ['id' => $id])) {
        return false;
    }

    // Aqui você pode apagar tabelas relacionadas se houver
    // Exemplo: $DB->delete_records('intersaberesplay_somechildtable', ['intersaberesplayid' => $id]);

    // Apaga a instância principal
    $DB->delete_records('intersaberesplay', ['id' => $id]);

    return true;
}
