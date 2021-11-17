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

declare(strict_types=1);

namespace core_user\reportbuilder\datasource;

use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_course\local\entities\course_enrolments;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\database;

/**
 * Users datasource
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('users');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $CFG;

        $userentity = new user();
        $usertablealias = $userentity->get_table_alias('user');

        $this->set_main_table('user', $usertablealias);

        $userparamguest = database::generate_param_name();
        $this->add_base_condition_sql("{$usertablealias}.id != :{$userparamguest} AND {$usertablealias}.deleted = 0", [
            $userparamguest => $CFG->siteguest,
        ]);

        // Add all columns from entities to be available in custom reports.
        $this->add_entity($userentity);

        $userentityname = $userentity->get_entity_name();
        $this->add_columns_from_entity($userentityname);
        $this->add_filters_from_entity($userentityname);
        $this->add_conditions_from_entity($userentityname);

        // Add course enrolment entity.
        $enrolmententity = new course_enrolments();
        $uetablealias = $enrolmententity->get_table_alias('user_enrolments');
        $enrolalias = $enrolmententity->get_table_alias('enrol');

        // Join enrolments entity to users entity.
        $userenrolmentjoin = "JOIN {user_enrolments} {$uetablealias}
                              ON {$uetablealias}.userid = {$usertablealias}.id";

        $this->add_entity($enrolmententity->add_join($userenrolmentjoin));

        $enrolmententityname = $enrolmententity->get_entity_name();
        $this->add_columns_from_entity($enrolmententityname);
        $this->add_filters_from_entity($enrolmententityname);
        $this->add_conditions_from_entity($enrolmententityname);

        // Add course entity.
        $courseentity = new course();
        $coursetablealias = $courseentity->get_table_alias('course');
        $coursejoin = "JOIN {course} {$coursetablealias} ON {$coursetablealias}.id = {$enrolalias}.courseid";

        $this->add_entity($courseentity->add_join($coursejoin));
        $courseentityname = $courseentity->get_entity_name();
        $this->add_columns_from_entity($courseentityname);
        $this->add_filters_from_entity($courseentityname);
        $this->add_conditions_from_entity($courseentityname);
    }

    /**
     * Return the columns that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }
}
