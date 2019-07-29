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

defined('MOODLE_INTERNAL') || die();

use tool_paulholden\api;

/**
 * Tests for plugin API
 *
 * @package    tool_paulholden
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_paulholden_api_testcase extends advanced_testcase {

    /** @var stdClass test course. */
    private $course;

    /** @var stdClass test record. */
    private $record;

    /**
     * Test setup
     *
     * @return void
     */
    public function setUp() {
        $this->resetAfterTest(true);

        $this->course = $this->getDataGenerator()->create_course();

        $this->record = $this->getDataGenerator()->get_plugin_generator('tool_paulholden')->create_record([
            'courseid' => $this->course->id,
            'name' => 'Something',
            'completed' => 0,
        ]);
    }

    /**
     * Test get_record method
     *
     * @return void
     */
    public function test_get_record() {
        global $DB;

        $expected = $DB->get_record('tool_paulholden', ['id' => $this->record->id]);

        $this->assertEquals($expected, api::get_record($this->course->id, $this->record->id));
    }

    /**
     * Test insert_record method
     *
     * @return void
     */
    public function test_insert_record() {
        global $DB;

        $record = (object) [
            'courseid' => $this->course->id,
            'name' => 'Something new',
            'completed' => 0,
        ];

        $id = api::insert_record($record);
        $this->assertInternalType('int', $id);

        $actual = $DB->get_record('tool_paulholden', ['id' => $id]);
        $this->assertEquals($record->courseid, $actual->courseid);
        $this->assertEquals($record->name, $actual->name);
        $this->assertEquals($record->completed, $actual->completed);
    }

    /**
     * Test update_record method
     *
     * @return void
     */
    public function test_update_record() {
        global $DB;

        $this->record->name = 'Something else';
        $this->record->completed = 1;
        api::update_record($this->record);

        $actual = $DB->get_record('tool_paulholden', ['id' => $this->record->id]);
        $this->assertEquals($this->record->name, $actual->name);
        $this->assertEquals($this->record->completed, $actual->completed);
    }

    /**
     * Test delete_record method
     *
     * @return void
     */
    public function test_delete_record() {
        global $DB;

        api::delete_record($this->record->id);

        $this->assertFalse($DB->record_exists('tool_paulholden', ['id' => $this->record->id]));
    }
}
