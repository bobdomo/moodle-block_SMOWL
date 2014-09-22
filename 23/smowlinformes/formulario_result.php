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

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
class formulario_result extends moodleform 
{
    //Add elements to form
    public function definition() 
	{
        global $CFG;
		global $DB;
		
		$id = required_param('id', PARAM_INT);
		$type = required_param('type', PARAM_INT);
		$quizid = required_param('quizid', PARAM_INT);
		$idalu = required_param('idalu', PARAM_INT);
		$tiempo1 = required_param('tiempo1', PARAM_INT);
		$tiempo2 = required_param('tiempo2', PARAM_INT);
		$prefix = $CFG->prefix;
 
        $mform = $this->_form;
		
//////////////////////////////////////////// CABECERA
		$mform->addElement('hidden', 'id', $this->_customdata['id']);
		$mform->addElement('hidden', 'type', $type);
		$mform->addElement('hidden', 'quizid', $quizid);
		$mform->addElement('hidden', 'idalu', $idalu);
		$mform->addElement('hidden', 'tiempo1', $tiempo1);
		$mform->addElement('hidden', 'tiempo2', $tiempo2);
		
		$mform->addElement('html', '<img src="http://smowltech.com/img/logo.png">');
		$mform->addElement('html', '<p> <br> </p>');
		$mform->addElement('header', 'res_title', get_string('res_title', 'report_smowlinformes'));
		
		$mform->setType('id', PARAM_INT);
		$mform->setType('type', PARAM_INT);
		$mform->setType('quizid', PARAM_INT);
		$mform->setType('idalu', PARAM_INT);
		$mform->setType('tiempo1', PARAM_INT);
		$mform->setType('tiempo2', PARAM_INT);
//////////////////////////////////////////// FIN CABECERA
		
//////////////////////////////////////////// LISTA MODALITY
		$aMod = array();
		$aMod[0] = get_string('res_modality_type1', 'report_smowlinformes');
		$aMod[1] = get_string('res_modality_type2', 'report_smowlinformes');
		
		$selListaMod = $mform->addElement('select', 'listaMod', get_string('res_modality', 'report_smowlinformes'), $aMod, array('onchange' => "M.core_formchangechecker.set_form_submitted(); this.form.submit()"));
		$mform->addHelpButton('listaMod', 'res_modality', 'report_smowlinformes');
		$selListaMod->setSelected($type);
//////////////////////////////////////////// FIN LISTA MODALITY

//////////////////////////////////////////// CURSO SELECCIONADO
		if($type == 0)
		{
//////////////////////////////////////////// LISTA DE ALUMNOS MATRICULADOS
			$aNombres = array();
			$aNombres['def'] = get_string('res_select_default', 'report_smowlinformes');
			$table1 = $prefix.'user_enrolments';
			$table2 = $prefix.'enrol';
			$table3 = $prefix.'role_assignments';
			$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $id) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
			$array = $DB->get_records_sql($sql);
			$uid = 0;
			foreach ($array as $valor)
			{
				$uid = $valor->userid;
				$table = $prefix.'user';
				$sql = "SELECT firstname FROM $table WHERE id=$valor->userid";
				$firstname = $DB->get_records_sql($sql);
				foreach ($firstname as $first)
				{
					$nombre = $first->firstname;
				}
				$table = $prefix.'user';
				$sql = "SELECT lastname FROM $table WHERE id=$valor->userid";
				$lastname = $DB->get_records_sql($sql);
				foreach ($lastname as $last)
				{
					$apellidos = $last->lastname;
				}
				$alumno = $nombre." ".$apellidos;
				$aNombres[$valor->userid] = $alumno;
			}
			if($uid == null)
			{
				$aNombres['novalue'] = get_string('res_no_matriculados', 'report_smowlinformes');
			}
			$selListaAlu = $mform->addElement('select', 'listaAlumnos', get_string('res_matriculados_list', 'report_smowlinformes'), $aNombres);
			$mform->addHelpButton('listaAlumnos', 'res_matriculados_list', 'report_smowlinformes');
			$selListaAlu->setSelected($idalu);
//////////////////////////////////////////// FIN LISTA DE ALUMNOS MATRICULADOS
			
//////////////////////////////////////////// LISTA DE MESES DEL CURSO
			$hoy = getdate();
			$courseStart = $DB->get_field('course', 'startdate', array('id' => $id));
			$comMes = date('n', $courseStart);
			$comAny = date('Y', $courseStart);
			$actMes = $hoy['mon'];
			$actAny = $hoy['year'];
			$aMeses = array();
			$aMeses['def'] = get_string('res_select_default', 'report_smowlinformes');
			if($comAny == $actAny)
			{
				for ($i = $comMes; $i <= $actMes; $i++)
				{
					$valor = $actAny.','.$i;
					$nombre = get_string('res_month'.$i, 'report_smowlinformes').' '.$actAny;
					$aMeses[$valor] = $nombre;
				}
			}
			else
			{
				$anyComienzo = $comAny;
				$fin = false;
				for ($i = $comMes; $i <= 12; $i++)
				{
					$valor = $comAny.','.$i;
					$nombre = get_string('res_month'.$i, 'report_smowlinformes').' '.$comAny;
					$aMeses[$valor] = $nombre;
				}
				while($fin==false)
				{
					$anyComienzo++;
					if($anyComienzo == $actAny)
					{
						for ($i = 1; $i <= $actMes; $i++)
						{
							$valor = $actAny.','.$i;
							$nombre = get_string('res_month'.$i, 'report_smowlinformes').' '.$actAny;
							$aMeses[$valor] = $nombre;
						}
						$fin = true;
					}
					else
					{
						for ($i = 1; $i <= 12; $i++)
						{
							$valor = $anyComienzo.','.$i;
							$nombre = get_string('res_month'.$i, 'report_smowlinformes').' '.$anyComienzo;
							$aMeses[$valor] = $nombre;
						}
					}
				}
			}
			$selListaMeses = $mform->addElement('select', 'listaMeses', get_string('res_months', 'report_smowlinformes'), $aMeses);
			$mform->addHelpButton('listaMeses', 'res_months', 'report_smowlinformes');
			$selListaMeses->setSelected($tiempo1.','.$tiempo2);
//////////////////////////////////////////// FIN LISTA DE MESES DEL CURSO
		}
//////////////////////////////////////////// FIN CURSO SELECCIONADO

//////////////////////////////////////////// QUIZ SELECCIONADO
		else if($type == 1)
		{
//////////////////////////////////////////// LISTA QUIZ DEL CURSO
			$aQuiz = array();
			$aQuiz['def'] = get_string('res_select_default', 'report_smowlinformes');
			$table = $prefix.'quiz';
			$sql = "SELECT DISTINCT id FROM $table WHERE course=$id";
			$array = $DB->get_records_sql($sql);
			foreach ($array as $quiz)
			{
				$qid = $quiz->id;
				$table = $prefix.'quiz';
				$sql = "SELECT name FROM $table WHERE id=$qid";
				$nombre = $DB->get_records_sql($sql);
				foreach ($nombre as $n)
				{
					$name = $n->name;
				}
				$aQuiz[$qid] = $name;
			}
			if($qid == null)
			{
				$aQuiz['novalue'] = get_string('res_no_quiz', 'report_smowlinformes');
			}
			$selListaQuiz = $mform->addElement('select', 'listaQuiz', get_string('res_quiz_list', 'report_smowlinformes'), $aQuiz, array('onchange' => "M.core_formchangechecker.set_form_submitted(); this.form.submit()"));
			$mform->addHelpButton('listaQuiz', 'res_quiz_list', 'report_smowlinformes');
			$selListaQuiz->setSelected($quizid);
//////////////////////////////////////////// FIN LISTA QUIZ DEL CURSO
			
			if($quizid != 0)
			{
//////////////////////////////////////////// LISTA ALUMNOS QUE HAN REALIZADO EL QUIZ
				$aNombres = array();
				$aNombres['def'] = get_string('res_select_default', 'report_smowlinformes');
				$table = $prefix.'quiz_grades';
				$sql = "SELECT DISTINCT userid FROM $table WHERE quiz=$quizid";
				$array = $DB->get_records_sql($sql);
				foreach ($array as $valor)
				{
					$uid = $valor->userid;
					$table = $prefix.'user';
					$sql = "SELECT firstname FROM $table WHERE id=$valor->userid";
					$firstname = $DB->get_records_sql($sql);
					foreach ($firstname as $first)
					{
						$nombre = $first->firstname;
					}
					$table = $prefix.'user';
					$sql = "SELECT lastname FROM $table WHERE id=$valor->userid";
					$lastname = $DB->get_records_sql($sql);
					foreach ($lastname as $last)
					{
						$apellidos = $last->lastname;
					}
					$alumno = $nombre." ".$apellidos;
					$aNombres[$valor->userid] = $alumno;
				}
				if($uid == null)
				{
					$aNombres['novalue'] = get_string('res_no_realizados', 'report_smowlinformes');
				}
				$selListaQuizR = $mform->addElement('select', 'listaQuizR', get_string('res_realizados', 'report_smowlinformes'), $aNombres);
				$mform->addHelpButton('listaQuizR', 'res_realizados', 'report_smowlinformes');
				$selListaQuizR->setSelected($idalu);
//////////////////////////////////////////// FIN LISTA ALUMNOS QUE HAN REALIZADO EL QUIZ

//////////////////////////////////////////// LISTA PERIODO DE TIEMPO
				$selListaMinutos = $mform->addElement('select', 'listaMinutos', get_string('res_minutes', 'report_smowlinformes'), 
						array(
							0 => get_string('res_select_default', 'report_smowlinformes'),
							5 => get_string('res_min1', 'report_smowlinformes'),
							15 => get_string('res_min2', 'report_smowlinformes'),
							30 => get_string('res_min3', 'report_smowlinformes'),
							60 => get_string('res_min4', 'report_smowlinformes'),
						));
				$mform->addHelpButton('listaMinutos', 'res_minutes', 'report_smowlinformes');
				$selListaMinutos->setSelected($tiempo1);
//////////////////////////////////////////// FIN LISTA PERIODO DE TIEMPO
			}
		}
//////////////////////////////////////////// FIN QUIZ SELECCIONADO
		
		$this->add_action_buttons($cancel = true, $submitlabel=get_string('res_button', 'report_smowlinformes'));
    }
    //Custom validation should be added here
    function validation($data, $files) 
	{
        return array();
    }
}