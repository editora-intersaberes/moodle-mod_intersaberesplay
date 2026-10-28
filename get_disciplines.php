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
 * AJAX endpoint to get disciplines from the API.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_login();
require_sesskey();

$requestedcourseid = optional_param('courseid', 0, PARAM_INT);
$requestedcourseid = $requestedcourseid ?: (isset($COURSE->id) ? (int)$COURSE->id : 0);
$context = $requestedcourseid ? context_course::instance($requestedcourseid) : context_system::instance();
require_capability('mod/intersaberesplay:addinstance', $context);

$courseid = optional_param('idCursoInstituicao', 0, PARAM_INT);
$token = get_config('mod_intersaberesplay', 'token');

header('Content-Type: application/json');

if (empty($token)) {
    echo json_encode([]);
    exit;
}

$baseurl = 'https://play.intersaberes.com';
if (defined('INTERSABERES_PLAY_URL')) {
    $baseurl = INTERSABERES_PLAY_URL;
}

$apiurl = $baseurl . '/api/instituicao/listar-disciplinas?token=' . urlencode($token);
if ($courseid) {
    $apiurl .= '&idCursoInstituicao=' . urlencode($courseid);
}

// Use Moodle's curl class for better compatibility.
$curl = new \curl();
$curl->setopt([
    'CURLOPT_SSL_VERIFYPEER' => strpos($apiurl, 'https://') === 0,
]);

$response = $curl->get($apiurl);
$data = json_decode($response, true);
if (!is_array($data)) {
    $data = [];
}
echo json_encode($data['lista'] ?? []);
