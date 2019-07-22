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

/**
 * Execute upgrade steps between versions
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_tool_paulholden_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019072205) {

        // Define table tool_paulholden to be created.
        $table = new xmldb_table('tool_paulholden');

        // Adding fields to table tool_paulholden.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('priority', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table tool_paulholden.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for tool_paulholden.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Paulholden savepoint reached.
        upgrade_plugin_savepoint(true, 2019072205, 'tool', 'paulholden');
    }

    if ($oldversion < 2019072206) {

        // Define key courseid (foreign) to be added to tool_paulholden.
        $table = new xmldb_table('tool_paulholden');
        $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

        // Launch add key courseid.
        $dbman->add_key($table, $key);

        // Paulholden savepoint reached.
        upgrade_plugin_savepoint(true, 2019072206, 'tool', 'paulholden');
    }

    if ($oldversion < 2019072207) {

        // Define index courseidname (unique) to be added to tool_paulholden.
        $table = new xmldb_table('tool_paulholden');
        $index = new xmldb_index('courseidname', XMLDB_INDEX_UNIQUE, ['courseid', 'name']);

        // Conditionally launch add index courseidname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Paulholden savepoint reached.
        upgrade_plugin_savepoint(true, 2019072207, 'tool', 'paulholden');
    }

    return true;
}
