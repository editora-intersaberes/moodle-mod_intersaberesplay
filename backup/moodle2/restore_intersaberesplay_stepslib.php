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
 * Restore steps for the Intersaberes Play activity module.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore structure step class for the Intersaberes Play activity module.
 *
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_intersaberesplay_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define the structure to be restored.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('intersaberesplay', '/activity/intersaberesplay');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the data for the activity.
     *
     * @param array $data The data array.
     * @return void
     */
    protected function process_intersaberesplay($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->task->get_courseid();
        unset($data->id);

        // Insert the activity record and link it to the course module being restored.
        $newitemid = $DB->insert_record('intersaberesplay', $data);
        $this->apply_activity_instance($newitemid);

        // Immediately after inserting "activity" record, call this.
        $this->set_mapping('intersaberesplay', $oldid, $newitemid);
    }

    /**
     * Post execution actions.
     *
     * @return void
     */
    protected function after_execute() {
        // Add activity_complete_after_restore() call if needed.
    }
}
