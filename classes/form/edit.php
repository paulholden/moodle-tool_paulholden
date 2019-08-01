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

namespace tool_paulholden\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class for editing form
 *
 * @package    tool_paulholden
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit extends \moodleform {

    /**
     * Editor options
     *
     * @return array
     */
    public static function editor_options() {
        global $PAGE;

        return [
            'context' => $PAGE->context,
            'maxfiles' => -1,
            'maxbytes' => 0,
        ];
    }

    /**
     * Form definition
     *
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_NOTAGS);

        $mform->addElement('editor', 'description_editor', get_string('description'), null,
            self::editor_options());
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('advcheckbox', 'completed', get_string('completed', 'tool_paulholden'));

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Name must be unique to the course.
        $select = 'courseid = :courseid AND name = :name AND id <> :id';
        $params = [
            'courseid' => $data['courseid'],
            'name' => $data['name'],
            'id' => $data['id'],
        ];

        if ($DB->record_exists_select('tool_paulholden', $select, $params)) {
            $errors['name'] = get_string('errorduplicatename', 'tool_paulholden');
        }

        return $errors;
    }
}
