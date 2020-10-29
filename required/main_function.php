<?php
	function id_admin(){
		$result 	= '';
		$id_admin 		= (isset($_SESSION['id_admin'])?$_SESSION['id_admin']:'');
    	if(!empty($token) AND !empty($key)){
	    	$result = $id_admin;
	    }else{
	    	header('location:index.php?route=home/login');
	    }
	    return $result;
	}
	function page_404(){
		echo 'ไม่พบหน้านี้ <a href="index.php">กลับ</a>';
		exit();
	}
	function is($val,$index){
		return (isset($val[$index])?$val[$index]:'');
	}
	function limit($page,$total_limit=0){
		$text_limit = 0;
		$limit = $total_limit;
		if($total_limit==0){
			$limit = DEFAULT_LIMIT_PAGE;
		}
		$page = ($page-1)*$limit;
		$text_limit = ' LIMIT '.$page.','.$limit;
		return $text_limit;
	}
	function date_f($date_text,$format=''){
		if(empty($format)){
			$format = DATE_FORMAT;
		}
		$result = '';
		if(!empty($date_text)){
			$result = date($format,strtotime($date_text));
		}
		return $result;
	}
	function rutime($ru, $rus, $index) {
	    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
	     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
	}
	function id_company(){
		if(DEBUG_MODE==true){
			$result=1;
		}else{
			$result 	= '';
			$encode_id_company = (isset($_SESSION['encode_id_company'])?$_SESSION['encode_id_company']:error('Not found session id company'));
			$id_user = id_user();
			$id_company = decode($encode_id_company,$id_user);
			$result = $id_company;
			if(empty($result) OR $result==0){
				// header('location: index.php?route=dashboard/home');
			}
		}
		return $result;
	}
	function id_user(){
		if(DEBUG_MODE==true){
			$result=1;
		}else{
			$result 	= '';
			$token 		= (isset($_SESSION['token'])?$_SESSION['token']:'');
	    	$key 		= (isset($_SESSION['user_key'])?$_SESSION['user_key']:'');
	    	if(empty($token)){
	    		error('Not found session token');
	    	}
	    	if(empty($key)){
	    		error('Not found session key');
	    	}
	    	if(!empty($token) AND !empty($key)){
		    	$id_user 	= decode($token,$key);
		    	$result = $id_user;
		    }else{
		    	header('location:index.php?route=dashboard/expireSession');
		    }
		}
		return $result;
	}
	function error($text){
		$result = '';
		$result = '<div id="errMsg" class="alert alert-danger"><div class="text-danger">'.$text.'</div></div>';
	}

	function fsp_crypt( $string, $action = 'e' ) {
	    $secret_key = 'fsp@friendlysoftpro#secret-key568';
	    $secret_iv = 'fsp@friendlysoftpro#secret-iv568';
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	    if( $action == 'e' ) {
	        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    }
	    else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	    }
	    return $output;
	}

	function encrypt($string){
		$salting = substr(md5(microtime()),-1) . $string;
		return fsp_crypt( $salting, 'e' );
	}

	function decrypt($string){
		$encode = fsp_crypt( $string, 'd' );
		return substr($encode, 1);
	}
	function encode($string,$key) {
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    $i=0;$j=0;$hash='';
	    for ($i = 0; $i < $strLen; $i++) {
	        $ordStr = ord(substr($string,$i,1));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
	    }
	    return $hash;
	}

	function decode($string,$key) {
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    $i=0;$j=0;$hash='';
	    for ($i = 0; $i < $strLen; $i+=2) {
	        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= chr($ordStr - $ordKey);
	    }
	    return $hash;
	}

	function pure_text($text){
		$resut = '';
		$text = trim($text);
		$text = strtolower($text);
		$result = $text;
		return $result;
	}
	function data_to_html($data=array()){
		$html_data = '';
		$i =0;
		$j=0;
		$count_col = 0;
		foreach($data as $val){
			$count_col = count($val);
			$html_data .= '<tr id="'.$i.'">';
			foreach($val as $k => $v){
				$html_data .= '<td id="'.$i.'_'.$j.'">'.$val[$k].'</td>';
			}
			$html_data .= '</tr>';
			$i++;
		}
		$count_data = count($data);
		// echo $count_data.' '.ROW_IN_DOC;exit();
		if($count_data<ROW_IN_DOC){
			for($i=$count_data;$i<=ROW_IN_DOC;$i++){
				$html_data .= '<tr>';
				for($j=1;$j<=$count_col;$j++){
					if($i==ROW_IN_DOC){
						$html_data .= '<td style="border-bottom:solid 1px #000;">&nbsp;</td>';
					}else{
						$html_data .= '<td style="border-top:solid 1px #fff;border-bottom:solid 1px #fff;">&nbsp;</td>';
					}
				}
				$html_data .= '</tr>';
			}
		}
		return $html_data;
	}
	function pageing($data=array()){
		$total = $data['total'];
		$link  = $data['link'];
		$active = ($data['active']?$data['active']:1);

		$pageing = '<nav>
			  <ul class="pagination pagination-theme">
			    <li class="page-item '.($active==1?'disabled':'').'">
			      <span class="page-link"><i class="fa fa-angle-double-left"></i></span>
			    </li>';
		for($i=1;$i<=ceil($total/DEFAULT_LIMIT_PAGE);$i++){
			if($i==$active){
				$pageing .= '<li class="page-item active" aria-current="page">
			      <span class="page-link">
			        '.$i.'
			        <span class="sr-only">(current)</span>
			      </span>
			    </li>';
			}else{
				$pageing .= '<li class="page-item"><a class="page-link" href="'.$link.'&page='.$i.'">'.$i.'</a></li>';
			}
		}
		$pageing .= '<li class="page-item '.($total==$active?'disabled':'').'">
			      <a class="page-link" href="'.($total==$active?$link:'').'"><i class="fa fa-angle-double-right"></i></a>
			    </li>
			  </ul>
			</nav>';
		return $pageing;
	}
	function breadcrumb($data=array()){
		$breadcrumb = '';
		if($data){
			$i=1;
			$active_class = 'active';
			$active_add = 'aria-current="page"';

			$breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
			foreach($data as $val){ 
				if(isset($val['active'])){
					$breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">'.$val['text'].'</li>';
				}else{ 
					$breadcrumb .= '<li class="breadcrumb-item"><a href="'.$val['url'].'">'.$val['text'].'</a></li>';
				} 
			}
			$breadcrumb .= '</ol></nav>';
		}
		return $breadcrumb;
	}
	function lang($text='',$index=''){
		$result = $text;
		return $result;
	}
	function check_member_login(){
		$result = '';
		if(isset($_SESSION['id_member'])){
			if(!empty($_SESSION['id_member'])){
				$result = $_SESSION['id_member'];
			}
		}
		return $result;
	}
	function check_admin_path(){
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$arr_explore = explode('/', $url);
		$result_search = array_search('admin',$arr_explore);
		return $result_search;
	}
	function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); } 
	function base64url_decode($data) { return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); } 
	function encodeKey($key,$msg){
		$encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $msg, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encoded;
	}
	function decodeKey($key,$msg){
		$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($msg), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $decoded;
	}
	function checktoken(){
		$result = false;
		$email = "";
		$user_id = "";
		if(!empty($_SESSION['token'])){
			
			if(isset($_SESSION["email"])){
				$email=$_SESSION["email"];
			}
			if(isset($_SESSION["user_id"])){
				$user_id=$_SESSION["user_id"];
			}
			if($_SESSION['token'] == md5($email.$user_id)){
				$result = true;
			}else{
				$result = false;
			}
		}else{
			$result = false;
		}
		return $result;
	}
	function check($username="",$password=""){

		global $obj_db;
		global $PRIVATEuser_home;
		global $user;
		$result = false;
		$result_txt="";

		if($username=="" and (isset($_SESSION["euser"]))){ 
			$username=base64url_decode($_SESSION["euser"]); 
		}else{ 
			$username = $obj_db->escape($username); 
		}
		if($password=="" and (isset($_SESSION["epass"]))){ 
			$password=base64url_decode($_SESSION["epass"]); 
		}else{ 
			$password = $obj_db->escape($password); 
		}
		// echo $password;exit();
		if(!empty($username) and !empty($password)){
			$result = $user->login($username,$password,1,5);
		}

		return $result;
	}
	
	function getpara($unset=NULL){
	
	$array_g = $_GET;
	$unset = explode(",",$unset);
	
	
	if($unset!=NULL){
	
	foreach($unset as $val){
		unset($array_g[$val]);
	}
	}
	$i=1;
	$para = '?';
	foreach($array_g as $key=>$val){
	$para .= "$key=$val";
	if($i!=count($array_g)){$para .= "&";}
	$i++;
	}
	return $para;
	}

	
	
	
	function get_slug($txt){
		$i=1;
		global $obj_db;
		$badword = array("\"", "/");
		$slug = $txt;
		$slug = str_replace($badword, "",$slug );
		$slug = str_replace(" ","_",$slug );
		
		$num = mysql_num_rows($obj_db->select("".PREFIX."product","slug = '$slug'"));
		while($num==1){
			$i++;
			$slug = $slug."($i)";
			$num = mysql_num_rows($obj_db->select("".PREFIX."product","slug = '$slug'"));
		}
	
		return $slug;
	}
	function get_tags($txt){
		$txt = explode(",", $txt);
		return $txt;
	}
	function getValArr($val){
		$result ='';
		foreach($val as $val){
			$result .= "&filter[]=".$val;
		}
		return $result;
	}
	function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	function img($path,$type){
		global $mdir;
		$result = "";
		if($type=="gallery"){
			$result = $mdir."upload/gallery/".$path;
		}else{
			$result = $mdir."upload/content/".$path;
		}
		return $result; 
	}
	
	function sendmail($to_email="",$msg="",$subject=""){
		global $mail;
		$email_username = email_username;
		$email_password = email_password;
		$email_host = email_host;
		$name_website = name_website;
		$email_port = email_port;
		$email_send = email_send;
		$email_stmpsecure = email_stmpsecure;
		

	    $body = "
	    <html>
	    	<body>".$msg."</body> 
	    </html>";
	    $subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
		$mail->Subject  =  $subject;
		//$mail->AddBCC("nongluck@systems2000.co.th,account@systems2000.co.th");
		$mail->From       = $email_username;
		$mail->FromName   = $name_website;
		$mail->MsgHTML($body);
		$mail->IsHTML(true);
		$mail->AddAddress($to_email);
		if(!$mail->Send()){ 
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'To: '.$to_email.' <'.$to_email.'>' . "\r\n";
			$headers .= 'From: '.$email_username.' <'.$email_send.'>' . "\r\n";
			$headers .= 'Bcc: '.$setting['email_bcc'] . "\r\n";
			mail($to_email,$subject,$body,$headers); 
		}
		//mail($to_email,$subject,$body,$header);
	}
	function sendmailSmtp($to_email,$msg,$subject="",$attch_file='',$name_attch_file=''){
		global	$mail ;

		global $email_detail_header;
		global $email_detail_footer;
		global $email_detail;
		global $email_bcc;


		$email_username = email_username;
		$email_password = email_password;
		$email_host = email_host;
		// $name_website = name_website;
		$email_port = email_port;
		$email_send = email_send;
		$email_stmpsecure = email_stmpsecure;

		try {
			global $mail ;

			$body             	= $msg;
			$body             	= $body;//preg_replace('/\\\\/','', $body); //Strip backslashes
			$subject 			= $subject;//= "=?utf-8?b?".base64_encode($subject)."?=";
			$mail->IsSMTP();                           // tell the class to use SMTP
			$mail->CharSet 		= "utf-8";
			$mail->SMTPAuth   	= true;                  // enable SMTP authentication
			$mail->Debugoutput 	= 'html';
			$mail->SMTPDebug 	= 0;
			$mail->Port       	= $email_port;			// set the SMTP server port
			$mail->Host       	= $email_host; 			// SMTP server
			$mail->Username   	= $email_username;     	// SMTP server username
			$mail->Password   	= $email_password;      // SMTP server password
			$mail->SMTPSecure 	= $email_stmpsecure;
			$mail->SMTPAuth 	= true;
			if(!empty($attch_file)){
				$mail->addAttachment($attch_file,$name_attch_file);
			}
			//$mail->IsSendmail();  // tell the class to use Sendmail
			
			$mail->AddAddress($to_email);
			//$mail->AddReplyTo($to,"First Last");
			/*$str = explode(',', $email_bcc);
			foreach ($str as $key => $value) {
				$mail->AddBCC($value);
			}*/
			$mail->From       = $email_username;
			$mail->FromName   = $email_send;

			$mail->Subject  = $subject;

			$mail->AltBody    = $body; // optional, comment out and test
			$mail->WordWrap   = 80; // set word wrap

			$mail->MsgHTML($body);

			$mail->IsHTML(true); // send as HTML

			$mail->Send();
			// echo 'Message has been sent.'.date('H:i:s');
			
			$fp = fopen(DOCUMENT_ROOT.'/log/mail_send.txt', 'a+');
			fwrite($fp, date('Y-m-d H:i:s').' : '.$to_email.'_'.$subject.'_'.$body.PHP_EOL);
			fclose($fp);

		} catch (phpmailerException $e) {
			$fp = fopen(DOCUMENT_ROOT.'/log/mail_error.txt', 'a+');
			fwrite($fp, date('Y-m-d H:i:s').' : '.$to_email.'_'.$subject.'_'.$body.'_'.$e->errorMessage().PHP_EOL);
			fclose($fp);
			// echo $e->errorMessage();
		}
	}
	function function_error($input){
			$file = dirname(__FILE__).DIRECTORY_SEPARATOR.'../MyLog.txt';
			$sort = "";
			$current = file_get_contents($file);
			$sort = date("d-m-Y H:i:s")." ".$input."\n".$current;
			file_put_contents($file, $sort);
	}
	function debug($variable){
		echo "<pre>";
		var_dump($variable);
		echo "</pre>";
		exit();
	}
	function post($val=""){
		$result = '';
		if (!empty($val)) {
			if(isset($_POST[$val])){
				$result = $_POST[$val];
			}
			return $result;
		} else {
			return $_POST;
		}
		
	}
	function hasSession($key) {
		if (isset($key)&&isset($_SESSION[$key])) {
			return true;
			exit();
		}
		return false;
	}
	function getSession($key) {
		if (isset($key)&&isset($_SESSION[$key])) {
			$result = $_SESSION[$key];
			return true;
			exit();
		}
		return false;
	}
	
	function session($val=""){
		$result = '';
		if(isset($_SESSION[$val])){
			$result = $_SESSION[$val];
		}
		return $result;
	}
	function get($val=""){
		$result = '';
		if(isset($_GET[$val])){
			$result = $_GET[$val];
		}
		return $result;
	}
	function check_var($val=""){
		$result = '';
		if(isset($val)){
			$result = $val;
		}
		return $val;
	}
	function files($val=""){
		$result = '';
		if(isset($_FILES[$val])){
			$result = $_FILES[$val];
		}
		return $result;
	}
	function url($path){
		global $mdir;
		$result = $mdir.$path;
		return $result;
	}
	function route($path,$para=""){
		$str_para = '';
		if(!empty($para)){
			$str_para = "&".$para;
		}
		// $result = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?route=".$path.$str_para;
		// $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		$result = "index.php?route=".$path.$str_para;
		return $result;
	}
	function style($path){
		global $mdir;
		$result = '<link rel="stylesheet" href="'.$mdir.$path . '?v=' . uniqid() . '">';
		return $result;
	}
	function script($path){
		global $mdir;
		$result = '<script src="'.$mdir.$path . '?v=' . uniqid() .'"></script>';
		return $result;
	}
	function redirect($route,$para=""){
		header('location: index.php?route='.$route.$para);
		exit();
	}
	function method_post(){
		$result = false;
		if($_SERVER['REQUEST_METHOD']=="POST"){
			$result = true;
		}
		return $result;
	}
	function method_get(){
		$result = false;
		if($_SERVER['REQUEST_METHOD']=="GET"){
			$result = true;
		}
		return $result;
	}
	function convertFileMultiple($files) {
		$result = array();
		foreach ($files['name'] as $key => $name) {
			$result[] = array(
				'name'     => $name,
				'type'     => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error'    => $files['error'][$key],
				'size'     => $files['size'][$key]
			);
		}
		return $result;
	}
	function upload($var=array(),$path,$img_profile_name=""){
		if(empty($img_profile_name)){
			$img_profile_name = $var["name"];
		}
		// $var = $_FILES[$var];
		$result = false;
		if(move_uploaded_file($var["tmp_name"],$path.$img_profile_name)){
			$result = true;
		}
		return $result;
	}
	function uploadProfile($var,$path,$img_profile_name=""){
		if(empty($img_profile_name)){
			$img_profile_name = $var["name"];
		}
		$var = $_FILES[$var];
		$result = false;
		if(move_uploaded_file($var["tmp_name"],$path.$img_profile_name)){
			$result = true;
		}
		return $result;
	}
	function check_login(){
		global $user;
		$resul = false;
		if(!$user->checkLogin()){
			redirect('student/page_login');
		}
	}
	function img_profile(){
		$result = "";
		if(!empty($_SESSION['fb_id'])){
			$result = $_SESSION['profile_path'];
		}else{
			$result = PROFILE_STUDENT_PATH.$_SESSION['profile_path'];
		}
		return $result;
	}
	function list_error($error = array()){
		$result = '';
		if($error){
			$result  = "<ul class='error text-danger'>";
			foreach ($error as $key => $value) {
				echo "<li>".$value."</li>";
			}
			$result .= "</ul>";
		}
		return $result;
	}
	function get_route(){
		$result = $_GET['route'];
		return $result;
	}
	function menu_active($route){
		$result = "";
		if($_GET['route']==$route){
			$result = "active";
		}
		return $result;
	}
	function num2wordsThai($number){     
	    $txtnum1 =  array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
		$txtnum2 =  array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');

		$number = str_replace(",","",$number);
		$number = str_replace(" ","",$number);
		$number = str_replace("บาท","",$number);
		$number = explode(".",$number);

		if(sizeof($number)>2){
			return 'error more 2 demical';
			exit;
		}
		$strlen = strlen($number[0]);
		$convert = '';
		for($i=0;$i<$strlen;$i++){
			$n = substr($number[0], $i,1);
			if($n!=0){
				if($i==($strlen-1) AND $n==1){ $convert .=  'เอ็ด'; }
				elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; }
				elseif($i==($strlen-2) AND $n==1){  $convert .= ''; }
				else{ $convert .= $txtnum1[$n]; } 
				$convert .= $txtnum2[$strlen-$i-1];
			}
		}
		$convert .= 'บาท';
		if($number[1]=='0' OR $number[1]=='00' OR 
		$number[1]==''){
		$convert .= 'ถ้วน';
		}else{
		$strlen = strlen($number[1]);
		for($i=0;$i<$strlen;$i++){
		$n = substr($number[1], $i,1);
		if($n!=0){
		if($i==($strlen-1) AND $n==1){$convert 
		.= 'เอ็ด';}
		elseif($i==($strlen-2) AND 
		$n==2){$convert .= 'ยี่';}
		elseif($i==($strlen-2) AND 
		$n==1){$convert .= '';}
		else{ $convert .= $txtnum1[$n];}
		$convert .= $txtnum2[$strlen-$i-1];
		}
		}
		$convert .= 'สตางค์';
		}
		return $convert;
	}
	function curlPost($url, $data=array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		
		curl_close ($ch);
		return json_decode($server_output, true);
	}
	function curlParseJson($url, $methodpost=true, $data=array()) {
		$get_url = $url;

		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $get_url);
		curl_setopt($cURL, CURLOPT_POST, $methodpost);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER,TRUE);
		if (count($data)>0) {

			curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Accept: application/json'
		));
		$exec = curl_exec($cURL);
		$result_json = json_decode($exec, true);
		curl_close($cURL);

		return $result_json;
	}  
	function api_test($path,$type = 'get',$params = array(), $output = NULL){

		$get_url = $path;
		// echo $get_url;exit();
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, trim($get_url));

		if($type != 'get') {
			curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");  
			curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($params));
		}

		//curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER,TRUE);
		// curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
		// 		//'Content-Type: application/json',
		// 		'Accept: application/json'
		// ));
		$exec = curl_exec($cURL);
		$info = curl_getinfo($cURL);
		// $exec = trim($exec, "\xEF\xBB\xBF");
		// if($output == 'array') {
		// 	$result_json = json_decode($exec, true);
		// } else {
		// 	$result_json = json_decode($exec);
		// }
		curl_close($cURL);
		
		return $info;
	}
	function api($path,$type = 'get',$params = array(), $output = NULL){

		global $mdir_api;
		$get_url = $mdir_api.$path;
		// echo $get_url;exit();
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, trim($get_url));

		if($type != 'get') {
			curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");  
			curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($params));
		}

		//curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
				//'Content-Type: application/json',
				'Accept: application/json'
		));

		// if(count($params) != 0) {
			
		// }
		//echo http_build_query($params); 
		$exec = curl_exec($cURL);
		
		// var_dump($exec);
		// exit();	

		$exec = trim($exec, "\xEF\xBB\xBF");
		//echo $exec;
		if($output == 'array') {
			$result_json = json_decode($exec, true);
		} else {
			$result_json = json_decode($exec);
		}
		//echo ">>";
		//var_dump($result_json);
		//echo $result_json;
		curl_close($cURL);
		
		return $result_json;
		// $cURL = curl_init();

		// curl_setopt($cURL, CURLOPT_URL,$mdir_api.$path);
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS,"postvar1=value1&postvar2=value2&postvar3=value3");

		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 
		//          http_build_query(array('postvar1' => 'value1')));

		// receive server response ...
		// curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($cURL, CURLOPT_HTTPGET, true);
		// curl_setopt($cURL, CURLOPT_RETURNTRANSFER,TRUE);
		// curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
		// 		'Content-Type: application/json',
		// 		'Accept: application/json'
		// ));
		// $server_output = curl_exec ($cURL);

		// curl_close ($cURL);
		// // further processing ....
		// if ($server_output) { 
		// 	$return = json_decode($server_output);
		// 	return $return;
		// }
	}
	function api_arr($path,$type = 'get',$params = array(), $output = NULL){

		global $mdir_api;
		$get_url = $mdir_api.$path;
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, trim($get_url));

		if($type != 'get') {
			curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");  
			curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
				//'Content-Type: application/json',
				'Accept: application/json'
		));
		$exec = curl_exec($cURL);

		$exec = trim($exec, "\xEF\xBB\xBF");
		if($output == 'array') {
			$result_json = json_decode($exec, true);
		} else {
			$result_json = json_decode($exec);
		}
		$exec = curl_exec($cURL);
		$result_json = json_decode($exec, true);

		curl_close($cURL);

		return $result_json;
	}
	function wdeeLogin($email, $password, $fb_id = NULL) {
		global $obj_db;

		// if(!empty($fb_id)) {
		// 	$result_login = $obj_db->getdata('user', 'fb_id = ' . $fb_id . ' AND del = 0');
		// } else {
			$result_login = $obj_db->getdata('member', 'member_email = "' . $email . '" AND member_password = "' . $password . '" AND del = 0');
		// }

		$status = array();
		if($result_login->num_rows > 0) {
			$status = array(
				'status'	=>	'success',
				'user_info'	=>	array(
					'user_id'	=>	$result_login->row['user_id'],
					'user_code'	=>	$result_login->row['member_gen_id'],
					'user_firstname'	=>	$result_login->row['name'],
					'user_lastname'		=>	$result_login->row['lname'],
					'user_birthday'		=>	$result_login->row['birthday'],
					'user_email'		=>	$result_login->row['email'],
				)
			);
		} else {
			// $status['status'] = 'error';
			$status = array(
				'status'	=>	'error',
				'error'		=>	$result_login
			);
		}

		return $status;
	}
	function check_empty ($value) {
		if (empty($value)) {
            header('location:index.php?r=dashboard');
        }
	}
	function ConvertDate ($date) {
		// $date = str_replace('/', '-', $date);
		// return date('Y-m-d', strtotime($date));
		return $date;
	}
	function ConvertDate_v2 ($date) {
		$date = str_replace('/', '-', $date);
		return date('Y-m-d', strtotime($date));
		// return $date;
	}
	function get_week($date_start,$date_now){
	    $result = '';
	    if (empty($date_now)) {
	    	$date_now = date('Y-m-d');
	    }
	    // $date_now = date('Y-m-d');
	    $start = strtotime($date_start);
	    $end = strtotime($date_now);
	    $timeDiff = abs($end - $start);
	    $numberDays = $timeDiff/86400;
	    $result = intval($numberDays);
	    $result = (int)($result/7)+1;
	    return $result;
	}
	function get_id_in($input) {
		if ($input) {
			$text_in = '(';
		    foreach ($input as $key => $value) {
		      $text_in .= $value.',';
		    }
		    return substr($text_in, 0,-1).')';
		}
	}
	function find_last_losscode($result_losscode) {
		global $obj_db;
		$temp_arr = array();
		foreach($result_losscode->rows as $key => $value){
            $result_losscode_sub = $obj_db->getdata('losscode_info','losscode_parent = '.$value['loss_code_id']);
            if ($result_losscode_sub->num_rows == 0) {
            	$temp_arr[] = $value['losscode_code'];
            } else {
            	$temp_arr[] = find_last_losscode($result_losscode_sub);
            }
        } 
        return $temp_arr;
	}
	function loading(){
		echo '<img src="assets/loading.gif" style="width:100px;height:100px;">';
	}
	function date_string($date_cal){
		$result = '';
		$start_date = new DateTime($date_cal);
		$since_start = $start_date->diff(new DateTime());
		if($since_start->y > 0){
			$result = $since_start->y.' years';
		}else if($since_start->m){
			$result = $since_start->m.' months';
		}else if($since_start->d){
			$result = $since_start->d.' days';
		}else if($since_start->h){
			$result = $since_start->h.' hours';
		}else if($since_start->i){
			$result = $since_start->i.' minutes';
		}else{
			$result = $since_start->s.' seconds';
		}
		return $result;
	}
	function getNewDate($timestmap, $pattern='Y-m-d', $startTime=5) {
	 if (date('G',$timestmap)<$startTime) {
	  // echo strtotime('-1 day', $timestmap).' '.time().' ';
	  return date($pattern, strtotime('-1 day', $timestmap));
	 } else {
	  return date($pattern, $timestmap);
	 }
	}
	function getBetweenDate($timestmap, $pattern='Y-m-d',$startTime=5){
		if(empty($timestmap)){
			$timestmap = time();
		}
		$result = array();
		$date_start = '';
		$date_end = '';
		if (date('G',$timestmap)<$startTime) {
			$result['date_start'] = date($pattern, strtotime('-1 day', $timestmap)).' 06:00:00';
			$result['date_end'] = date('Y-m-d').' 05:59:59';
		}else{
			$result['date_start'] = date('Y-m-d').' 06:00:00';
			$result['date_end'] = date($pattern, strtotime('+1 day', $timestmap)).' 05:59:59';
		}
		return $result;
	}
	function getBetweenDateYeaterday($timestmap, $pattern='Y-m-d',$startTime=5){
		if(empty($timestmap)){
			$timestmap = time();
		}
		$result = array();
		$date_start = '';
		$date_end = '';
		if (date('G',$timestmap)<$startTime) {
			$result['date_start'] = date($pattern, strtotime('-1 day', $timestmap)).' 14:00:00';
			$result['date_end'] = date($pattern, $timestmap).' 13:59:59';
		}
		return $result;
	}
	function getWork($type=0){
		switch($type){
			case 1 : echo 'เข้างาน'; break;
			case 7 : echo 'โดฮัง'; break;
			case 5 : echo 'ออกเดท'; break;
			case 3 : echo 'เลิกงาน'; break;
			case 6 : echo 'ทำงานอีกครั้ง'; break;
			case 12 : echo 'ไปกับลูกค้าอีกร้าน'; break;
			default : echo '-'; break;
			// case 1 : 'ลงทะเบียนวันหยุด'; break;
			// case 1 : 'นำเที่ยว'; break;
		}
	}
	function cal_birth($bithdayDate){
		$date = new DateTime($bithdayDate);
		 $now = new DateTime();
		 $interval = $now->diff($date);
		 return $interval->y;
	}

	function curl_get_contents($url)
	{
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    return $data;
	}
	function imageToBase64($image){
		$a = '';
	    $imageData = base64_encode(curl_get_contents($image));
	    $mime_types = array(
	    'pdf' => 'application/pdf',
	    'doc' => 'application/msword',
	    'odt' => 'application/vnd.oasis.opendocument.text ',
	    'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	    'gif' => 'image/gif',
	    'jpg' => 'image/jpg',
	    'jpeg' => 'image/jpeg',
	    'png' => 'image/png',
	    'bmp' => 'image/bmp'
	    );
	    $ext = pathinfo($image, PATHINFO_EXTENSION);
	    
	    if (array_key_exists($ext, $mime_types)) {
	    	$a = $mime_types[$ext];
	    }
	    return 'data: '.$a.';base64,'.$imageData;
	}
	function gen_percent($total,$total_now,$total_torrow){
		$result = array();
		$result['percent'] = 0;
		$result['temp'] = 0;
		$temp = false;
		if($total>0){
			$num1 = $total_now / $total ;
			$num2 = $total_torrow / $total;
			$num3 = ($num1 - $num2);
			if($num3<0){
				$temp = true;
			}
			$result['percent'] = $num3 * 100;
			$result['temp'] = $temp;
		}
		$result_text = '<p><i class="icon md-long-arrow-'.(!$temp?'up':'down').' '.(!$temp?'green':'red').'-500 font-size-16"></i> '.$result['percent'].'%</p>';
		return $result_text;
	}
	function num2per($number, $total, $precision = 0) {
	  if ($number < 0) {
	    return 0;
	  }

	  try {

			if($number == 0 or $total == 0){

				$percent = 0;

			}else{

	  	$percent = (($number / $total) * 100) - 100;

			}


	    return round($percent, $precision);

	  } catch (Exception $e) {
	    return 0;
	  }

	}
	function whiteExcel($data=array(), $pathSave, $name = 'Export.xlsx') {
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Admin")
                                     ->setLastModifiedBy("Admin")
                                     ->setTitle("Export Excel")
                                     ->setSubject("Export Excel")
                                     ->setDescription("Export Excel")
                                     ->setKeywords("export excel")
									 ->setCategory("export");
									 $objPHPExcel->setActiveSheetIndex(0);

		$json = '["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","aa","ab","ac","ad","ae","af","ag","ah","ai","aj","ak","al","am","an","ao","ap","aq","ar","as","at","au","av","aw","ax","ay","az","ba","bb","bc","bd","be","bf","bg","bh","bi","bj","bk","bl","bm","bn","bo","bp","bq","br","bs","bt","bu","bv","bw","bx","by","bz","ca","cb","cc","cd","ce","cf","cg","ch","ci","cj","ck","cl","cm","cn","co","cp","cq","cr","cs","ct","cu","cv","cw","cx","cy","cz","da","db","dc","dd","de","df","dg","dh","di","dj","dk","dl","dm","dn","do","dp","dq","dr","ds","dt","du","dv","dw","dx","dy","dz","ea","eb","ec","ed","ee","ef","eg","eh","ei","ej","ek","el","em","en","eo","ep","eq","er","es","et","eu","ev","ew","ex","ey","ez","fa","fb","fc","fd","fe","ff","fg","fh","fi","fj","fk","fl","fm","fn","fo","fp","fq","fr","fs","ft","fu","fv","fw","fx","fy","fz","ga","gb","gc","gd","ge","gf","gg","gh","gi","gj","gk","gl","gm","gn","go","gp","gq","gr","gs","gt","gu","gv","gw","gx","gy","gz","ha","hb","hc","hd","he","hf","hg","hh","hi","hj","hk","hl","hm","hn","ho","hp","hq","hr","hs","ht","hu","hv","hw","hx","hy","hz","ia","ib","ic","id","ie","if","ig","ih","ii","ij","ik","il","im","in","io","ip","iq","ir","is","it","iu","iv","iw","ix","iy","iz","ja","jb","jc","jd","je","jf","jg","jh","ji","jj","jk","jl","jm","jn","jo","jp","jq","jr","js","jt","ju","jv","jw","jx","jy","jz","ka","kb","kc","kd","ke","kf","kg","kh","ki","kj","kk","kl","km","kn","ko","kp","kq","kr","ks","kt","ku","kv","kw","kx","ky","kz","la","lb","lc","ld","le","lf","lg","lh","li","lj","lk","ll","lm","ln","lo","lp","lq","lr","ls","lt","lu","lv","lw","lx","ly","lz","ma","mb","mc","md","me","mf","mg","mh","mi","mj","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nb","nc","nd","ne","nf","ng","nh","ni","nj","nk","nl","nm","nn","no","np","nq","nr","ns","nt","nu","nv","nw","nx","ny","nz","oa","ob","oc","od","oe","of","og","oh","oi","oj","ok","ol","om","on","oo","op","oq","or","os","ot","ou","ov","ow","ox","oy","oz","pa","pb","pc","pd","pe","pf","pg","ph","pi","pj","pk","pl","pm","pn","po","pp","pq","pr","ps","pt","pu","pv","pw","px","py","pz","qa","qb","qc","qd","qe","qf","qg","qh","qi","qj","qk","ql","qm","qn","qo","qp","qq","qr","qs","qt","qu","qv","qw","qx","qy","qz","ra","rb","rc","rd","re","rf","rg","rh","ri","rj","rk","rl","rm","rn","ro","rp","rq","rr","rs","rt","ru","rv","rw","rx","ry","rz","sa","sb","sc","sd","se","sf","sg","sh","si","sj","sk","sl","sm","sn","so","sp","sq","sr","ss","st","su","sv","sw","sx","sy","sz","ta","tb","tc","td","te","tf","tg","th","ti","tj","tk","tl","tm","tn","to","tp","tq","tr","ts","tt","tu","tv","tw","tx","ty","tz","ua","ub","uc","ud","ue","uf","ug","uh","ui","uj","uk","ul","um","un","uo","up","uq","ur","us","ut","uu","uv","uw","ux","uy","uz","va","vb","vc","vd","ve","vf","vg","vh","vi","vj","vk","vl","vm","vn","vo","vp","vq","vr","vs","vt","vu","vv","vw","vx","vy","vz","wa","wb","wc","wd","we","wf","wg","wh","wi","wj","wk","wl","wm","wn","wo","wp","wq","wr","ws","wt","wu","wv","ww","wx","wy","wz","xa","xb","xc","xd","xe","xf","xg","xh","xi","xj","xk","xl","xm","xn","xo","xp","xq","xr","xs","xt","xu","xv","xw","xx","xy","xz","ya","yb","yc","yd","ye","yf","yg","yh","yi","yj","yk","yl","ym","yn","yo","yp","yq","yr","ys","yt","yu","yv","yw","yx","yy","yz","za","zb","zc","zd","ze","zf","zg","zh","zi","zj","zk","zl","zm","zn","zo","zp","zq","zr","zs","zt","zu","zv","zw","zx","zy","zz"]';
		$char = json_decode($json, true);
		
		$row = 1;
		$index_char = 0;
		foreach ($data as $key => $column) {
			foreach ($column as $k => $v) {
				// echo strtoupper($char[$index_char]).$row.' '.$v;
				// echo ',';
				$objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
				$index_char++;
			}
			// echo '<br>';
			$index_char = 0;
			$row++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Export Excel');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $filename = $name;
		$objWriter->save($pathSave.$filename);
		return $filename;
	}
	function readExcel($path,$first_row=2,$index=0){
		require_once(DOCUMENT_ROOT.'system/lib/PHPExcel/vendor/autoload.php');
		$objPHPExcel = new PHPExcel();

		$inputFileName = DOCUMENT_ROOT.$path; 
	   // Read Excel
	   $page = $index;

	   $inputFileType = PHPExcel_IOFactory::identify($inputFileName); 
	   $objReader = PHPExcel_IOFactory::createReader($inputFileType); 
	   $objReader->setReadDataOnly(true); 
	   $objPHPExcel = $objReader->load($inputFileName); 

	   $objWorksheet = $objPHPExcel->setActiveSheetIndex($page);
	   $highestRow = $objWorksheet->getHighestRow();
	   $highestColumn = $objWorksheet->getHighestColumn();

	   $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
	   $headingsArray = $headingsArray[1];
	   $r = -1;
	   $namedDataArray = array();
	   $address = array();
	   for ($row = $first_row; $row <= $highestRow; ++$row) {
	    $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
	    // var_dump($dataRow);
	    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
	     ++$r;
	     foreach($headingsArray as $columnKey => $columnHeading) {
	      $namedDataArray[$r][] = $dataRow[$row][$columnKey];
	     }
	    }
	   }
	   return $namedDataArray;
	}

?>