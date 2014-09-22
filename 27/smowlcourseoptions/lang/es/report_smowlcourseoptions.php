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
$string['plugintitle'] = 'Opciones SMOWL para curso';
$string['course_button'] = 'Guardar';
$string['course_media_label_help'] = 'Número medio de fotos que el software capturará por hora para verificar la identidad del usuario';
$string['course_media_label'] = 'Número medio de fotos capturadas por hora';
$string['course_media1'] = '4 fotos por hora';
$string['course_media2'] = '10 fotos por hora';
$string['course_media3'] = '12 fotos por hora';
$string['course_mod_header'] = 'Seleccionar en qué módulos activar el reconocimiento facial';
$string['course_type_label_help'] = 'En función del tipo elegido, se establecerán los parámetros del software de reconocimiento facial.<br>- Grado:<br>* '.$CSM->curso_media_grado.' fotos por hora y '.$CSM->curso_varianza_grado.'% de varianza<br>- Master:<br>* '.$CSM->curso_media_master.' fotos por hora y '.$CSM->curso_varianza_master.'% de varianza';
$string['course_type_label'] = 'Tipo de curso';
$string['course_type1'] = 'Grado';
$string['course_type2'] = 'Master';
$string['gen_settings_header'] = 'Opciones de SMOWL';
$string['gen_smowl_no'] = 'No activar';
$string['gen_smowl_required'] = 'Activar el reconocimiento facial';
$string['gen_smowl_required_help'] = 'Si activas esta opción, la identidad de los usuarios será verificada utilizando el software de reconocimiento facial';
$string['gen_smowl_yes'] = 'Activar';
$string['gen_varianza_label'] = 'Varianza del intervalo de captura';
$string['gen_varianza_label_help'] = 'Varianza que el software aplicará al intervalo de captura de fotos';
$string['gen_varianza1'] = '10%';
$string['gen_varianza2'] = '15%';
$string['gen_varianza3'] = '20%';
$string['updateButton'] = "Actualizar usuarios en base de datos de SMOWL";