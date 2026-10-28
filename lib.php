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
// @copyright 2026 Editora Intersaberes.
// @license https://www.gnu.org/licenses/gpl-3.0.txt GNU GPL v3 or later.

/**
 * Library of interface functions and constants.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/intersaberesplay/classes/local/play_service.php');
use mod_intersaberesplay\local\play_service;

/**
 * Add a new intersaberesplay activity instance.
 *
 * @param stdClass $data The data from the form.
 * @param stdClass $mform The form instance.
 * @return int The ID of the created instance.
 */
function intersaberesplay_add_instance($data, $mform = null) {
    global $DB;

    // Add timestamps.
    $data->timecreated = time();
    $data->timemodified = time();

    // Store owning course.
    if (!isset($data->course)) {
        $data->course = 0;
    }

    // Ensure that custom fields exist.
    if (!isset($data->curso)) {
        $data->curso = 0;
    }
    if (!isset($data->disciplina)) {
        $data->disciplina = 0;
    }

    if (empty($data->targetblank)) {
        $data->targetblank = 0;
    }

    // Insert record and return the ID.
    $data->id = $DB->insert_record('intersaberesplay', $data);

    // Note: intersaberesplay_grade_item_update($data) is disabled.

    return $data->id;
}

/**
 * Update an intersaberesplay activity instance.
 *
 * @param stdClass $data The data from the form.
 * @param stdClass $mform The form instance.
 * @return bool True if successful.
 */
function intersaberesplay_update_instance($data, $mform = null) {
    global $DB;

    // Update timestamps.
    $data->timemodified = time();

    // Moodle sends the "instance" field in the form when editing.
    $data->id = $data->instance;

    // Ensure that custom fields exist.
    if (!isset($data->curso)) {
        $data->curso = 0;
    }
    if (!isset($data->disciplina)) {
        $data->disciplina = 0;
    }

    if (empty($data->targetblank)) {
        $data->targetblank = 0;
    }

    // Note: intersaberesplay_grade_item_update($data) is disabled.

    // Update the record.
    return $DB->update_record('intersaberesplay', $data);
}

/**
 * Creates a record in mdl_grade_items if it does not exist.
 *
 * @param stdClass $intersaberesplay The activity instance.
 * @param mixed $grades The grades.
 * @return bool|int Result of grade_update.
 */
function intersaberesplay_grade_item_update($intersaberesplay, $grades = null) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $playservice = new play_service();
    $response = $playservice->get_maximum_test_weight([
        'id_curso_instituicao' => $intersaberesplay->curso ?? null,
        'id_disciplina_instituicao' => $intersaberesplay->disciplina ?? null,
        'token' => get_config('mod_intersaberesplay', 'token'),
    ]);
    $response = json_decode($response, true);

    $params = [
        'itemname' => $intersaberesplay->name,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax' => $response['pesoProva'],
        'grademin' => 0,
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

/**
 * Check whether the intersaberesplay module supports a feature.
 *
 * @param string $feature The feature.
 * @return bool|null True/false if the feature is supported, null if not.
 */
function intersaberesplay_supports($feature) {
    switch ($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        default:
            return null;
    }
}

/**
 * Delete an intersaberesplay activity instance.
 *
 * @param int $id The ID of the module instance.
 * @return bool True if successful.
 */
function intersaberesplay_delete_instance($id) {
    global $DB;

    // Check if the record exists.
    if (!$record = $DB->get_record('intersaberesplay', ['id' => $id])) {
        return false;
    }

    // Delete related data if any.
    // Example: $DB->delete_records('intersaberesplay_somechildtable', ['intersaberesplayid' => $id]).

    // Delete the main instance.
    $DB->delete_records('intersaberesplay', ['id' => $id]);

    return true;
}
