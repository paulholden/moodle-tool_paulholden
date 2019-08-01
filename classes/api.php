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
 * @package    tool_paulholden
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_paulholden;

use context_course;
use stdClass;
use tool_paulholden\form\edit as edit_form;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for plugin storage methods
 *
 * @package    tool_paulholden
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /** Plugin table name. */
    const TABLE = 'tool_paulholden';

    /**
     * Get record
     *
     * @param int $courseid
     * @param int $id
     * @return stdClass
     */
    public static function get_record(int $courseid, int $id) : stdClass {
        global $DB;

        return $DB->get_record(self::TABLE, ['courseid' => $courseid, 'id' => $id], '*', MUST_EXIST);
    }

    /**
     * Insert record
     *
     * @param stdClass $record
     * @return int
     */
    public static function insert_record(stdClass $record) : int {
        global $DB;

        $record->timecreated = $record->timemodified = time();
        $record->id = $DB->insert_record(self::TABLE, $record);

        // We now need to update the record to ensure files attached to the editor element are processed.
        self::update_record($record);

        return $record->id;
    }

    /**
     * Update record
     *
     * @param stdClass $record
     * @return bool
     */
    public static function update_record(stdClass $record) : bool {
        global $DB;

        // Prepare content of the description editor element to be saved to database.
        if (isset($record->description_editor)) {
            $context = context_course::instance($record->courseid);
            $record = file_postupdate_standard_editor($record, 'description', edit_form::editor_options(), $context,
                'tool_paulholden', 'attachment', $record->id);
        }

        $record->timemodified = time();

        return $DB->update_record(self::TABLE, $record);
    }

    /**
     * Delete record
     *
     * @param int $id
     * @return bool
     */
    public static function delete_record(int $id) : bool {
        global $DB;

        return $DB->delete_records(self::TABLE, ['id' => $id]);
    }
}
