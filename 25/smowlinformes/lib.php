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
 * @package   report_smowlinformes
 * @author    Manu Fraile Yarza (manuel.fraile@deusto.es)
 * @copyright Smiley Owl Tech S.L.
 * @contact   mikel.labayen@smowltech.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_smowlinformes_extend_navigation_course($navigation, $course, $context) 
{
    global $CFG, $OUTPUT, $PAGE;
	
    if (has_capability('report/smowlinformes:view', $context)) 
	{
        $url = new moodle_url('/report/smowlinformes/edit.php', array('id'=>$course->id, 'type'=>0, 'quizid'=>0, 'idalu'=>0, 'tiempo1'=>0, 'tiempo2'=>0));
		$navigation->add(get_string('plugintitle', 'report_smowlinformes'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('/smowl/buho', ''));
	}    
}