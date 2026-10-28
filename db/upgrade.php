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
 * Upgrade steps for mod_intersaberesplay.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute mod_intersaberesplay upgrade steps.
 *
 * @param int $oldversion Previous version.
 * @return bool
 */
function xmldb_intersaberesplay_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Version 2025111000 - Add course and discipline fields.
    if ($oldversion < 2025111000) {
        $table = new xmldb_table('intersaberesplay');

        // Add course field.
        if (!$dbman->field_exists($table, new xmldb_field('curso'))) {
            $field = new xmldb_field('curso', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $dbman->add_field($table, $field);
        }

        // Add discipline field.
        if (!$dbman->field_exists($table, new xmldb_field('disciplina'))) {
            $field = new xmldb_field('disciplina', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'curso');
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2025111000, 'intersaberesplay');
    }

    // Version 2025111300 - Add open in new tab flag.
    if ($oldversion < 2025111300) {
        $table = new xmldb_table('intersaberesplay');

        // New field: open in new tab.
        $field = new xmldb_field('targetblank', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2025111300, 'intersaberesplay');
    }

    // Version 2026072100 - Align base schema with module features and backup structure.
    if ($oldversion < 2026072100) {
        $table = new xmldb_table('intersaberesplay');

        // Standard Moodle module fields.
        $field = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'intro');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2026072100, 'intersaberesplay');
    }

    return true;
}
