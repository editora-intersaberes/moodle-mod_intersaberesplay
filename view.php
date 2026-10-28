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
 * View an Intersaberes Play instance.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_once('classes/local/play_service.php');
require_once('classes/event/course_module_viewed.php');


use mod_intersaberesplay\local\play_service;
use mod_intersaberesplay\event\course_module_viewed;

$id = required_param('id', PARAM_INT); // Course module ID.

$cm = get_coursemodule_from_id('intersaberesplay', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$intersaberesplay = $DB->get_record('intersaberesplay', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Check capability.
require_capability('mod/intersaberesplay:view', $context);

// Page header.
$PAGE->set_url('/mod/intersaberesplay/view.php', ['id' => $cm->id]);
$PAGE->set_title($intersaberesplay->name);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($intersaberesplay->name);

// Log view event.
$event = course_module_viewed::create([
    'objectid' => $intersaberesplay->id,
    'context' => $context,
]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('intersaberesplay', $intersaberesplay);
$event->trigger();

global $USER;
global $SITE;

// Data to send to the API.
$postdata = [
    'user_id' => $USER->id,
    'name' => fullname($USER),
    'email' => $USER->email,
    'user_name' => $USER->username,
    'support_name' => get_config('moodle', 'supportname'),
    'support_email' => get_config('moodle', 'supportemail'),
    'course_id' => $course->id,
    'course_name' => $course->fullname,
    'activity_id' => $cm->id,
    'activity_name' => $cm->name,
    'site' => $SITE->fullname,
    'site_url' => $CFG->wwwroot,
    'id_curso_instituicao' => $intersaberesplay->curso,
    'id_disciplina_instituicao' => $intersaberesplay->disciplina,
    'token' => get_config('mod_intersaberesplay', 'token'),
    'mostrar_em_nova_pagina' => $intersaberesplay->targetblank,
];

$playservice = new play_service();
$response = $playservice->access_via_plugin($postdata);

echo $response;

echo $OUTPUT->footer();
