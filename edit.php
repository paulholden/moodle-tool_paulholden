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

require_once(__DIR__ . '/../../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
require_login($course, false);

$context = context_course::instance($course->id);
require_capability('tool/paulholden:edit', $context);

$returnurl = new moodle_url('/admin/tool/paulholden/index.php', ['id' => $course->id]);

if ($id) {
    $record = $DB->get_record('tool_paulholden', ['courseid' => $course->id, 'id' => $id], '*', MUST_EXIST);

    if ($delete) {
        require_sesskey();

        $DB->delete_records('tool_paulholden', ['id' => $record->id]);

        redirect($returnurl);
    }
} else {
    $record = (object)[
        'courseid' => $course->id,
    ];
}

$mform = new tool_paulholden\form\edit();
$mform->set_data($record);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    if ($data->id) {
        $data->timemodified = time();

        $DB->update_record('tool_paulholden', $data);
    } else {
        $data->timecreated = $data->timemodified = time();

        $DB->insert_record('tool_paulholden', $data);
    }

    redirect($returnurl);
}

$url = new moodle_url('/admin/tool/paulholden/edit.php', ['courseid' => $course->id, 'id' => $id]);
$PAGE->set_url($url);

$stradd = get_string('edit');

$PAGE->set_title($stradd);
$PAGE->set_heading(get_string('pluginname', 'tool_paulholden'));

echo $OUTPUT->header();
echo $OUTPUT->heading($stradd);

$mform->display();

echo $OUTPUT->footer();
