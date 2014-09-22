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
 * Implementaton of the quizaccess_quizoptions plugin.
 *
 * @package   quizaccess_quizoptions
 * @author    Manu Fraile Yarza (manuel.fraile@deusto.es)
 * @copyright Smiley Owl Tech S.L.
 * @contact   mikel.labayen@smowltech.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CSM;
global $CFG;

require_once($CFG->dirroot.'/report/smowlinformes/configsmowl.php');
require_once($CFG->dirroot.'/mod/quiz/accessrule/accessrulebase.php');

/**
 * A rule requiring the student to promise not to cheat.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_quizoptions extends quiz_access_rule_base 
{
    public function is_preflight_check_required($attemptid) 
	{
        return empty($attemptid);
    }
    public function add_preflight_check_form_fields(mod_quiz_preflight_check_form $quizform, MoodleQuickForm $mform, $attemptid) 
	{
//////////////////////////////////////////// FORMULARIO DE VALIDACIÓN DE ENTRADA EN QUIZ
        $mform->addElement('header', 'quiz_smowl_message_header', get_string('quiz_smowl_message_header', 'quizaccess_quizoptions'));
        $mform->addElement('static', 'quiz_smowl_statement', '', get_string('quiz_smowl_statement', 'quizaccess_quizoptions'));
        $mform->addElement('checkbox', 'quiz_smowl_label', '', get_string('quiz_smowl_label', 'quizaccess_quizoptions'));
    }
    public function validate_preflight_check($data, $files, $errors, $attemptid) 
	{
//////////////////////////////////////////// ERROR SI NO SE MARCA LA CASILLA SMOWL
        if (empty($data['quiz_smowl_label'])) 
		{
            $errors['quiz_smowl_label'] = get_string('quiz_smowl_mustagree', 'quizaccess_quizoptions');
        }
        return $errors;
    }
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) 
	{
        if (empty($quizobj->get_quiz()->smowlquizrequired)) 
		{
            return null;
        }
        return new self($quizobj, $timenow);
    }
    public static function add_settings_form_fields(mod_quiz_mod_form $quizform, MoodleQuickForm $mform) 
	{
		global $DB, $CSM;
		$modid = optional_param('update', 0, PARAM_INT);
		
//////////////////////////////////////////// SETTINGS HEADER
		$mform->addElement('header', 'gen_settings_header', get_string('gen_settings_header', 'quizaccess_quizoptions'));
		$mform->addElement('html', '<img src="http://smowltech.com/img/logo.png">');
		$mform->addElement('html', '<p> <br> </p>');
		
//////////////////////////////////////////// ACTIVAR/DESACTIVAR SMOWL
        $selSmowl = $mform->addElement('select', 'gen_smowl_required', get_string('gen_smowl_required', 'quizaccess_quizoptions'),
                array(
                    0 => get_string('gen_smowl_no', 'quizaccess_quizoptions'),
                    1 => get_string('gen_smowl_yes', 'quizaccess_quizoptions'),
                ));

//////////////////////////////////////////// SET SELECTED
		if ($modid != 0)
		{
			$quizid = $DB->get_field('course_modules', 'instance', array('id' => $modid, 'module' => 16));
			
			$smowl  = $DB->get_field('options_smowl_quiz', 'smowlquizrequired', array('quizid' => $quizid));
			$selSmowl->setSelected($smowl);
		}
		
		switch($CSM->privileges_quiz)
		{
			case 1:
				$mform->addHelpButton('gen_smowl_required', 'gen_smowl_required', 'quizaccess_quizoptions');
//////////////////////////////////////////// SELECT MEDIA
				$selMedia = $mform->addElement('select', 'quiz_media_label', get_string('quiz_media_label', 'quizaccess_quizoptions'),
						array(
							0 => get_string('quiz_media1', 'quizaccess_quizoptions'),
							1 => get_string('quiz_media2', 'quizaccess_quizoptions'),
							2 => get_string('quiz_media3', 'quizaccess_quizoptions'),
						));
				$mform->addHelpButton('quiz_media_label', 'quiz_media_label', 'quizaccess_quizoptions');
				
//////////////////////////////////////////// SELECT VARIANZA
				$selVar = $mform->addElement('select', 'gen_varianza_label', get_string('gen_varianza_label', 'quizaccess_quizoptions'),
						array(
							0 => get_string('gen_varianza1', 'quizaccess_quizoptions'),
							1 => get_string('gen_varianza2', 'quizaccess_quizoptions'),
							2 => get_string('gen_varianza3', 'quizaccess_quizoptions'),
						));
				$mform->addHelpButton('gen_varianza_label', 'gen_varianza_label', 'quizaccess_quizoptions');

//////////////////////////////////////////// SET SELECTED
				if ($modid != 0)
				{
					$quizid = $DB->get_field('course_modules', 'instance', array('id' => $modid, 'module' => 16));
					
					$valorMedia  = $DB->get_field('options_smowl_quiz', 'smowlquizmedia', array('quizid' => $quizid));
					switch ($valorMedia) 
					{
						case 1:
							$media = 0;
							break;
						case 3:
							$media = 1;
							break;
						case 5:
							$media = 2;
							break;
					}
					$selMedia->setSelected($media);
					
					$valorVarianza  = $DB->get_field('options_smowl_quiz', 'smowlquizvarianza', array('quizid' => $quizid));
					switch ($valorVarianza) 
					{
						case 10:
							$varianza = 0;
							break;
						case 15:
							$varianza = 1;
							break;
						case 20:
							$varianza = 2;
							break;
					}
					$selVar->setSelected($varianza);
				}
				$mform->disabledIf('quiz_media_label', 'gen_smowl_required', 'neq', 1);
				$mform->disabledIf('gen_varianza_label', 'gen_smowl_required', 'neq', 1);
				break;
			case 2:
				$mform->addHelpButton('gen_smowl_required', 'gen_smowl_required2', 'quizaccess_quizoptions');
				break;
		}
		
//////////////////////////////////////////// ACTUALIZAR USUARIOS
		$mform->addElement('html', '<br>');
		$mform->addElement('submit', 'updateUsers', get_string('updateButton', 'report_smowlcourseoptions'));
		
		$idDatabase = $DB->get_field('course_modules', 'instance', array('id' => $modid));
		
		if($DB->record_exists('options_smowl_quiz', array('quizid' => $idDatabase)))
		{
			$mform->disabledIf('updateUsers', 'gen_smowl_required', 'neq', 1);
		}
		else
		{
			$mform->disabledIf('updateUsers', 'gen_smowl_required', 'neq', 88);
		}
    }
	
    public static function save_settings($quiz) 
	{
        global $DB;
		global $CFG;
		global $CSM;
		global $PAGE;
		
		include "$CSM->SOAPorig";

		$prefix = $CFG->prefix;
		
		if ($quiz->updateUsers) 
		{	
			$id = $DB->get_field('quiz', 'course', array('id' => $quiz->id)); //course id
			$urlCourse = new moodle_url('/course/view.php?id='.$id);
				
////////////////////////////////////////// LLAMADA A WEB SERVICE
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
					
			$idEntity = $CSM->entity;
			$idModality = 'quiz';
			$idCourse = intval($quiz->id);
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
				$urledit = $PAGE->url;
				redirect($urledit);
			}
		}
        else if (empty($quiz->gen_smowl_required))
		{
			if ($DB->record_exists('options_smowl_quiz', array('quizid' => $quiz->id))) 
			{
//////////////////////////////////////////// SMOWL DESACTIVADO -> BORRAR DE LA BD
				$instid1  = $DB->get_field('options_smowl_quiz', 'block_instance_id', array('quizid' => $quiz->id));
				
				$DB->delete_records('options_smowl_quiz', array('quizid' => $quiz->id));
				$DB->delete_records('block_instances', array('id' => $instid1));
			
//////////////////////////////////////////// LLAMADA A WEB SERVICE
				$idcurso = $DB->get_field('quiz', 'course', array('id' => $quiz->id));
				$table1 = $prefix."user_enrolments";
				$table2 = $prefix."enrol";
				$table3 = $prefix."role_assignments";
				$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $idcurso) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
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
				$idEntity = $CSM->entity;
				$idModality = 'quiz';
				$idCourse = intval($quiz->id);
				$aUser = $str;
				
				/* llamada a función DELETE */
				$sw = new SOAP_WSDL("http://www.smowltech.net/WS_SMOWL_MOODLE/WS_SMOWL_MOODLE_DEMO.php?wsdl"); 
				$proxy = $sw->getProxy();
				$erg = $proxy->Delete($idEntity, $idModality, $idCourse, $aUser);

//////////////////////////////////////////// COMPROBACIÓN DATOS
				if($erg == 0)
				{
//////////////////////////////////////////// PAGINA ERROR
					$urlCourse = new moodle_url('/course/view.php?id='.$idcurso);
					$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
					redirect($urlRed);
				}
			}
		} 
		else 
		{
//////////////////////////////////////////// SMOWL ACTIVADO
            if (!$DB->record_exists('options_smowl_quiz', array('quizid' => $quiz->id))) 
			{
//////////////////////////////////////////// NO EXISTE EN LA BD -> INSERTAR EN LA BD
				
//////////////////////////////////////////// OBTENCIÓN DE PARENTCONTEXTID
				$modid = $DB->get_field('course_modules', 'id', array('module' => 16, 'instance' => $quiz->id));
				$pcontext = $DB->get_field('context', 'id', array('contextlevel' => 70, 'instanceid' => $modid));
				
//////////////////////////////////////////// CAMBIO DEL DEFAULTWEIGHT DEL BLOQUE QUE TENGA 0
				$table = $prefix."block_instances";
				$sql = "SELECT id FROM $table WHERE parentcontextid=$pcontext AND defaultregion='side-post' AND defaultweight=0";
				if($DB->record_exists_sql($sql)==TRUE)
				{
					$table = $prefix."block_instances";
					$sql2 = "UPDATE $table SET defaultweight=1 WHERE parentcontextid=$pcontext AND defaultregion='side-post' AND defaultweight=0";
					$DB->execute($sql2);
				}
				
//////////////////////////////////////////// CREACIÓN DE LA INSTANCIA DEL BLOQUE
			    $record = new stdClass();
				$record->blockname = 'bloquequiz';
				$record->parentcontextid = $pcontext;
				$record->showinsubcontexts = 0;
				$record->pagetypepattern = 'mod-quiz-attempt';
				$record->defaultregion = 'side-post';
				$record->defaultweight = 0;
				$DB->insert_record('block_instances', $record);
				
//////////////////////////////////////////// ALMACENAMIENTO DEL ID DE LA INSTANCIA DEL BLOQUE
				$instid2 = $DB->get_field('block_instances', 'id', array('blockname' => 'bloquequiz', 'parentcontextid' => $pcontext));
				
//////////////////////////////////////////// ALMACENAMIENTO DE LOS DATOS DE SMOWL
				$media = 0;
				$varianza = 0;
				$record = new stdClass();
                $record->quizid = $quiz->id;
                $record->smowlquizrequired = 1;
				switch($CSM->privileges_quiz)
				{
					case 1:
						switch ($_POST['quiz_media_label']) 
						{
							case 0:
								$media = 1;
								break;
							case 1:
								$media = 3;
								break;
							case 2:
								$media = 5;
								break;
						}
						switch ($_POST['gen_varianza_label']) 
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
						$record->smowlquizmedia = $media;
						$record->smowlquizvarianza = $varianza;
						break;
					case 2:
						$record->smowlquizmedia = $CSM->quiz_media;
						$record->smowlquizvarianza = $CSM->quiz_varianza;
						$media = $CSM->quiz_media;
						$varianza = $CSM->quiz_varianza;
						break;
				}
				$record->block_instance_id = $instid2;
                $DB->insert_record('options_smowl_quiz', $record);

//////////////////////////////////////////// LLAMADA A WEB SERVICE
				$idcurso = $DB->get_field('quiz', 'course', array('id' => $quiz->id));
				$table1 = $prefix."user_enrolments";
				$table2 = $prefix."enrol";
				$table3 = $prefix."role_assignments";
				$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $idcurso) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
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
				
				$idEntity = $CSM->entity;
				$idModality = 'quiz';
				$idCourse = intval($quiz->id);
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
					$urlCourse = new moodle_url('/course/view.php?id='.$idcurso);
					$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
					redirect($urlRed);
				}
            }
			else
			{
//////////////////////////////////////////// EXISTE EN LA BD -> UPDATE
				switch($CSM->privileges_quiz)
				{
					case 1:
						$quizid = $quiz->id;			
						switch ($_POST['quiz_media_label']) 
						{
							case 0:
								$media = 1;
								break;
							case 1:
								$media = 3;
								break;
							case 2:
								$media = 5;
								break;
						}
						switch ($_POST['gen_varianza_label']) 
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
						
						$table = $prefix."options_smowl_quiz";
						$sql = "UPDATE $table SET smowlquizmedia=$media, smowlquizvarianza=$varianza WHERE quizid = $quizid";
						$DB->execute($sql);

//////////////////////////////////////////// LLAMADA A WEB SERVICE
						$idcurso = $DB->get_field('quiz', 'course', array('id' => $quiz->id));
						$table1 = $prefix."user_enrolments";
						$table2 = $prefix."enrol";
						$table3 = $prefix."role_assignments";
						$sql = "SELECT DISTINCT userid FROM $table1 WHERE enrolid IN (SELECT DISTINCT id FROM $table2 WHERE courseid = $idcurso) AND userid IN (SELECT userid FROM $table3 WHERE roleid=5)";
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
						$idEntity = $CSM->entity;
						$idModality = 'quiz';
						$idCourse = intval($quiz->id);
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
							$urlCourse = new moodle_url('/course/view.php?id='.$idcurso);
							$urlRed = new moodle_url('/report/smowlinformes/smowlerror.php?courseUrl='.$urlCourse);
							redirect($urlRed);
						}
						break;
				}
            }
		}
    }
	
    public static function delete_settings($quiz) 
	{
        global $DB;
        $DB->delete_records('options_smowl_quiz', array('quizid' => $quiz->id));
    }
    public static function get_settings_sql($quizid) 
	{
        return array('smowlquizrequired', 'LEFT JOIN {options_smowl_quiz} smowlquiz ON smowlquiz.quizid = quiz.id', array());
    }
}
?>