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
 * Uninstall script for intersaberesplay module.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to execute during module uninstall.
 *
 * @package    mod_intersaberesplay
 * @return bool
 */
function xmldb_intersaberesplay_uninstall() {
    global $DB;

    // Delete related data if any exists.
    // Example child table cleanup:
    // $DB->delete_records('intersaberesplay_childtable', []).

    // Delete module data (Moodle usually removes module tables automatically).
    $DB->delete_records('intersaberesplay', []);

    return true;
}
