<?php 
/**
* @subpackage block_downloade
* Descarga todos los archivos que el filearea sea curl_multi_getcontent,
* descarga segun por temas....
*
* Version 1.0.0.1
* fecha 21/06/2016
*
* @e-mail ucorreag1@gmail.com
*/


define('NO_DEBUG_DISPLAY', true);

require_once('../../config.php');
require_once('../../lib/filelib.php');
global $DB, $USER;
$ids = $_POST['id_couse'];



$fs = get_file_storage();
$zipper   = get_file_packer('application/zip');
$course_name=$DB->get_records('course', array('id' => $ids));


$filename = clean_filename($course_name[$ids]->fullname) . '-'. date("d-M-Y"). ".zip";
$temppath = make_request_directory().  $filename;
$modules = $DB->get_records('course_modules', array('course' => $ids)); 
$course_sections = $DB->get_records('course_sections', array('course' => $ids));

//'topic' .section '-'. name
$arr_folder=array();
$sections=array();
$cont_sect=0;
foreach ($course_sections as $seq) {
    $s=$seq->sequence;
    
    if($s){ 
      $data=array();    
      
        $sequen=explode(',', $s);             
        foreach ($sequen as $da) {  
                  
                if($DB->get_records('course_modules', array('module' => 8, 'id' => $da))){// folder en dodules es 8
	             $cm = get_coursemodule_from_id('folder', $da, 0, true, MUST_EXIST);
                
                $folder = $DB->get_record('folder', array('id' => $cm->instance), '*', MUST_EXIST);        
                //$arr_folder[]=$folder;
                $dato = upload_file_info($da);
                $data[$folder->name] = array('/' => $fs->get_file($dato[0]['contextid'], $dato[0]['component'], $dato[0]['filearea'], 0, '/', '.'));
                 
                
                }else{
                $dato = upload_file_info($da);
                if($dato != null){
                $data[explode('.', $dato[0]['filename'])[0]] = array('/' => $fs->get_file($dato[0]['contextid'], $dato[0]['component'], $dato[0]['filearea'], 0, '/', '.'));
                }
                } 
                          
        }
        
        
        if($seq->name != null){
             $sections[$seq->name]=$data;       
        }else{
             $sections['section'. $seq->section]=$data;
        }       
        
      
        
    }
}


$urls=array();
foreach ($sections as $key0 => $section) {
    foreach ($section as $key1=>$url) {
        foreach ($url as $key => $nombres) {
            if (!in_array($nombres,$urls) ){
                $urls[$key0. '/'. $key1] = $nombres; 
            }
        }
    }
   
}



if ($zipper->archive_to_pathname($urls, $temppath)) {
    send_temp_file($temppath,  $filename);
}
//---------------------------

//obtiene informacion del file con parametro de modulo de curso
function upload_file_info($cm) {
         global $DB;
         $id = optional_param('id', $cm, PARAM_INT);
         
         $context = context_module::instance($id, MUST_EXIST);         
         $file = $DB->get_records('files', array('contextid' => $context->id));
         $contents = array();
         foreach ($file as $value) {
             $fil = array();
             $fil['type'] = 'file';
             $fil['filename'] = $value->filename;
             $fil['contextid'] = $value->contextid;
             $fil['component'] = $value->component;
             $fil['filearea'] = $value->filearea;
          
             if ($fil['filename'] != '.' && $fil['filearea'] == 'content') {
                 $contents[] = $fil;
             }             
         }
         return $contents;
     }



?>