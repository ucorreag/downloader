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
 * downloader block definition
 *
 * @package    contrib
 * @subpackage block_downloader
 * @copyright  downloader-school.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class block_downloader extends block_base {

	function init() {
	    $this->title = get_string('pluginname', 'block_downloader');
	}
	
    /**
     * Constrols the block title based on instance configuration
     *
     * @return bool
     */
    function specialization() {
        $this->title = get_string('pluginname', 'block_downloader');
    }
	
	function get_content() {
	    global $CFG, $USER, $COURSE;
	    if ($this->content !== NULL) {
	        return $this->content;
	    }
		// default role is Student so show Student content
		$html_code_head = '<div style="width:100%; text-align:center; font-size: 0.9em; font-weight:bold">';		
	    $this->content = new stdClass;
		// check first if the user is logged
		if($USER->id){
			$html_code_body = '<form name="block_downloader_form" id="block_downloader_form" action="'. $CFG->wwwroot .
			 '/blocks/downloader/view.php" method="post" target="downloaderroom">'.
			 	'<input type="hidden" name="id_couse" value="' .$COURSE->id. '">'.
				 '<input type="submit" value="'. get_string('downloader', 'block_downloader'). '" name="downloader" title="' . get_string('download') . '">'.
				'</form>';
		}
	    $this->content->text = $html_code_head.$html_code_body.'</div>';
	    $this->content->footer = '<noscript><p style="font-size: 0.9em;">you dont have Javascript enabled which is required to run downloader plugin</p></noscript>';
			
	    return $this->content;
	}


    function instance_allow_config(){
        return false;
    }

    function has_config(){
        return true;
    }

	function instance_allow_multiple(){
	  return false;
	}

	function get_user_role($courseid){
		global $CFG,$USER,$DB;
		$roleTab = array();
		$sql_string = "select ra.roleid from ".$CFG->prefix."context,".$CFG->prefix."role_assignments ra where ".$CFG->prefix."context.id=ra.contextid and ra.userid=".$USER->id;
		$tab_sql = $DB->get_records_sql($sql_string);
		$sql_string_course = "select ra.enrolid from ".$CFG->prefix."context,".$CFG->prefix."user_enrolments ra where (".$CFG->prefix."context.instanceid=".$courseid." or ".$CFG->prefix."context.instanceid=0) and ra.id=".$courseid." and ra.userid=".$USER->id;
		$tab_sql_course = $DB->get_records_sql($sql_string_course);
		if(empty($tab_sql)){
			// current user has no any system role
			$roleTab[0] = 0;
		}else{
			sort($tab_sql);
			$sqlArray = $tab_sql[0];
			$roleTab[0] = $sqlArray->roleid;
		}
		if(empty($tab_sql_course)){
			// current user has no any system role
			$roleTab[1]= 0;
		}else{
			sort($tab_sql_course);
			$sqlArray = $tab_sql_course[0];
			$roleTab[1] = $sqlArray->enrolid;
		}
		return $roleTab;
	}
}