<?php
$doc_root = str_replace('private_html','public_html',DOCUMENT_ROOT);
$autoload_pdf = $doc_root.'system/loader/vendor/autoload.php';
require_once($autoload_pdf);

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
require_once('form.php');

class Controller{
    public function call($controller){
        include(BASE.'/catalog/controller/'.strtolower($controller).'.php');
        $class = $controller.'Controller';
        $controller = new $class(); 
        return $controller;
    }
    public function file($id, $multiple=false, $default=""){
        $ids = $id;
        if ($multiple==true) { $ids .= time(); }
        $html = "
<input type='file' name='".$id.($multiple?'[]':'')."' id='jsfile_".$ids."' value='".$default."' />
<div class='row'>
  <div class='col-12'>
    <img src='".$default."' id='jspreview_".$ids."' class='img-thumbnail mb-2'>
  </div>
  <div class='col-12'>
    <button type='button' id='jsadd_".$ids."' class='btn btn-primary'><i class='far fa-image'></i></button>
    <button type='button' id='jsedit_".$ids."' class='btn btn-outline-primary'><i class='far fa-image'></i></button>
    <button type='button' id='jsdel_".$ids."' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>    
  </div>
</div>
<script type='text/javascript'>
jQuery(document).ready(function($) {
    $('#jsfile_".$ids.",#jsedit_".$ids.",#jsdel_".$ids.",#jspreview_".$ids."').hide();

    if ('".$default."'!='') {
      $('#jsadd_".$ids.",#jsfile_".$ids."').hide();
      $('#jsedit_".$ids.",#jsdel_".$ids.",#jspreview_".$ids."').show();
    }

    $('#jsadd_".$ids.",#jsedit_".$ids."').click(function(event) {
      $('#jsfile_".$ids."').trigger('click');
    });

    $('#jsdel_".$ids."').click(function(event) {
      $('#jsedit_".$ids.",#jsdel_".$ids."').hide();
      $('#jsadd_".$ids."').show();
      $('#jspreview_".$ids."').attr('src', '').hide();
    });

    $('#jsfile_".$ids."').change(function(event) {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#jsedit_".$ids.",#jsdel_".$ids."').show();
          $('#jsadd_".$ids."').hide();
          $('#jspreview_".$ids."').attr('src', e.target.result).show();
        }
        reader.readAsDataURL(this.files[0]);
      }
    });

    $('#jsdel_".$ids."').click(function(event) {
      $('#jsfile_".$ids."').val('');
      $('#jsedit_".$ids.",#jsdel_".$ids."').hide();
      $('#jsadd_".$ids."').show();
    });
});
</script>";


        return $html;
    }
    // public function upload($var,$path='',$new_name=''){
    //     $result = array();
    //     if(empty($path)){
    //         $path = UPLOAD_MEP;
    //     }
    //     if(empty($new_name)){
    //         $file = (!empty($_FILES[$var])?$_FILES[$var]:'');
    //         $result_info = pathinfo($file["name"]);
    //         $extension = $result_info['extension'];

    //         $new_name = time().'_'.rand().'.'.$extension;
    //     }
    //     $result['result']   = upload($var,$path,$new_name);
    //     $result['name']     = $new_name;
    //     $result['path']     = $path;
    //     return $result;
    // }

    public function destroySession(){
        session_destroy();
    }
    public function setSession($key='',$val=''){
        if(!empty($key)){
            $_SESSION[$key] = $val;
        }
    }
    public function hasSession($key='') {
        if (isset($_SESSION[$key])) {
            return true;
        } else {
            return false;
        }
    }
    public function getSession($key=''){
        $result = '';
        if(isset($_SESSION[$key])){
            $result = $_SESSION[$key];
        }else{
            error('Not fonud session key : '.$key);
        }
        return $result;
    }
    public function rmSession($key=''){
        $result = '';
        if(isset($_SESSION[$key])){
            $_SESSION[$key] = '';
            unset($_SESSION[$key]);
        }
        return $result;
    }
    public function view($path='',$data=array(), $headfoot=true){
        // var_dump($_SERVER['REQUEST_TIME_FLOAT']);
        $time_start = microtime(true); 

        $absolute_path = '';
        $absolute_path = BASE_CATALOG.'view/'.THEME.'/'.$path.'.php';
        if(file_exists($absolute_path)){
            extract($data);
            $common_path = BASE_CATALOG.'controller/common.php';
            require_once($common_path);
             $arr_bypass = array('common/header','common/footer');
            if(!in_array($path,$arr_bypass)){
                $common = new CommonController();
                // $data_header = array(
                //     'title' => (isset($title)?$title:WEB_NAME),
                //     'class_body' => (isset($class_body)?$class_body:'')
                // );
                if ($headfoot) {
                    $common->header($data);    
                }
                require_once($absolute_path);
                if ($headfoot) {
                    $common->footer($data);
                }

            }
        }else{
            echo 'File view/'.$absolute_path.' Not found!';
            exit();
        }
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;

        //execution time of the script
        // echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
    }
    public function getFile($path){
        $filename = "/usr/local/something.txt";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        // $url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";

        // $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        // // echo $escaped_url;
        // $url = $url.'?route='.$path;
        // $c = curl_init($url);
        // curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        // //curl_setopt(... other options you want...)

        // $html = curl_exec($c);

        // if (curl_error($c))
        //     die(curl_error($c));

        // // Get the status code
        // $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        // curl_close($c);
        return $html;
    }
    public function getHtml($path){
        $url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";

        $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        // echo $escaped_url;
        $url = $url.'?route='.$path;
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(... other options you want...)

        $html = curl_exec($c);

        if (curl_error($c))
            die(curl_error($c));

        // Get the status code
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);
        return $html;
    }
    public function getHtmlPDF($path,$replace=array()){
        
        $url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";

        $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        // echo $escaped_url;
        $url = $url.'?route='.$path;
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(... other options you want...)

        $html = curl_exec($c);

        if (curl_error($c))
            die(curl_error($c));

        // Get the status code
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);
        $html = strtr($html, $replace);
        return $html;
    }
    public function render($path='',$data=array()){
        // $absolute_path = '';
        // if(!check_admin_path()){
        //     $absolute_path = BASE_CATALOG.'view/'.THEME.'/'.$path.'.php';
        // }else{
        //     $absolute_path = BASE_CATALOG_ADMIN.'view/'.THEME.'/'.$path.'.php';
        // }
        // if(file_exists($absolute_path)){
            $absolute_path = '';
            $absolute_path = BASE_CATALOG.'view/'.THEME.'/'.$path.'.php';
            if(file_exists($absolute_path)){
                extract($data);
                require_once($absolute_path);
            }
            // if($path!="common/header" or $path!="common/footer"){
           
            //     if(!check_admin_path()){
            //         $common_path = BASE_CATALOG.'controller/common.php';
            //     }else{
            //         $common_path = BASE_CATALOG_ADMIN.'controller/common.php';
            //     }
            //     require_once($common_path);
            //  $arr_bypass = array('common/header','common/footer');
            // if(in_array($path,$arr_bypass)){
            //     $common = new CommonController();
            //     $common->header();
            //     require_once($absolute_path);
            //     $common->footer();
            // }
        // }else{
        //     echo 'File view/'.$absolute_path.' Not found!';
        //     exit();
        // }
    }

    public function load_controller($path){
        // echo BASE.'system/db/'.DB.".php";exit();
        $base_path = str_replace('admin', '', BASE.'system/db/'.DB.".php");
        $base_path = str_replace('mep', '', BASE.'system/db/'.DB.".php");
        require_once($base_path);
        $absolute_path = BASE_CATALOG.'controller/'.$path.'.php';
        require_once($absolute_path);
        $string_model = ucfirst(strtolower($path))."Controller";
        $model = new $string_model();
        return $model;
    }
    public function model($path){
        // echo BASE.'system/db/'.DB.".php";exit();
        $base_path = str_replace('admin/', '', BASE.'system/db/'.DB.".php");
        $base_path = str_replace('mep/', '', BASE.'system/db/'.DB.".php");
        require_once($base_path);
        $absolute_path = BASE_CATALOG.'model/'.$path.'.php';
        require_once($absolute_path);
        $string_model = ucfirst(strtolower($path))."Model";
        $model = new $string_model();
        return $model;
    }
    public function json($data){
        header("Content-type:application/json");
        echo json_encode($data);
        exit();
    }
    public function redirect($route,$path=''){
        if(!empty($path)){
            $path = $path.'/';
        }
        $redirect = 'location: '.$path.'index.php?route='.$route;
        header($redirect);
    }
    public function pdf($html){
        ob_end_clean();
        $html2pdf = new Html2Pdf();
        $html2pdf->setDefaultFont("thsarabunb");
        $html2pdf->writeHTML($html);
        $html2pdf->output();
    }
    public function downloadPdf($html,$data=array()){
        $result = array();
        // if (class_exists('Html2Pdf')) {
            $file_name = $data['file_name'];
            $path = $data['path'];
            ob_end_clean();
            $html2pdf = new Html2Pdf();
            $html2pdf->setDefaultFont("thsarabunb");
            $html2pdf->writeHTML($html);
            $html2pdf->output($path,'F');
            $result['path_file'] = $path;
            $result['size'] = filesize($path);
        // }else{
        //     echo "Not found Html2Pdf Class";
        //     exit();
        // }
        return $result;
    }
    public function setTitle(){
        
    }
}