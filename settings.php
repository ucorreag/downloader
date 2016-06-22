<?php

defined('MOODLE_INTERNAL') || die;
global $USER;
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_downloader_skey', get_string('skey', 'block_downloader'), get_string('description', 'block_downloader'), null, PARAM_RAW));
	$link ='<a href="http://www.downloader-school.net/indexOrg.lol" target="_blank">downloader website</a>';
    $settings->add(new admin_setting_heading('block_downloader', '', $link));
}