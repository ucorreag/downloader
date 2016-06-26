<?php 
/**
* 
* Descarga todos los archivos que el filearea sea curl_multi_getcontent,
* descarga segun por temas....
*
* Version 1.0.0.2
* fecha 21/06/2016
* @package block_downloader
* @e-mail ucorreag1@gmail.com
*/

require_once(__DIR__ . "/../../config.php");

$id = $_POST['id_couse'];

download_zip($id);
///////////////////////////////////////////////////
function download_zip($id){
global $DB, $CFG;
$zipper   = get_file_packer('application/zip');
$course_name = $DB->get_records('course', array('id' => $id));
$filename = clean_filename($course_name[$id]->fullname) . '-'. date("d-M-Y"). ".zip";
$temppath=$CFG->tempdir.'/'. $filename ;

$urls=get_files_sections($id);
if ($urls!=null && $zipper->archive_to_pathname($urls, $temppath)) {
    if(file_exists($temppath)){
        header('Content-type:application/zip');
        header('Content-Disposition:Attachtment; filename="'. $filename.'"');
        readfile($temppath);
        unlink($temppath);							
	}
}else{
    echo '<div style="color:blue; box-shadow: 2px 3px 4px black; margin:8% 20%; padding:10%; font-size:24px;">'.
    '<p align="center">La Asignatura seccionada no tiene archivos <br> que se puedan'. 
    ' descargar, <br> inténtelo de nuevo cuando tenga archivos</p></div>';
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
                         
             if ($fil['filename'] != '.' && $fil['filearea'] == 'content') {
                 $contents[] = $fil;
             }             
         }
         return $contents;
     }


//-------------------------------------------------------------------
// bevuelve un arreglo de files por secciones o temas
function get_files_sections($ids){
    global $DB;
    
    $modules = $DB->get_records('course_modules', array('course' => $ids)); 
    $course_sections = $DB->get_records('course_sections', array('course' => $ids));

    $sections= get_data_files($course_sections);

    $urls=array();
    foreach ($sections as $key0 => $section) {
           foreach ($section as $key => $nombres) {
                if (!in_array($nombres,$urls) ){
                    $urls[$key0] = $nombres; 
                }
            }
     }
    return $urls;   
    
}

//---------------------------------

//obtiene los files dado un arreglo de secciones de cursos
function get_data_files($course_sections){
    global $DB;    
    $fs = get_file_storage();   
    $sections = array();
    foreach ($course_sections as $seq) {
        $sequence = $seq->sequence;
        
        if($sequence){ 
              
            $sequen = explode(',', $sequence);             
            foreach ($sequen as $da) {  
                    
                    if($DB->get_records('course_modules', array('module' => 8, 'id' => $da))){// folder en modules es 8
                        $cm = get_coursemodule_from_id('folder', $da, 0, true, MUST_EXIST);                    
                        $folder = $DB->get_record('folder', array('id' => $cm->instance), '*', MUST_EXIST);        
                        
                        $dato = upload_file_info($da);
                        $sections['sección '. $seq->section. ' - '. $seq->name. '/'. $folder->name] = array('/' => $fs->get_file($dato[0]['contextid'],
                         $dato[0]['component'], $dato[0]['filearea'], $dato[0]['itemid'], $dato[0]['filepath'], '.'));
                    
                    }else{
                        $dato = upload_file_info($da);
                        if($dato != null){
                           $sections['sección '. $seq->section. ' - '. $seq->name. '/'. explode('.', $dato[0]['filename'])[0]] = array('/' => $fs->get_file($dato[0]['contextid'],
                             $dato[0]['component'], $dato[0]['filearea'], $dato[0]['itemid'], $dato[0]['filepath'], '.'));
                        }
                    } 
                            
            }
                
        }
    }
    return $sections;
}

///----



?>