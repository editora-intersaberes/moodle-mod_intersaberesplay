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
 * Backup steps for the Intersaberes Play activity module.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Backup structure step class for the Intersaberes Play activity module.
 *
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_intersaberesplay_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the structure of the activity to be backed up.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        // Define each element separated.
        $activity = new backup_nested_element(
            'intersaberesplay',
            ['id'],
            [
                'name',
                'intro',
                'introformat',
                'curso',
                'disciplina',
                'targetblank',
                'timemodified',
                'timecreated',
            ]
        );
        // Build the tree.
        $activity->set_source_table('intersaberesplay', ['id' => backup::VAR_ACTIVITYID]);
        return $this->prepare_activity_structure($activity);
    }
}
