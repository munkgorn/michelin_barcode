<?php 
	class ModelModel extends db {
		public function findPath($data = array()){
			$result = array();
			$list_file = scandir(DOCUMENT_ROOT);
			$arr_ban_word = array('.git','assets','catalog','log','required','system','tool','uploads','uploads_payment');
			foreach ($list_file as $value) {
		    if ($value === '.' or $value === '..') continue;
			    if (is_dir(DOCUMENT_ROOT . '/' . $value)) {
			    	if(!in_array($value,$arr_ban_word)){
			    		$result[] = $value;
			    	}
			    }
			}
			return $result;
		}
		public function listTableDB($data = array()){
			$result = array();
			$sql = "show tables;";
			$result_query = $this->query($sql);
			$result = $result_query->rows;
			return $result;
		}
		public function addModel($data=array()){
			$path_prototype = DOCUMENT_ROOT."tool/prototype/model.php";
			$myfile = fopen($path_prototype, "r") or die("Unable to open file!");
			$result_read_file = fread($myfile,filesize($path_prototype));
			fclose($myfile);
			$new_replace_str = str_replace("[ReplaceClass]",ucfirst($data['model_name']),$result_read_file);
			$new_replace_str = str_replace("[ReplaceDB]",$data['database_name'],$new_replace_str);

			$folder = (!empty($data['folder'])?$data['folder'].'/':'');
			$path_write_file = DOCUMENT_ROOT."/".$folder."catalog/model/".$data['model_name'].'.php';
			$file = fopen($path_write_file,"w");
			fwrite($file,$new_replace_str);
			fclose($file);
			return true;
		}
		public function addController($data=array()){
			$path_prototype = DOCUMENT_ROOT."tool/prototype/controller.php";
			$myfile = fopen($path_prototype, "r") or die("Unable to open file!");
			$result_read_file = fread($myfile,filesize($path_prototype));
			fclose($myfile);
			$new_replace_str = str_replace("[ReplaceController]",ucfirst($data['controller_name']),$result_read_file);
			$new_replace_str = str_replace("[ReplaceModel]",$data['model_name'],$new_replace_str);

			$folder = (!empty($data['folder'])?$data['folder'].'/':'');
			$path_write_file = DOCUMENT_ROOT."/".$folder."catalog/controller/".$data['controller_name'].'.php';
			$file = fopen($path_write_file,"w");
			fwrite($file,$new_replace_str);
			fclose($file);
			return true;
		}
	}
?>