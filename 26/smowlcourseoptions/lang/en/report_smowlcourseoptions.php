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
 * @package   report_smowlcourseoptions
 * @author    Manu Fraile Yarza (manuel.fraile@deusto.es)
 * @copyright Smiley Owl Tech S.L.
 * @contact   mikel.labayen@smowltech.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CSM;

$string['pluginname'] = 'SMOWL course options';
$string['plugintitle'] = 'SMOWL course options';
$string['course_button'] = 'Save';
$string['course_media_label_help'] = "Average number of pictures per hour that the software will take to verify user's identity";
$string['course_media_label'] = 'Average number of pictures taken per hour';
$string['course_media1'] = '4 photos per hour';
$string['course_media2'] = '10 photos per hour';
$string['course_media3'] = '12 photos per hour';
$string['course_mod_header'] = 'Select in which modules activate facial recognition';
$string['course_type_label_help'] = 'Depending on the chosen type, the parameters of facial recognition software will be established.<br>- Degree:<br>* '.$CSM->curso_media_grado.' photos per hour and '.$CSM->curso_varianza_grado.'% of variation<br>- Master´s Degree:<br>* '.$CSM->curso_media_master.' photos per hour and '.$CSM->curso_varianza_master.'% of variation';
$string['course_type_label'] = 'Course type';
$string['course_type1'] = 'Degree';
$string['course_type2'] = 'Master´s Degree';
$string['gen_settings_header'] = 'SMOWL options';
$string['gen_smowl_no'] = 'Not activate';
$string['gen_smowl_required'] = 'Enable facial recognition software';
$string['gen_smowl_required_help'] = 'If you enable this option, students identity will be verified using face recognition software';
$string['gen_smowl_yes'] = 'Activate';
$string['gen_varianza_label'] = 'Variance at capturing time';
$string['gen_varianza_label_help'] = 'Variation that the software will apply to the photo capture interval';
$string['gen_varianza1'] = '10%';
$string['gen_varianza2'] = '15%';
$string['gen_varianza3'] = '20%';
$string['updateButton'] = "Update users in SMOWL's database";