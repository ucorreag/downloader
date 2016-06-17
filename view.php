<?php 
define('NO_DEBUG_DISPLAY', true);

require_once('../../config.php');
require_once('../../lib/filelib.php');
global $USER;
//$ids = $_POST['id_couse'];
// $cm es id de usuario




//$files = $DB->get_record('files', array('filename' != '.'), '*', MUST_EXIST);
$fs = get_file_storage();
$val=array();

//$val = $fs->get_file(49, 'mod_resource', 'content', 0, '/', '.');
$val = $fs->get_file(62, 'mod_resource', 'content', 0, '/', '.');
//$val = $fs->get_file(57, 'mod_folder', 'content', 0, '/', '.');



$zipper   = get_file_packer('application/zip');
$filename = clean_filename(  date("Ymd")) . ".zip";
$temppath = make_request_directory() . $filename;

if ($zipper->archive_to_pathname(array('/' => $val), $temppath)) {
    send_temp_file($temppath, $filename);
} else {
    print_error('cannotdownloaddir', 'repository');
}











$valores = upload_file_info(4);









foreach ($valores as $key) {
    echo "{$key['filename']} {$key['filepath']} {$key['filesize']} <br>";
}



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
             $fil['filepath'] = $value->filepath;
             $fil['filesize'] = display_size($value->filesize);
             $fil['timecreated'] = $value->timecreated;
             $fil['timemodified'] = $value->timemodified;
             $fil['sortorder'] = $value->sortorder;
             $fil['userid'] = $value->userid;
             $fil['author'] = $value->author;
             $fil['license'] = $value->license;
             if ($fil['filename'] != '.') {
                 $contents[] = $fil;
             }
             
         }
         return $contents;
     }

//descargar en zip todos los archivos del curso
    function descargar_zip($path_array){
	$error="";
	if(extension_loaded('zip')){
		if(isset($path_array) and count($path_array) > 0){
			$zip = new ZipArchive;				
			$zip_name = time().".zip";	
			if($zip->open($zip_name, ZIPARCHIVE::CREATE)==TRUE){
				foreach($path_array as $file){
						$zip->addFile($file);
				}
				$zip->close();
				if(file_exists($zip_name)){
				header('Content-type:application/zip');
				header('Content-Disposition:Attachtment; filename="'.$zip_name.'"');
				readfile($zip_name);
				unlink($zip_name);							
				}				
			}else{
				$error.="Error al crear el zip";
			}
		}else{
			$error.="No hay archivos apara descargar";
		}
				
	}else{
		$error.="No tiene archivo zip";
	}	
	return "<script> alert('$error')</script>";		
	}




?>