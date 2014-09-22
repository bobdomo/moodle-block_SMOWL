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

global $CFG;
global $PAGE;
global $DB;

require('../../config.php');
require_once('/configsmowl.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot.'/report/smowlinformes/formulario_result.php');

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

//////////////////////////////////////////// CÓDIGO

	$id = required_param('id', PARAM_INT); // course id.
	$type = optional_param('type', 0, PARAM_INT);
	$quizid = optional_param('quizid', 0, PARAM_INT);
	$idalu = optional_param('idalu', 0, PARAM_INT);
	$tiempo1 = optional_param('tiempo1', 0, PARAM_INT);
	$tiempo2 = optional_param('tiempo2', 0, PARAM_INT);
	
//////////////////////////////////////////// URL BARRA NAVEGACIÓN
	$url = new moodle_url('/report/smowlinformes/edit.php', array('id'=>$id, 'type'=>0, 'quizid'=>0, 'idalu'=>0, 'tiempo1'=>0, 'tiempo2'=>0));
	$PAGE->set_url($url);
	$PAGE->set_pagelayout('admin');

	if (!$course = $DB->get_record('course', array('id'=>$id))) 
	{
		print_error('invalidcourse');
	}

	require_login($course);
	$context = context_course::instance($course->id);
	require_capability('report/smowlinformes:view', $context);

	$strsmowl_options = get_string('res_settings_header', 'report_smowlinformes');

//////////////////////////////////////////// TÍTULO PESTAÑA
	$PAGE->set_title($course->shortname .': '. $strsmowl_options);

//////////////////////////////////////////// TÍTULO PÁGINA
	$PAGE->set_heading($course->fullname);

//////////////////////////////////////////// INSTANCIA DEL FORMULARIO
	$mform = new formulario_result(null, array('id' => $id));
	
//////////////////////////////////////////// ACCIONES DEL FORMULARIO
	if ($mform->is_cancelled()) 
	{
//////////////////////////////////////////// CANCEL
		$url = new moodle_url('/course/view.php?id='.$id);
		redirect($url);
	} 
	else if ($fromform = $mform->get_data()) 
	{
//////////////////////////////////////////// BUSCAR
		$type = $_POST['listaMod'];
		
		if($type == 0)
		{
			$idalu = $_POST['listaAlumnos'];
			$seleccion = $_POST['listaMeses'];
			$division = explode(",", $seleccion);
			$tiempo1 = $division[0];
			$tiempo2 = $division[1];
		}
		else
		{
			$quizid = $_POST['listaQuiz'];
			$idalu = $_POST['listaQuizR'];
			$tiempo1 = $_POST['listaMinutos'];
		}
		$url = new moodle_url('/report/smowlinformes/edit.php', array('id'=>$course->id, 'type'=>$type, 'quizid'=>$quizid, 'idalu'=>$idalu, 'tiempo1'=>$tiempo1, 'tiempo2'=>$tiempo2));
		redirect($url);
	}
	else 
	{
//////////////////////////////////////////// DISPLAY
		echo $OUTPUT->header();
		$mform->display();
//////////////////////////////////////////// CARGA IFRAME (si hay algún alumno seleccionado)
		if($idalu != 0 and $tiempo1 > 0)
		{
//////////////////////////////////////////// OBTENCIÓN DE DATOS
			$Entity = $CSM->entity;
			$idUser = $Entity.'_'.$idalu;
			if($type == 0)
			{
				$Modality = "course";
				$idCourse = $id;
				if($tiempo2<10 and $tiempo2>0)
				{
					$tiempo2 = '0'.$tiempo2;
				}
			}
			else
			{
				$Modality = "quiz";
				$idCourse = $quizid;
			}
//////////////////////////////////////////// FIN OBTENCIÓN DE DATOS
		
//////////////////////////////////////////// MOSTRAR IFRAME
			$url = 'https://smowltech.net/demo/SMOWL_Results/results_a_moodle.php?idUserTable=37&tiempo=Month&Modality=3&Course=3';
			//$url = "https://www.smowltech.net/SMOWL_Results2/processConsultMoodle.php?Entity=$Entity&Modality=$Modality&idCourse=$idCourse&idUser=$idUser&tiempo1=$tiempo1&tiempo2=$tiempo2";
			echo '<iframe id=smowl width=100% height=3050 src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
//////////////////////////////////////////// FIN MOSTRAR IFRAME
		}
//////////////////////////////////////////// FIN CARGA IFRAME
	}
//////////////////////////////////////////// FOOTER
	echo $OUTPUT->footer();