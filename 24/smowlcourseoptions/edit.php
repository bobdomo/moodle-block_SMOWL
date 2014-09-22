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
 
global $CFG;
global $PAGE;
global $CSM;

require('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot.'/report/smowlinformes/configsmowl.php');
require_once($CFG->dirroot.'/report/smowlcourseoptions/formulario_curso.php');
require_once("$CSM->SOAPorig");

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

// CÓDIGO

	$id = required_param('id', PARAM_INT); // course id.
	$prefix = $CFG->prefix;
	$urlCourse = new moodle_url('/course/view.php?id='.$id);

//////////////////////////////////////////// URL BARRA NAVEGACIÓN
	$url = new moodle_url('/report/smowlcourseoptions/edit.php', array('id'=>$id));
	$PAGE->set_url($url);
	$PAGE->set_pagelayout('admin');

	if (!$course = $DB->get_record('course', array('id'=>$id))) 
	{
		print_error('invalidcourse');
	}

	require_login($course);
	$context = context_course::instance($course->id);
	require_capability('report/smowlcourseoptions:view', $context);
	
	$strsmowl_options = get_string('gen_settings_header', 'report_smowlcourseoptions');

//////////////////////////////////////////// TÍTULO PESTAÑA
	$PAGE->set_title($course->shortname .': '. $strsmowl_options);

//////////////////////////////////////////// TÍTULO PÁGINA
	$PAGE->set_heading($course->fullname);
	
//////////////////////////////////////////// INSTANCIA DEL FORMULARIO
	$mform = new formulario_curso(null, array('id' => $id));	
	
//////////////////////////////////////////// ACCIONES DEL FORMULARIO
	if ($mform->is_cancelled()) 
	{ 
		redirect($urlCourse);
	}
	else if ($mform->no_submit_button_pressed()) 
	{
		updateUsers();
	}
	else if ($fromform = $mform->get_data()) 
	{
		if (empty($fromform->activar))
		{
			if ($DB->record_exists('options_smowl_course', array('courseid' => $id))) 
			{
				delete();
			}
			else
			{
				redirect($urlCourse);
			}
        }
		else
		{ 			
            if (!$DB->record_exists('options_smowl_course', array('courseid' => $id))) 
			{
				insert();
            }
			else
			{
				update();
            }
        }
	}
	else 
	{ // DISPLAY
		echo $OUTPUT->header();
		$mform->display();
	}
	// FOOTER
	echo $OUTPUT->footer();
	
//////////////////////////////////////////// FUNCIONES
	function delete()
	{
		global $DB, $CFG, $CSM;
		
		$id = required_param('id', PARAM_INT); // course id.
		$prefix = $CFG->prefix;
		$urlCourse = new moodle_url('/course/view.php?id='.$id);
		
//////////////////////////////////////////// OBTENCIÓN DE LOS ID´S DE LOS BLOQUES QUE HAY QUE BORRAR
		$arrayInstancias  = $DB->get_field('options_smowl_course', 'block_instance_id', array('courseid' => $id));
		
//////////////////////////////////////////// OBTENCIÓN DEL TIPO DE CURSO
		$courseType  = $DB->get_field('options_smowl_course', 'course_type', array('courseid' => $id));
			
//////////////////////////////////////////// BORRAR TABLA SMOWL
		$DB->delete_records('options_smowl_course', array('courseid' => $id));
			
//////////////////////////////////////////// BORRAR BLOQUES DEL CURSO
		$instancias = explode(",", $arrayInstancias);
		for ($i = 0; $i < count($instancias); $i++)
		{
			$DB->delete_records('block_instances', array('id' => $instancias[$i]));
		}
//////////////////////////////////////////// LLAMADA A WEB SERVICE
		$table1 = $prefix."user_enrolments";
		$table2 = $prefix."enrol";
		$table3 = $prefix."role_assignments";
		$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $id) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
		$array = $DB->get_records_sql($sql);
		$str = "";
		foreach ($array as $userid) 
		{
			if($str == "")
			{
				$str = $userid->userid;
			}
			else
			{
				$str = $str.','.$userid->userid;
			}
		}
		
		$modality = '';
		switch($CSM->privileges_course)
		{
			case 1:
				$modality = 'course';
				break;
			case 2:
				switch($courseType)
				{
					case 1:
						$modality = 'grado';
						break;
					case 2:
						$modality = 'master';
						break;
				}
				break;
		}
		
		$idEntity = $CSM->entity;
		$idModality = $modality;
		$idCourse = $id;
		$aUser = $str;
			
		/* llamada a función DELETE */
		$sw = new SOAP_WSDL("http://www.smowltech.net/WS_SMOWL_MOODLE/WS_SMOWL_MOODLE_DEMO.php?wsdl"); 
		$proxy = $sw->getProxy();
		$erg = $proxy->Delete($idEntity, $idModality, $idCourse, $aUser);
		
//////////////////////////////////////////// COMPROBACIÓN DATOS
		if($erg == 0)
		{
//////////////////////////////////////////// PAGINA ERROR
			$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
			redirect($urlRed);
		}
		else
		{
//////////////////////////////////////////// VUELTA AL CURSO
			redirect($urlCourse);
		}
	}

	function insert()
	{
		global $DB, $CFG, $CSM;
		
		$id = required_param('id', PARAM_INT); // course id.
		$prefix = $CFG->prefix;
		$urlCourse = new moodle_url('/course/view.php?id='.$id);

//////////////////////////////////////////// OBTENCIÓN DE PARENTCONTEXTID
		$pcontext = $DB->get_field('context', 'id', array('contextlevel' => 50, 'instanceid' => $id));
		
//////////////////////////////////////////// CAMBIO DEL DEFAULTWEIGHT DEL BLOQUE QUE TENGA 0
		$table = $prefix.'block_instances';
		$sql = "SELECT id FROM $table WHERE parentcontextid=$pcontext AND defaultregion='side-post' AND defaultweight=0";
		if($DB->record_exists_sql($sql)==TRUE)
		{
			$table = $prefix.'block_instances';
			$sql2 = "UPDATE $table SET defaultweight=1 WHERE parentcontextid=$pcontext AND defaultregion='side-post' AND defaultweight=0";
			$DB->execute($sql2);
		}
		
//////////////////////////////////////////// CREACIÓN DE LA INSTANCIA DEL BLOQUE EN EL CURSO
		$record = new stdClass();
		$record->blockname = 'bloquecurso';
		$record->parentcontextid = $pcontext;
		$record->showinsubcontexts = 0;
		$record->pagetypepattern = 'course-view-*';
		$record->defaultregion = 'side-post';
		$record->defaultweight = 0;
		$DB->insert_record('block_instances', $record);
				
//////////////////////////////////////////// CREACIÓN DE LAS INSTANCIAS DEL BLOQUE EN OTROS MÓDULOS
		$table1 = $prefix.'modules';
		$table2 = $prefix.'course_modules';
		$sql = "SELECT name FROM $table1 WHERE id IN (SELECT DISTINCT module FROM $table2 WHERE course=$id AND module!=16)";
		$array = $DB->get_records_sql($sql);
		foreach ($array as $name) 
		{
			$val = $_POST['check'.$name->name];
			if($val == 1)
			{
				$record = new stdClass();
				$record->blockname = 'bloquecurso';
				$record->parentcontextid = $pcontext;
				$record->showinsubcontexts = 1;
				$record->pagetypepattern = 'mod-'.$name->name.'-*';
				$record->defaultregion = 'side-post';
				$record->defaultweight = 0;
				$DB->insert_record('block_instances', $record);
			}
		}
				
//////////////////////////////////////////// OBTENCIÓN DEL ID DE LAS INSTANCIAS DEL BLOQUE
		$str = "";
		$table = $prefix.'block_instances';
		$sql = "SELECT id FROM $table WHERE blockname='bloquecurso' AND parentcontextid=$pcontext AND defaultweight=0";
		$array = $DB->get_records_sql($sql);
		foreach ($array as $valor)
		{
			if ($str == "")
			{
				$str = $valor->id;
			}
			else
			{
				$str = $str.",".$valor->id;
			}
		}
				
//////////////////////////////////////////// ALMACENAMIENTO DE LOS DATOS DE SMOWL
		$record = new stdClass();
		$record->courseid = $id;
		$record->smowlcourserequired = 1;
//////////////////////////////////////////// COMPROBACIÓN DE PRIVILEGIOS DE EDICIÓN
		switch ($CSM->privileges_course)
		{
			case 1:
				switch ($_POST['media']) 
				{
					case 0:
						$media = 4;
						break;
					case 1:
						$media = 10;
						break;
					case 2:
						$media = 12;
						break;
				}
				switch ($_POST['varianza']) 
				{
					case 0:
						$varianza = 10;
						break;
					case 1:
						$varianza = 15;
						break;
					case 2:
						$varianza = 20;
						break;
				}
				$record->smowlcoursemedia = $media;
				$record->smowlcoursevarianza = $varianza;
				$record->course_type = '';
				break;
			case 2:
				switch ($_POST['tipo']) 
				{
					case 1:
						$media = $CSM->curso_media_grado;
						$varianza = $CSM->curso_varianza_grado;
						break;
					case 2:
						$media = $CSM->curso_media_master;
						$varianza = $CSM->curso_varianza_master;
						break;
				}
				$record->smowlcoursemedia = $media;
				$record->smowlcoursevarianza = $varianza;
				$record->course_type = $_POST['tipo'];
				break;
		}
		$record->block_instance_id = $str;
		$DB->insert_record('options_smowl_course', $record);
				
//////////////////////////////////////////// LLAMADA A WEB SERVICE
		$table1 = $prefix."user_enrolments";
		$table2 = $prefix."enrol";
		$table3 = $prefix."role_assignments";
		$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $id) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
		$array = $DB->get_records_sql($sql);
		$str = "";
		foreach ($array as $userid) 
		{
			if($str == "")
			{
				$str = $userid->userid;
			}
			else
			{
				$str = $str.','.$userid->userid;
			}
		}
		$modality = '';
		switch($CSM->privileges_course)
		{
			case 1:
				$modality = 'course';
				break;
			case 2:
				switch($_POST['tipo'])
				{
					case 1:
						$modality = 'grado';
						break;
					case 2:
						$modality = 'master';
						break;
				}
				break;
		}
		
		$idEntity = $CSM->entity;
		$idModality = $modality;
		$idCourse = $id;
		$aUser = $str;
		$valMedia = $media;
		$valVarianza = $varianza;
		
		/* llamada a función INSERT */
		$sw = new SOAP_WSDL("http://www.smowltech.net/WS_SMOWL_MOODLE/WS_SMOWL_MOODLE_DEMO.php?wsdl");
		$proxy = $sw->getProxy ();
		$erg = $proxy->Insert($idEntity, $idModality, $idCourse, $aUser, $valMedia, $valVarianza);
				
//////////////////////////////////////////// COMPROBACIÓN DATOS
		if($erg == 0)
		{
//////////////////////////////////////////// PAGINA ERROR
			$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
			redirect($urlRed);
		}
		else
		{
//////////////////////////////////////////// VUELTA AL CURSO
			redirect($urlCourse);
		}
	}
	
	function update()
	{
		global $DB, $CFG, $CSM;
		
		$id = required_param('id', PARAM_INT); // course id.
		$prefix = $CFG->prefix;
		$urlCourse = new moodle_url('/course/view.php?id='.$id);
		
		$medVieja = $DB->get_field('options_smowl_course', 'smowlcoursemedia', array('courseid' => $id));
		$varVieja = $DB->get_field('options_smowl_course', 'smowlcoursevarianza', array('courseid' => $id));
			
//////////////////////////////////////////// PARENTCONTEXTID DEL CURSO
		$pcontext = $DB->get_field('context', 'id', array('contextlevel' => 50, 'instanceid' => $id));
				
//////////////////////////////////////////// LISTA DE TODOS LOS MODULOS DEL CURSO
		$table1 = $prefix.'modules';
		$table2 = $prefix.'course_modules';
		$sql = "SELECT name FROM $table1 WHERE id IN (SELECT DISTINCT module FROM $table2 WHERE course=$id AND module!=16)";
		$array = $DB->get_records_sql($sql);
		foreach ($array as $name) 
		{
			$pattern = 'mod-'.$name->name.'-*';
			$table = $prefix.'block_instances';
			$sql = "SELECT id FROM $table WHERE parentcontextid=$pcontext AND pagetypepattern = '$pattern'";
			$blockid = $DB->get_record_sql($sql);
//////////////////////////////////////////// COMPROBACIÓN DE SI EXISTE EL BLOQUE EN LA TABLA
			if($blockid == null)
			{
				// EL BLOQUE NO EXISTE --> COMPROBACIÓN DE SI EL BLOQUE ESTÁ MARCADO
				// valor=1 insertamos la instancia del bloque en mdl_block_instances
				// valor=0 no hacemos nada
				if($_POST['check'.$name->name] == 1)
				{
					$record = new stdClass();
					$record->blockname = 'bloquecurso';
					$record->parentcontextid = $pcontext;
					$record->showinsubcontexts = 1;
					$record->pagetypepattern = $pattern;
					$record->defaultregion = 'side-post';
					$record->defaultweight = 0;
					$DB->insert_record('block_instances', $record);
				}
			}
			else
			{
				// EL BLOQUE SI EXISTE --> COMPROBACIÓN DE SI EL BLOQUE ESTÁ MARCADO
				// valor=0 borramos la instancia del bloque en mdl_block_instances
				// valor=1 no hacemos nada
				if($_POST['check'.$name->name] == '')
				{
					$DB->delete_records('block_instances', array('parentcontextid' => $pcontext, 'pagetypepattern' => $pattern));
				}
			}
		}
		
//////////////////////////////////////////// OBTENCIÓN DEL ID DE LAS INSTANCIAS DEL BLOQUE
		$str = "";
		$table = $prefix.'block_instances';
		$sql = "SELECT id FROM $table WHERE blockname='bloquecurso' AND parentcontextid=$pcontext AND defaultweight=0";
		$array = $DB->get_records_sql($sql);
		foreach ($array as $valor)
		{
			if ($str == "")
			{
				$str = $valor->id;
			}
			else
			{
				$str = $str.",".$valor->id;
			}
		}
				
//////////////////////////////////////////// ACTUALIZACIÓN DATOS DE SMOWL
		switch ($CSM->privileges_course) 
		{
			case 1:
				switch ($_POST['media']) 
				{
					case 0:
						$media = 4;
						break;
					case 1:
						$media = 10;
						break;
					case 2:
						$media = 12;
						break;
				}
				switch ($_POST['varianza']) 
				{
					case 0:
						$varianza = 10;
						break;
					case 1:
						$varianza = 15;
						break;
					case 2:
						$varianza = 20;
						break;
				}
				$courseType = 0;
				break;
			case 2:
				switch ($_POST['tipo']) 
				{
					case 1:
						$media = $CSM->curso_media_grado;
						$varianza = $CSM->curso_varianza_grado;
						break;
					case 2:
						$media = $CSM->curso_media_master;
						$varianza = $CSM->curso_varianza_master;
						break;
				}
				$courseType = $_POST['tipo'];
				break;
		}
		$table = $prefix."options_smowl_course";
		$sql = "UPDATE $table SET smowlcoursemedia=$media, smowlcoursevarianza=$varianza, course_type=$courseType, block_instance_id='$str' WHERE courseid = $id";
		$DB->execute($sql);
				
//////////////////////////////////////////// LLAMADA A WEB SERVICE
		if($medVieja != $media or $varVieja != $varianza)
		{
			$table1 = $prefix."user_enrolments";
			$table2 = $prefix."enrol";
			$table3 = $prefix."role_assignments";
			$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $id) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
			$array = $DB->get_records_sql($sql);
			$str = "";
			foreach ($array as $userid) 
			{
				if($str == "")
				{
					$str = $userid->userid;
				}
				else
				{
					$str = $str.','.$userid->userid;
				}
			}
			$modality = '';
			switch($CSM->privileges_course)
			{
				case 1:
					$modality = 'course';
					break;
				case 2:
					switch($_POST['tipo'])
					{
						case 1:
							$modality = 'grado';
							break;
						case 2:
							$modality = 'master';
							break;
					}
					break;
			}
			
			$idEntity = $CSM->entity;
			$idModality = $modality;
			$idCourse = $id;
			$aUser = $str;
			$valMedia = $media;
			$valVarianza = $varianza;
			
			/* llamada a función UPDATE */
			$sw = new SOAP_WSDL("http://www.smowltech.net/WS_SMOWL_MOODLE/WS_SMOWL_MOODLE_DEMO.php?wsdl"); 
			$proxy = $sw->getProxy();
			$erg = $proxy->Update($idEntity, $idModality, $idCourse, $aUser, $valMedia, $valVarianza);
					
//////////////////////////////////////////// COMPROBACIÓN DATOS
			if($erg == 0)
			{
//////////////////////////////////////////// PAGINA ERROR
				$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
				redirect($urlRed);
			}
			else
			{
//////////////////////////////////////////// VUELTA AL CURSO
				redirect($urlCourse);
			}
		}
		else
		{
			redirect($urlCourse);
		}
	}
	
	function updateUsers()
	{
		global $DB, $CFG, $CSM;
		
		$id = required_param('id', PARAM_INT); // course id.
		$prefix = $CFG->prefix;
		$urlCourse = new moodle_url('/course/view.php?id='.$id);
		
//////////////////////////////////////////// OBTENCIÓN DEL TIPO DE CURSO
		$courseType  = $DB->get_field('options_smowl_course', 'course_type', array('courseid' => $id));
			
//////////////////////////////////////////// LLAMADA A WEB SERVICE
		$table1 = $prefix."user_enrolments";
		$table2 = $prefix."enrol";
		$table3 = $prefix."role_assignments";
		$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $id) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
		$array = $DB->get_records_sql($sql);
		$str = "";
		foreach ($array as $userid) 
		{
			if($str == "")
			{
				$str = $userid->userid;
			}
			else
			{
				$str = $str.','.$userid->userid;
			}
		}
		
		$modality = '';
		switch($CSM->privileges_course)
		{
			case 1:
				$modality = 'course';
				break;
			case 2:
				switch($courseType)
				{
					case 1:
						$modality = 'grado';
						break;
					case 2:
						$modality = 'master';
						break;
				}
				break;
		}
		
		$idEntity = $CSM->entity;
		$idModality = $modality;
		$idCourse = $id;
		$aUser = $str;
			
		/* llamada a función UPDATE USER */
		$sw = new SOAP_WSDL("http://www.smowltech.net/WS_SMOWL_MOODLE/WS_SMOWL_MOODLE_DEMO.php?wsdl"); 
		$proxy = $sw->getProxy();
		$erg = $proxy->UpdateUser($idEntity, $idModality, $idCourse, $aUser);
		
//////////////////////////////////////// COMPROBACIÓN DATOS
		if($erg == 0)
		{
//////////////////////////////////////// PAGINA ERROR
			$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
			redirect($urlRed);
		}
		else
		{
////////////////////////////////////// VUELTA A LAS OPCIONES
			$urledit = new moodle_url('/report/smowlcourseoptions/edit.php?id='.$idCourse);
			redirect($urledit);
		}	
	}