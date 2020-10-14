<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', 'ON');
	include('required/config.php');
	include('required/main_function.php');

	include('system/loader/controller.php');
	$path_parent_folder = 'mep';
	$path = '/catalog/controller/';
	$file_controller = array();
	$file_controller[] = $path_parent_folder.$path.'accounting.php';
	// $file_controller[] = $path_parent_folder.$path.'api.php';

	foreach($file_controller as $val){
		include($val);
	}
	
	echo '=== Start<br>';
	$classes = get_declared_classes();
	echo "- find class controller<br>";
	$count_class = 0;
	$arr_controller = array();
	foreach($classes as $class){
		$str_class = explode('Controller',$class);
		if(count($str_class)==2 and !empty($str_class[0])){
			echo $str_class[0].'Controller<br>';
			$arr_controller[] = $str_class[0];
			$count_class++;
		}
		
	}
	echo '<b>Found '.$count_class.' classes</b><br>';
	echo '=== End<br>';
	$result_testing = array();
	$controller = new AccountingController();
	foreach($arr_controller as $controller){
		// echo $controller.'Controller';exit();
		$class_controller = $controller.'Controller';
		$class_methods = get_class_methods(new $class_controller());
		foreach ($class_methods as $method_name) {
			$path_url = MURL.$path_parent_folder.'/index.php?route='.strtolower($controller).'/'.$method_name;
			$result_get = api_test($path_url,'get');
			$result_post = api_test($path_url,'post');
			$result_testing[] = array(
				'controller'	=> $controller,
				'method_name'	=> $method_name,
				'http_code_get' => $result_get['http_code'],
				'http_code_post' => $result_post['http_code']
			);
		}
	}
?> 
<table>
	<tr>
		<td>controller</td>
		<td>method_name</td>
		<td>http_code_get</td>
		<td>http_code_post</td>
	</tr>
	<?php foreach($result_testing as $val){ ?>
		<tr>
			<td><?php echo $val['controller']; ?></td>
			<td><?php echo $val['method_name']; ?></td>
			<td><?php echo $val['http_code_get']; ?></td>
			<td><?php echo $val['http_code_post']; ?></td>
		</tr>
	<?php } ?>
</table>