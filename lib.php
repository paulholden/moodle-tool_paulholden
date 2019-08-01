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

use tool_paulholden\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Extend course administration menu
 *
 * @param navigation_node $parentnode
 * @param stdClass $course
 * @param context_course $context
 * @return void
 */
function tool_paulholden_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    $strpluginname = get_string('pluginname', 'tool_paulholden');

    if (has_capability('tool/paulholden:view', $context)) {
        $parentnode->add(
            $strpluginname,
            new moodle_url('/admin/tool/paulholden/index.php', ['id' => $course->id]),
            navigation_node::TYPE_SETTING,
            $strpluginname,
            'paulholden',
            new pix_icon('icon', $strpluginname, 'tool_paulholden')
        );
    }
}

/**
 * Serve plugin files
 *
 * @param stdClass $course
 * @param stdClass|null $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool|null
 */
function tool_paulholden_pluginfile(stdClass $course, ?stdClass $cm, context $context, string $filearea, array $args, bool $forcedownload, array $options = []) : ?bool {
    if (!$context instanceof context_course) {
        return false;
    }

    require_login($course);
    require_capability('tool/paulholden:view', $context);

    if (strcmp($filearea, 'attachment') != 0) {
        return false;
    }

    list($id, $filename) = $args;

    $record = api::get_record($course->id, $id);

    // Retrieve the file from the Files API.
    $file = get_file_storage()->get_file($context->id, 'tool_paulholden', $filearea, $record->id, '/', $filename);
    if (!$file or $file->is_directory()) {
        return false;
    }

    // Finally send the file.
    \core\session\manager::write_close();
    send_stored_file($file, null, 0, true, $options);
}