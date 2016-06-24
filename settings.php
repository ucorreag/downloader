<?php

defined('MOODLE_INTERNAL') || die;
global $USER;
if ($ADMIN->fulltree) {
   $settings->add(new admin_setting_heading('Decripction','', get_string('description', 'block_downloader')));
	$link ='<a href="http://serverinfo.uho.edu.cu" target="_blank">downloader website</a>';
    $settings->add(new admin_setting_heading('block_downloader', '', $link));
	
}