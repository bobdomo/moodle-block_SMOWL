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

$string['gen_settings_header'] = 'Opciones de SMOWL';
$string['gen_smowl_no'] = 'No activar';
$string['gen_smowl_required'] = 'Activar el reconocimiento facial';
$string['gen_smowl_required_help'] = 'Si activas esta opción, la identidad de los usuarios será verificada utilizando el software de reconocimiento facial';
$string['gen_smowl_required2_help'] = 'Si activas esta opción, la identidad de los usuarios será verificada utilizando el software de reconocimiento facial
<br>Parámetros del software de reconocimiento facial:
<br>- Fotos por minúto: '.$CSM->quiz_media.'
<br>- % de variación: '.$CSM->quiz_varianza;
$string['gen_smowl_yes'] = 'Activar';
$string['gen_varianza_label'] = 'Varianza del intervalo de captura';
$string['gen_varianza_label_help'] = 'Varianza que el software aplicará al intervalo de captura de fotos';
$string['gen_varianza1'] = '10%';
$string['gen_varianza2'] = '15%';
$string['gen_varianza3'] = '20%';
$string['pluginname'] = 'SMOWL quiz options';
$string['quiz_media_label'] = 'Número medio de fotos capturadas por minuto';
$string['quiz_media_label_help'] = 'Número medio de fotos que el software capturará por minuto para verificar la identidad del usuario';
$string['quiz_media1'] = '1 foto por minuto';
$string['quiz_media2'] = '3 fotos por minuto';
$string['quiz_media3'] = '5 fotos por minuto';
$string['quiz_smowl_label'] = 'He leído y acepto el mensaje anterior';
$string['quiz_smowl_message_header'] = 'Por favor, lee el siguiente mensaje';
$string['quiz_smowl_mustagree'] = 'Debes aceptar antes de empezar el quiz';
$string['quiz_smowl_statement'] = 'El intento que voy a realizar, será verificado utilizando reconocimiento facial';