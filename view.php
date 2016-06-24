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

//defined('MOODLE_INTERNAL') || die();
//define('NO_DEBUG_DISPLAY', true);


require_once(__DIR__ . "/../../config.php");
global $DB, $USER;
$ids = $_POST['id_couse'];

$fs = get_file_storage();
$course_name=$DB->get_records('course', array('id' => $ids));

$modules = $DB->get_records('course_modules', array('course' => $ids)); 
$course_sections = $DB->get_records('course_sections', array('course' => $ids));


$sections=array();
foreach ($course_sections as $seq) {
    $s=$seq->sequence;
    
    if($s){ 
      $data=array();    
      
        $sequen=explode(',', $s);             
        foreach ($sequen as $da) {  
                  
                if($DB->get_records('course_modules', array('module' => 8, 'id' => $da))){// folder en modules es 8
	             $cm = get_coursemodule_from_id('folder', $da, 0, true, MUST_EXIST);
                
                $folder = $DB->get_record('folder', array('id' => $cm->instance), '*', MUST_EXIST);        
                
                $dato = upload_file_info($da);
                $data[$folder->name] = array('/' => $fs->get_file($dato[0]['contextid'], $dato[0]['component'], $dato[0]['filearea'], 0, '/', '.'));
                 
                    
                
                }else{
                $dato = upload_file_info($da);
                if($dato != null){
                $data[explode('.', $dato[0]['filename'])[0]] = array('/' => $fs->get_file($dato[0]['contextid'], $dato[0]['component'], $dato[0]['filearea'], $dato[0]['itemid'], $dato[0]['filepath'], '.'));
               
                
                }
                } 
                          
        }
        
       $sections['sección '. $seq->section. ' - '. $seq->name]=$data;             
               
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




///////////////////////////////////////////////////
$zipper   = get_file_packer('application/zip');

$filename = clean_filename($course_name[$ids]->fullname) . '-'. date("d-M-Y"). ".zip";
$temppath=$CFG->tempdir.'/'. $filename ;

print($temppath);
if ($zipper->archive_to_pathname($urls, $temppath)) {
    if(file_exists($temppath)){
        header('Content-type:application/zip');
        header('Content-Disposition:Attachtment; filename="'. $filename.'"');
        readfile($temppath);
        unlink($temppath);							
	}
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
             $fil['itemid'] = $value->itemid;
             $fil['filepath'] = $value->filepath;
             $fil['pathnamehash'] = $value->pathnamehash;
             $fil['contenthash'] = $value->contenthash;
             
             if ($fil['filename'] != '.' && $fil['filearea'] == 'content') {
                 $contents[] = $fil;
             }             
         }
         return $contents;
     }



?>