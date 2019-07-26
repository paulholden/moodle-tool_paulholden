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
use moodle_url;
use pix_icon;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class for displaying table records
 *
 * @package    tool_paulholden
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table extends \table_sql {

    /** @var context_course Course context */
    protected $context;

    /**
     * Constructor
     *
     * @param int $courseid
     */
    public function __construct($courseid) {
        parent::__construct('tool-paulholden-table');

        $this->context = context_course::instance($courseid);

        // Define columns.
        $columns = [
            'name' => get_string('name'),
            'completed' => get_string('completed', 'tool_paulholden'),
            'priority' => get_string('priority', 'tool_paulholden'),
            'timecreated' => get_string('timecreated', 'tool_paulholden'),
            'timemodified' => get_string('timemodified', 'tool_paulholden'),
        ];
        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));

        // Table configuration.
        $this->set_attribute('class', $this->attributes['class'] .' tool-paulholden-table');
        $this->sortable(true, 'timecreated', SORT_DESC);

        // Initialize table SQL properties.
        $this->set_sql('*', '{tool_paulholden}', 'courseid = ?', [$courseid]);
        $this->set_count_sql('SELECT COUNT(1) FROM {tool_paulholden} WHERE courseid = ?', [$courseid]);
    }

    /**
     * Format record name column
     *
     * @param stdClass $record
     * @return string
     */
    protected function col_name(stdClass $record) {
        global $OUTPUT;

        $icons = [];

        if (has_capability('tool/paulholden:edit', $this->context)) {
            $icons[] = $OUTPUT->action_icon(
                new moodle_url('/admin/tool/paulholden/edit.php', [
                    'courseid' => $record->courseid,
                    'id' => $record->id,
                ]),
                new pix_icon('t/edit', get_string('edit'), 'moodle')
            );

            $icons[] = $OUTPUT->action_icon(
                new moodle_url('/admin/tool/paulholden/edit.php', [
                    'courseid' => $record->courseid,
                    'id' => $record->id,
                    'delete' => 1,
                    'sesskey' => sesskey(),
                ]),
                new pix_icon('t/delete', get_string('delete'), 'moodle')
            );
        }

        return implode('', $icons) . format_string($record->name, true, $this->context);
    }

    /**
     * Format record completed column
     *
     * @param stdClass $record
     * @return string
     */
    protected function col_completed(stdClass $record) {
        return $record->completed ? get_string('yes') : get_string('no');
    }

    /**
     * Format record priority column
     *
     * @param stdClass $record
     * @return string
     */
    protected function col_priority(stdClass $record) {
        return $record->priority ? get_string('yes') : get_string('no');
    }

    /**
     * Format record timecreated column
     *
     * @param stdClass $record
     * @return string
     */
    protected function col_timecreated(stdClass $record) {
        $format = get_string('strftimedatetime', 'langconfig');

        return userdate($record->timecreated, $format);
    }

    /**
     * Format record timemodified column
     *
     * @param stdClass $record
     * @return string
     */
    protected function col_timemodified(stdClass $record) {
        $format = get_string('strftimedatetime', 'langconfig');

        return userdate($record->timemodified, $format);
    }
}
