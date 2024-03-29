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

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_login($course, false);

$context = context_course::instance($course->id);
require_capability('tool/paulholden:view', $context);

$url = new moodle_url('/admin/tool/paulholden/index.php', ['id' => $course->id]);
$PAGE->set_url($url);

$strhelloworld = get_string('helloworld', 'tool_paulholden', $course->id);

$PAGE->set_title($strhelloworld);
$PAGE->set_heading(get_string('pluginname', 'tool_paulholden'));

echo $OUTPUT->header();
echo $OUTPUT->heading($strhelloworld);

$table = new tool_paulholden\table($course->id);
$table->define_baseurl($PAGE->url);
$table->out(0, false);

if (has_capability('tool/paulholden:edit', $context)) {
    echo $OUTPUT->single_button(
        new moodle_url('/admin/tool/paulholden/edit.php', ['courseid' => $course->id]), get_string('add')
    );
}

echo $OUTPUT->footer();
