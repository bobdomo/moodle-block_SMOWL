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
 * @package   block_bloquequiz
 * @author    Manu Fraile Yarza (manuel.fraile@deusto.es)
 * @copyright Smiley Owl Tech S.L.
 * @contact   mikel.labayen@smowltech.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/report/smowlinformes/configsmowl.php');

class block_bloquequiz extends block_base 
{
    public function init()
	{
        $this->title = get_string('smowl', 'block_bloquequiz');
    }
	public function get_content() 
	{
        global $CFG, $USER, $COURSE, $DB, $CSM;
        
        require_once($CFG->libdir.'/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }
		
        $this->content = new stdClass;
		
		$idAttempt = required_param('attempt', PARAM_INT);
		$idQuiz = $DB->get_field('quiz_attempts', 'quiz', array('id' => $idAttempt));
		
		$idEntity = $CSM->entity;
		$idModality = 'quiz';
		$idCourse = $idQuiz;
		$idUser = $idEntity.'_'.$USER->id;
		$url =  new moodle_url('/course/view.php', array('id'=>$COURSE->id));
		
		$form = '<form>';
        $form .= '<div>';
		$url2 = 'https://www.smowltech.net/WebPageClient/WebPageClientMOODLE/mqcDEMO.php?idEntity='.$idEntity.'&idModality='.$idModality.'&idCourse='.$idCourse.'&idUser='.$idUser.'&url='.$url;
		$form .= '<iframe style="background-color: #51c2ec" width="100%" height="'.$CSM->blockheight.'" src="'.$url2.'" frameborder="0" allowfullscreen scrolling="no"></iframe>';
        $form .= '</div>';
        $form .= '</form>';

        $this->content->text = $form;
       
        return $this->content;
	}
}