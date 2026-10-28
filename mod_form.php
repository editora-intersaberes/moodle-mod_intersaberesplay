<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Module form for creating/editing Intersaberes Play instances.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module form class for mod_intersaberesplay.
 *
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_intersaberesplay_mod_form extends moodleform_mod {
    /**
     * Define form fields.
     *
     * @return void
     */
    public function definition() {
        global $PAGE, $CFG;

        $mform = $this->_form;

        // Keep automatic demo registration, but never break the form on network policy failures.
        $this->register_demo_institution();

        // Activity name.
        $mform->addElement('text', 'name', get_string('name', 'mod_intersaberesplay'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Standard intro/description field.
        $this->standard_intro_elements();

        // Course selection.
        $courseoptions = $this->get_courses_from_api();
        $mform->addElement(
            'autocomplete',
            'curso',
            get_string('curso', 'mod_intersaberesplay'),
            $courseoptions,
            [
                'multiple' => false,
                'noselectionstring' => get_string('select', 'mod_intersaberesplay'),
            ]
        );
        $mform->addRule('curso', null, 'required', null, 'client');
        $mform->setType('curso', PARAM_INT);

        // Discipline selection.
        $disciplines = $this->get_disciplines_from_api();
        $disciplineoptions = [];
        foreach ($disciplines as $discipline) {
            $disciplineoptions[$discipline['id_disciplina_instituicao']] = $discipline['nome_disciplina'];
        }
        $mform->addElement(
            'autocomplete',
            'disciplina',
            get_string('disciplina', 'mod_intersaberesplay'),
            $disciplineoptions,
            [
                'multiple' => false,
                'noselectionstring' => get_string('select', 'mod_intersaberesplay'),
            ]
        );
        $mform->addRule('disciplina', null, 'required', null, 'client');
        $mform->setType('disciplina', PARAM_INT);

        // Open in new tab option.
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

        // Load AMD JavaScript for dynamic updates.
        $PAGE->requires->js_call_amd('mod_intersaberesplay/filter_disciplines', 'init', [$disciplines]);

        // Standard course module elements.
        $this->standard_coursemodule_elements(false);

        // Action buttons.
        $this->add_action_buttons();
    }


    /**
     * Register demo institution if needed.
     *
     * @return void
     */
    private function register_demo_institution() {

        $token = get_config('mod_intersaberesplay', 'token');
        if (!empty($token)) {
            // Token already configured.
            return;
        }
        $baseurl = 'https://play.intersaberes.com';
        if (defined('INTERSABERES_PLAY_URL')) {
            $baseurl = INTERSABERES_PLAY_URL;
        }
        $apiurl = $baseurl . '/api/instituicao/cadastrar-instituicao-demo';

        // Avoid triggering Moodle blocked URL warnings in restricted environments.
        $host = (string)parse_url($apiurl, PHP_URL_HOST);
        if ($this->is_host_blocked_by_moodle($host)) {
            return;
        }

        global $SITE, $CFG;
        $data = [
            'site' => $SITE->fullname,
            'site_url' => $CFG->wwwroot,
            'support_name' => get_config('moodle', 'supportname'),
            'support_email' => get_config('moodle', 'supportemail'),
        ];
        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_SSL_VERIFYPEER' => strpos($apiurl, 'https://') === 0,
            'CURLOPT_TIMEOUT' => 60,
        ]);
        try {
            $response = $curl->post($apiurl, $data);
            $response = json_decode($response, true);
            if (!empty($response['token_plugin'])) {
                set_config('token', $response['token_plugin'], 'mod_intersaberesplay');
            }
        } catch (\Throwable $e) {
            // Do not block activity creation when outbound HTTP is restricted in QA.
            return;
        }
    }

    /**
     * Check whether the host is blocked by Moodle cURL security settings.
     *
     * @param string $host Target host.
     * @return bool
     */
    private function is_host_blocked_by_moodle(string $host): bool {
        global $CFG;

        if ($host === '' || empty($CFG->curlsecurityblockedhosts)) {
            return false;
        }

        $entries = preg_split('/[\s,;]+/', (string)$CFG->curlsecurityblockedhosts, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($entries as $entry) {
            $entry = trim(strtolower($entry));
            if ($entry === '') {
                continue;
            }

            $hostlower = strtolower($host);
            if ($hostlower === $entry || substr($hostlower, -strlen('.' . $entry)) === '.' . $entry) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get courses from API.
     *
     * @return array List of courses.
     */
    private function get_courses_from_api(): array {
        $options = [];
        $token = get_config('mod_intersaberesplay', 'token');
        if (empty($token)) {
            return $options;
        }

        $baseurl = 'https://play.intersaberes.com';
        if (defined('INTERSABERES_PLAY_URL')) {
            $baseurl = INTERSABERES_PLAY_URL;
        }

        $apiurl = $baseurl . '/api/instituicao/listar-cursos?token=' . urlencode($token);

        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_SSL_VERIFYPEER' => strpos($apiurl, 'https://') === 0,
        ]);

        $response = $curl->get($apiurl);

        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['lista'])) {
                foreach ($data['lista'] as $item) {
                    $options[$item['id_curso_instituicao']] = $item['nome_curso'];
                }
            }
        }

        return $options;
    }

    /**
     * Get disciplines from API.
     *
     * @param int|null $idcursoinstituicao Optional course institution ID.
     * @return array List of disciplines.
     */
    private function get_disciplines_from_api($idcursoinstituicao = null): array {
        $options = [];
        $token = get_config('mod_intersaberesplay', 'token');
        if (empty($token)) {
            return $options;
        }

        $baseurl = 'https://play.intersaberes.com';

        if (defined('INTERSABERES_PLAY_URL')) {
            $baseurl = INTERSABERES_PLAY_URL;
        }

        $apiurl = $baseurl . '/api/instituicao/listar-disciplinas?token=' . urlencode($token);
        if (!empty($idcursoinstituicao)) {
            $apiurl .= '&idCursoInstituicao=' . urlencode($idcursoinstituicao);
        }
        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_SSL_VERIFYPEER' => strpos($apiurl, 'https://') === 0,
        ]);
        $response = $curl->get($apiurl);

        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['lista'])) {
                return $data['lista'];
            }
        }
        return $options;
    }
}
