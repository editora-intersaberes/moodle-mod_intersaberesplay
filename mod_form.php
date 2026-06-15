<?php
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_intersaberesplay_mod_form extends moodleform_mod
{

    public function definition()
    {
        global $PAGE;

        $mform = $this->_form;

        $this->cadastrarInstituicaoDemo();

        // --------------------------
        // Campo nome
        // --------------------------
        $mform->addElement('text', 'name', get_string('name', 'mod_intersaberesplay'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // --------------------------
        // Select Curso
        // --------------------------
        $cursooptions = $this->get_courses_from_api();
        // $mform->addElement('select', 'curso', get_string('curso', 'mod_intersaberesplay'), $cursooptions);

        $mform->addElement(
            'autocomplete',
            'curso',
            get_string('curso', 'mod_intersaberesplay'),
            $cursooptions,
            [
                'multiple' => false,
                'noselectionstring' => get_string('select'),
                // 'clearable' => true
            ]
        );

        $mform->addRule('curso', null, 'required', null, 'client');
        $mform->setType('curso', PARAM_INT);

        // --------------------------
        // Select Disciplina
        // --------------------------
        $disciplinas = $this->get_disciplines_from_api();
        $disciplinaOptions = [];
        foreach ($disciplinas as $d) {
            $disciplinaOptions[$d['id_disciplina_instituicao']] = $d['nome_disciplina'];
        }

        $mform->addElement(
            'autocomplete',
            'disciplina',
            get_string('disciplina', 'mod_intersaberesplay'),
            $disciplinaOptions,
            [
                'multiple' => false,
                'noselectionstring' => get_string('select'),
                // 'clearable' => true
            ]
        );

        // Adicionar data-cursoid aos options via JS ou attrs, mas como é autocomplete, vamos usar JS para filtrar
        // Na verdade, para autocomplete, precisamos manipular o select subjacente

        $mform->addRule('disciplina', null, 'required', null, 'client');
        $mform->setType('disciplina', PARAM_INT);

        // Passa as disciplinas para JS
        $PAGE->requires->js_init_code('var allDisciplinas = ' . json_encode($disciplinas) . ';');


        // --------------------------
        // Opção: abrir em nova aba
        // --------------------------
        $mform->addElement(
            'advcheckbox',
            'targetblank',
            get_string('targetblank', 'mod_intersaberesplay'),
            get_string('targetblank_label', 'mod_intersaberesplay'),
            ['group' => 1],
            [0, 1]
        );
        $mform->setDefault('targetblank', 0);
        $mform->setType('targetblank', PARAM_INT);



        // --------------------------
        // Incluir JS para atualização dinâmica
        // --------------------------
        $js = file_get_contents(__DIR__ . '/ajax_mod_form.js');
        $PAGE->requires->js_init_code($js);

        // --------------------------
        // Campos padrão de curso/módulo
        // --------------------------
        $this->standard_coursemodule_elements(false);


        // --------------------------
        // Botões de ação
        // --------------------------
        $this->add_action_buttons();
    }

    private function cadastrarInstituicaoDemo()
    {
        $token = get_config('mod_intersaberesplay', 'token');
        if (!empty($token)) {
            // Token configurado, não precisa cadastrar instituição demo
            return;
        }


        $urbBase   = 'https://play.intersaberes.com';
        if (defined('INTERSABERES_PLAY_URL')) {
            $urbBase   = INTERSABERES_PLAY_URL;
        }
        $apiurl = "{$urbBase}/api/instituicao/cadastrar-instituicao-demo";

        global $SITE;
        global $CFG;

        $data = array(
            'site' => $SITE->fullname,
            'site_url' => $CFG->wwwroot,
            'support_name' => get_config('moodle', 'supportname'),
            'support_email' => get_config('moodle', 'supportemail'),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $apiurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resposta = curl_exec($ch);
        curl_close($ch);


        $resposta = json_decode($resposta, true);

        if ($resposta['token_plugin']) {
            set_config('token', $resposta['token_plugin'], 'mod_intersaberesplay');
        }

    }

    // Busca cursos via API
    private function get_courses_from_api()
    {
        $options = [];
        $token = get_config('mod_intersaberesplay', 'token');

        if (!empty($token)) {
            $urbBase   = 'https://play.intersaberes.com';
            if (defined('INTERSABERES_PLAY_URL')) {
                $urbBase   = INTERSABERES_PLAY_URL;
            }
            $apiurl = "{$urbBase}/api/instituicao/listar-cursos?token={$token}";


            $context = stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ]
            ]);

            $response = file_get_contents($apiurl, false, $context);

            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['lista'])) {
                    foreach ($data['lista'] as $item) {
                        $options[$item['id_curso_instituicao']] = $item['nome_curso'];
                    }
                }
            }
        }

        return $options;
    }


    // Busca cursos via API
    private function get_disciplines_from_api($idCursoInstituicao = null)
    {
        $options = [];
        $token = get_config('mod_intersaberesplay', 'token');

        if (!empty($token)) {
            $urbBase   = 'https://play.intersaberes.com';
            if (defined('INTERSABERES_PLAY_URL')) {
                $urbBase   = INTERSABERES_PLAY_URL;
            }

            $apiurl = "{$urbBase}/api/instituicao/listar-disciplinas?token={$token}&idCursoInstituicao={$idCursoInstituicao}";

            $context = stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ]
            ]);

            $response = file_get_contents($apiurl, false, $context);

            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['lista'])) {
                    return $data['lista'];
                }
            }
        }

        return $options;
    }
}
