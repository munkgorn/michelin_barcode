<?php 
	class ConfigController extends Controller {
	    public function update() {
	    	$data = array();
	    	// echo getSession('id_user').'<';exit();
	    	// $user = $this->call('User')->login();
	    	// $data['user'] = $user;
	    	$data = array();
	    	$data['title'] = "Update Software";
	    	$style = array(
	    		// 'assets/home.css'
	    	);
	    	$data['style'] 	= $style;

 	    	$this->view('config/update',$data); 
	    }
	    public function update_source(){
	    	$result = array();
	    	// http://localhost/barcode_source/
	    	$result_source = curlParseJson(SOURCE_UPDATE);
	    	$result_link = SOURCE_UPDATE.$result_source['link'];

	    	$the_folder = 'downloads/update/';
			$zip_file_name = 'update_file.zip';
	    	$output_filename = $the_folder.$zip_file_name;

		    $host = $result_link;
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $host);
		    curl_setopt($ch, CURLOPT_VERBOSE, 1);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_AUTOREFERER, false);
		    // curl_setopt($ch, CURLOPT_REFERER, "http://www.xcontest.org");
		    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    $result = curl_exec($ch);
		    curl_close($ch);
		    $fp = fopen($output_filename, 'w');
		    fwrite($fp, $result);
		    fclose($fp);

		    $result = array();
		    $result['result_download_file'] = true;

			umask(0);
			$zip = new ZipArchive;
			if ($zip->open($the_folder.$zip_file_name) === TRUE) {
			    $zip->extractTo( './');
			    $zip->close();
			} else {
			    // echo 'failed';
			}


		    
		    $this->json($result);
	    }
	}
	// $za = new FlxZipArchive;
	// $res = $za->open($zip_file_name, ZipArchive::CREATE);
	// if($res === TRUE) {
	//     $za->addDir($the_folder, basename($the_folder));
	//     $za->close();
	//     $result['result_extract_zip_file'] = true;
	// }else{
	// 	$result['result_extract_zip_file'] = false;
	// }
	// class FlxZipArchive extends ZipArchive { 
	//     public function addDir($location, $name) {
	//         $this->addEmptyDir($name);

	//         $this->addDirDo($location, $name);
	//      } 
	//     private function addDirDo($location, $name) {
	//         $name .= '/';
	//         $location .= '/';

	//         // Read all Files in Dir
	//         $dir = opendir ($location);
	//         while ($file = readdir($dir))
	//         {
	//             if ($file == '.' || $file == '..') continue;
	//             // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
	//             $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
	//             $this->$do($location . $file, $name . $file);
	//         }
	//     } 
	// }
?>