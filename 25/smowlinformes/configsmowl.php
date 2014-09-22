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

unset($CSM);
global $CSM;
$CSM = new stdClass();

/** PARÁMETROS
 *
 * entity                = Nombre de la universidad
 * privileges_course     = Privilegios de edición de curso: - 1: Puede elegir media y varianza
														    - 2: Sólo puede elegir entre curso y master
 * privileges_quiz       = Privilegios de edición de quiz:  - 1: Puede elegir media y varianza
														    - 2: Sólo puede activar/desactivar SMOWL
 * SOAPorig              = Ruta del archivo Client.php -> ../xampp/php/pear/SOAP/Client.php
 * blockheight           = Altura del bloque de la cámara
 *
 * // sólo se utilizan si privileges_course=2
 * curso_media_grado     = Valor de la media de fotos por hora para un curso de grado
 * curso_media_master    = Valor de la media de fotos por hora para un curso de master
 * curso_varianza_grado  = Valor de la varianza en la captura para un curso de grado
 * curso_varianza_master = Valor de la varianza en la captura para un curso de master
 *
 * // sólo se utilizan si privileges_quiz=2
 * quiz_media            = Valor de la media de fotos por hora para un quiz
 * quiz_varianza         = Valor de la varianza en la captura para un quiz
 */

$CSM->entity                = 'MANU';
$CSM->privileges_course     = 1;
$CSM->privileges_quiz       = 1;
$CSM->SOAPorig              = 'C:/xampp/php/pear/SOAP/Client.php';
$CSM->blockheight           = 200;

$CSM->curso_media_grado     = 5;
$CSM->curso_media_master    = 10;
$CSM->curso_varianza_grado  = 15;
$CSM->curso_varianza_master = 20;

$CSM->quiz_media            = 5;
$CSM->quiz_varianza         = 10;