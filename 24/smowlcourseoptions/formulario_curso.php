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

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
class formulario_curso extends moodleform 
{
    //Add elements to form
    public function definition() 
	{
        global $CFG;
		global $DB;
		global $CSM;

		$prefix = $CFG->prefix;
		$courseid = required_param('id', PARAM_INT);
		$valorSmowl = 0;
 
        $mform = $this->_form;
		
//////////////////////////////////////////// SETTINGS HEADER
		$mform->addElement('html', '<img src="http://smowltech.com/img/logo.png">');
		$mform->addElement('html', '<p> <br> </p>');
		$mform->addElement('header', 'gen_settings_header', get_string('gen_settings_header', 'report_smowlcourseoptions'));
		$mform->addElement('hidden', 'id', $this->_customdata['id']);
		
		$mform->setType('id', PARAM_INT);
		
//////////////////////////////////////////// ACTIVAR/DESACTIVAR SMOWL
        $selSmowl = $mform->addElement('select', 'activar', get_string('gen_smowl_required', 'report_smowlcourseoptions'), 
				array(
                    0 => get_string('gen_smowl_no', 'report_smowlcourseoptions'),
                    1 => get_string('gen_smowl_yes', 'report_smowlcourseoptions'),
                ));
        $mform->addHelpButton('activar', 'gen_smowl_required', 'report_smowlcourseoptions');
		
//////////////////////////////////////////// SET SELECTED
		$valorSmowl  = $DB->get_field('options_smowl_course', 'smowlcourserequired', array('courseid' => $courseid));
		$selSmowl->setSelected($valorSmowl);
//////////////////////////////////////////// FIN SET SELECTED
		
//////////////////////////////////////////// NIVEL DE PRIVILEGIOS DE EDICIÓN
		switch ($CSM->privileges_course)
		{
			case 1:
//////////////////////////////////////////// SELECT MEDIA
				$selMedia = $mform->addElement('select', 'media', get_string('course_media_label', 'report_smowlcourseoptions'),
						array(
							0 => get_string('course_media1', 'report_smowlcourseoptions'),
							1 => get_string('course_media2', 'report_smowlcourseoptions'),
							2 => get_string('course_media3', 'report_smowlcourseoptions'),
						));
				$mform->addHelpButton('media', 'course_media_label', 'report_smowlcourseoptions');
		
//////////////////////////////////////////// SELECT VARIANZA
				$selVar = $mform->addElement('select', 'varianza', get_string('gen_varianza_label', 'report_smowlcourseoptions'),
						array(
							0 => get_string('gen_varianza1', 'report_smowlcourseoptions'),
							1 => get_string('gen_varianza2', 'report_smowlcourseoptions'),
							2 => get_string('gen_varianza3', 'report_smowlcourseoptions'),
						));
				$mform->addHelpButton('varianza', 'gen_varianza_label', 'report_smowlcourseoptions');
		
//////////////////////////////////////////// SET SELECTED
				$valorMedia  = $DB->get_field('options_smowl_course', 'smowlcoursemedia', array('courseid' => $courseid));
				$media = 0;
				switch ($valorMedia) 
				{
					case 4:
						$media = 0;
						break;
					case 10:
						$media = 1;
						break;
					case 12:
						$media = 2;
						break;
				}
				$selMedia->setSelected($media);

				$valorVarianza  = $DB->get_field('options_smowl_course', 'smowlcoursevarianza', array('courseid' => $courseid));
				$varianza = 0;
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
//////////////////////////////////////////// FIN SET SELECTED
				$mform->disabledIf('media', 'activar', 'neq', 1);
				$mform->disabledIf('varianza', 'activar', 'neq', 1);
				break;
			case 2:
				$selTipo = $mform->addElement('select', 'tipo', get_string('course_type_label', 'report_smowlcourseoptions'),
						array(
							1 => get_string('course_type1', 'report_smowlcourseoptions'),
							2 => get_string('course_type2', 'report_smowlcourseoptions'),
						));
				$mform->addHelpButton('tipo', 'course_type_label', 'report_smowlcourseoptions');
				
//////////////////////////////////////////// SET SELECTED
				$valorTipo  = $DB->get_field('options_smowl_course', 'course_type', array('courseid' => $courseid));
				$selTipo->setSelected($valorTipo);
//////////////////////////////////////////// FIN SET SELECTED
				$mform->disabledIf('tipo', 'activar', 'neq', 1);
				break;
		}
//////////////////////////////////////////// FIN NIVEL DE PRIVILEGIOS DE EDICIÓN

//////////////////////////////////////////// ACTUALIZAR USUARIOS
		$mform->addElement('html', '<br>');
		$mform->addElement('submit', 'updateUsers', get_string('updateButton', 'report_smowlcourseoptions'));
		$mform->registerNoSubmitButton('updateUsers');
		
		$idDatabase = $DB->get_field('options_smowl_course', 'id', array('courseid' => $courseid));
		
		$comp = 0;
		if($idDatabase > 0)
		{
			$comp = $courseid;
		}
		
		$mform->disabledIf('updateUsers', 'id', 'neq', $comp);
		$mform->disabledIf('updateUsers', 'activar', 'neq', 1);

//////////////////////////////////////////// LISTA MÓDULOS
		$mform->addElement('header', 'course_mod_header',  get_string('course_mod_header', 'report_smowlcourseoptions'));
		
//////////////////////////////////////////// OBTENCIÓN DE PARENTCONTEXT
		if($valorSmowl == 1)
		{
			$arrayId = $DB->get_field('options_smowl_course', 'block_instance_id', array('courseid' => $courseid));
			$id = explode(",", $arrayId);
			$stringId = "";
			for($i=0; $i<count($id); $i++)
			{
				if($stringId == "")
				{
					$stringId = "(id=".$id[$i];
				}
				else
				{
					$stringId = $stringId." OR id=".$id[$i];
				}
			}
			$stringId = $stringId.")";
			$table = $prefix.'block_instances';
			$pattern = 'course-view-*';
			$sql = "SELECT parentcontextid FROM $table WHERE pagetypepattern = '$pattern' AND $stringId";
			$context = $DB->get_record_sql($sql);
			$pcontext = $context->parentcontextid;
		}
//////////////////////////////////////////// FIN OBTENCIÓN PARENTCONTEXT
		
//////////////////////////////////////////// OBTENCIÓN MÓDULOS DEL CURSO
		$table1 = $prefix.'modules';
		$table2 = $prefix.'course_modules';
		$sql = "SELECT name FROM $table1 WHERE id IN (SELECT DISTINCT module FROM $table2 WHERE course=$courseid AND module!=16)";
		$array = $DB->get_records_sql($sql);
//////////////////////////////////////////// FIN OBTENCIÓN MÓDULOS DEL CURSO

//////////////////////////////////////////// BUCLE FOR (para cada módulo, miramos si smowl está activado e insertamos su checkbox)
		foreach ($array as $name) 
		{
			if($valorSmowl == 1)
			{
				$pattern = 'mod-'.$name->name.'-*';	
				$table = $prefix.'block_instances';				
				$sql2 = "SELECT id FROM $table WHERE parentcontextid=$pcontext AND pagetypepattern = '$pattern'";
				$existe = $DB->record_exists_sql($sql2);
				if($DB->record_exists_sql($sql2))
				{
					$checked = 'checked';
				}
				else
				{
					$checked = '';
				}
			}
			else
			{
				$checked = '';
			}
			$mform->addElement('advcheckbox', 'check'.$name->name, $name->name, null, array('group' => 1), $checked);
			
			$mform->disabledIf('check'.$name->name, 'activar', 'neq', 1);
		}
//////////////////////////////////////////// FIN BUCLE FOR
		$this->add_checkbox_controller(1);
//////////////////////////////////////////// FIN LISTA MÓDULOS	
		
		$this->add_action_buttons($cancel = true, $submitlabel=get_string('course_button', 'report_smowlcourseoptions'));
    }
    //Custom validation should be added here
    function validation($data, $files) 
	{
        return array();
    }
}