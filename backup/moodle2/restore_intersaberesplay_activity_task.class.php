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
 * Restore task class for the Intersaberes Play activity module.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/intersaberesplay/backup/moodle2/restore_intersaberesplay_stepslib.php');

/**
 * Restore task class for the Intersaberes Play activity module.
 *
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_intersaberesplay_activity_task extends restore_activity_task {
    /**
     * Define (add) particular settings this activity can have.
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Define (add) particular steps this activity can have.
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new restore_intersaberesplay_activity_structure_step('intersaberesplay_structure', 'intersaberesplay.xml'));
    }

    /**
     * Define the contents in the activity that must be processed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_contents() {
        return [];
    }

    /**
     * Define the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_rules() {
        return [];
    }
}
