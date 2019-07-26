@tool @tool_paulholden
Feature: A teacher can manage plugin entries
  In order to manage plugin entries
  As a teacher
  I need to add, edit and remove plugin entries

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | Jones    | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  Scenario: Add a new entry
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "My first Moodle plugin" in current page administration
    And I click on "Add" "button"
    And I set the following fields to these values:
      | Name      | Hello |
      | Completed | 0     |
    And I click on "Save changes" "button"
    Then the following should exist in the "tool-paulholden-table" table:
      | Name  | Completed |
      | Hello | No        |
    And I log out
