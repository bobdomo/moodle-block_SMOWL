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
 * @package   quizaccess_quizoptions
 * @author    Manu Fraile Yarza (manuel.fraile@deusto.es)
 * @copyright Smiley Owl Tech S.L.
 * @contact   mikel.labayen@smowltech.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CSM;

$string['gen_settings_header'] = 'SMOWL options';
$string['gen_smowl_no'] = 'Not activate';
$string['gen_smowl_required'] = 'Enable facial recognition software';
$string['gen_smowl_required_help'] = 'If you enable this option, students identity will be verified using face recognition software';
$string['gen_smowl_required2_help'] = 'If you enable this option, students identity will be verified using face recognition software
<br>Parameters of facial recognition software:
<br>- Photos per minute: '.$CSM->quiz_media.'
<br>- % of variation: '.$CSM->quiz_varianza;
$string['gen_smowl_yes'] = 'Activate';
$string['gen_varianza_label'] = 'Variance at capturing time';
$string['gen_varianza_label_help'] = 'Variation that the software will apply to the photo capture interval';
$string['gen_varianza1'] = '10%';
$string['gen_varianza2'] = '15%';
$string['gen_varianza3'] = '20%';
$string['pluginname'] = 'SMOWL quiz options';
$string['quiz_media_label'] = 'Average number of pictures taken per minute';
$string['quiz_media_label_help'] = "Average number of pictures per minute that the software will take to verify user's identity";
$string['quiz_media1'] = '1 photo per minute';
$string['quiz_media2'] = '3 photos per minute';
$string['quiz_media3'] = '5 photos per minute';
$string['quiz_smowl_label'] = 'I have read and accept the previous message';
$string['quiz_smowl_message_header'] = 'Please, read the following message';
$string['quiz_smowl_mustagree'] = 'You must agree before starting the quiz';
$string['quiz_smowl_statement'] = 'The attempt I will perform will be verified using facial recognition';