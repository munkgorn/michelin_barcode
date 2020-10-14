<?php
require_once('vendor/autoload.php');
require_once('form.php');
use Spipu\Html2Pdf\Html2Pdf; 

class Controller{
    public function checkPermission() {
        $model_user = $this->model('user');
        $route = strtolower(trim(get('route')));
        $ex = explode('/',$route);
        if (count($ex)==1) {
            $route .= '/index';
        }
        $user = $model_user->checkUserPermission($route,id_company(),id_user());

        if (isset($user['permission'])) {
            if (in_array((string)strtolower($route), json_decode($user['permission'],true))) {
                return true;
            } else {
                // redirect('user/notfound_permission');
                echo 'notfound permission1 '.$route;
                exit();
            }
        } else {
            // redirect('user/notfound_permission');
            echo 'notfound permission2 '.$route;
            exit();
        }
        exit();
    }
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
    public function getHtmlUpload() {
        return $html = '
           <div class="modal" tabindex="-1" role="dialog" id="modal_filemanager"> 
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-4 mb-2">
                                    <input type="hidden" id="upload_result" value=""> 
                                    <input type="hidden" id="upload_preview" value=""> 
                                    <button type="button" id="upload_return" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i></button>
                                    <button type="button" id="upload_home" class="btn btn-outline-dark"><i class="fas fa-home"></i></button>
                                    <button type="button" id="upload_list" class="btn btn-outline-dark"><i class="fas fa-sync-alt"></i></button>
                                    <button type="button" id="upload_deldir" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                    <input type="file" id="upload_file" class="d-none"> 
                                    <button type="button" id="upload_select" class="btn btn-primary"><i class="far fa-image"></i></button>
                                    <button type="button" id="upload_submitupload" class="d-none"></button>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" id="upload_dirname" value="" class="form-control" placeholder="New Folder" />
                                        <div class="input-group-append">
                                            <button type="button" id="upload_newdir" class="btn btn-outline-dark"><i class="fas fa-folder-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="input-group">
                                        <input type="text" id="upload_searchtxt" value="" class="form-control" placeholder="Search" />
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <input type="text" id="upload_path" value="" class="form-control-plaintext" readonly  />
                                </div>

                            </div>
                            <div id="upload_load">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            jQuery(document).ready(function($) {
                var element_return;

                $("#upload_submitupload").hide();
                $("#modal_filemanager").on("show.bs.modal", function (e) { 
                    var button = $(e.relatedTarget)
                    var dataresult = button.data("result");
                    var datapreview = button.data("preview");
                    $("#upload_result").val(dataresult);
                    $("#upload_preview").val(datapreview);
                    loadimg("/");
                });
                $("#upload_removeimg").click(function(event) {
                    $("#upload_preview").attr("src","<?php echo MURL."assets/image/noimg.png";?>");
                    $("#upload_selectpath").val("");
                });

                $("#upload_searchtxt").keyup(function(event) {
                    var search = $("#upload_searchtxt").val();
                    var path = $("#upload_path").val();
                    loadimg(path,search);
                });
                $("#upload_home").click(function(event) {
                    $("#upload_selectpath").val("");
                    loadimg("/");
                });
                 $("#upload_return").click(function(event) {
                    var path = $("#upload_path").val().split("/");
                    var newpath = [];
                    $.each(path, function(index, val) {
                        if (val) {
                            newpath.push(val);
                        }
                    });
                    newpath.pop();
                    $("#upload_selectpath").val("");
                    loadimg(newpath.join("/"));
                }); 

                $("#upload_select").click(function(event) {
                    console.log("upload click บังคับให้ upload file click");
                    $("#upload_file").trigger("click");
                });
                $("#upload_file").change(function(event) {
                    console.log("upload file change");
                    $("#upload_submitupload").trigger("click");
                });
                $("#upload_submitupload").click(function(){
                    var fd = new FormData();
                    var files = $("#upload_file")[0].files[0];
                    fd.append("file",files);
                    fd.append("path", $("#upload_path").val());
                    $.ajax({
                        url: "<?php echo route("upload/uploadfile"); ?>",
                        type: "post",
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response){
                            if(response != 0){
                                var path = $("#upload_path").val();
                                loadimg(path);
                            }else{
                                alert("file not uploaded");
                            }
                        },
                    });
                });

                $("#upload_list").click(function(event) {
                    var path = $("#upload_path").val();
                    loadimg(path);
                });
                $("#modal_filemanager").on("click", ".selectimg", function(event) { 
                    event.preventDefault();
                    var idcom = $(this).data("company");
                    var path = $("#upload_path").val();
                    var thispath = $(this).data("path");
                    $($("#upload_result").val()).val("/"+idcom+path+thispath);
                    $($("#upload_preview").val()).attr("src","<?php echo MURL;?>uploads/"+idcom+path+thispath);
                    // $("#upload_selectpath").val(path+thispath);
                    // $("#upload_preview").attr("src","<?php echo MURL;?>uploads"+path+thispath);
                    loadimg("/");
                    $("#modal_filemanager").modal("hide"); 
                });
                $("#upload_load").on("click", ".folder", function(event) {
                    event.preventDefault();
                    var path = $("#upload_path").val().split("/");
                    var newpath = [];
                    $.each(path, function(index, val) {
                        if (val) {
                            newpath.push(val);
                        }
                    });
                    newpath.push($(this).data("path"));
                    loadimg(newpath.join("/"));
                });
                $("body").on("click", ".upload_removeimg", function(event) {
                    event.preventDefault();
                    console.log("click remove");
                    var dataresult = $(this).data("result");
                    var datapreview = $(this).data("preview");
                    $(dataresult).val("");
                    $(datapreview).attr("src","<?php echo MURL."assets/image/noimg.png"; ?>");
                });

                $("#upload_newdir").click(function(event) {
                    var nowpath = $("#upload_path").val();
                    $.ajax({
                        url: "<?php echo route("upload/makedir"); ?>",
                        type: "POST",
                        data: {path: nowpath, name: $("#upload_dirname").val()},
                        // dataType: "json",
                        success: function(response){
                            console.log(response);
                            if (response) {
                                $("#upload_dirname").val("");
                                $("#upload_list").trigger("click"); 
                                $("#upload_selectpath").val("");
                            }
                        },
                    });
                });
                $("#upload_deldir").click(function(event) {
                    if (confirm("ยืนยันการลบ ทั้ง Folder และ File ในนี้จะหายไปทั้งหมด")) {
                        var nowpath = $("#upload_path").val();
                        $("#upload_load [type="checkbox"]:checked").each(function(index, el) {
                            $.ajax({
                                url: "<?php echo route("upload/removedir"); ?>",
                                type: "POST",
                                data: {path: nowpath+$(this).data("path")},
                                // dataType: "json",
                                success: function(response){
                                    $("#upload_list").trigger("click"); 
                                    $("#upload_selectpath").val("");
                                },
                            });
                        });
                    }
                });
               
                function loadimg(folderpath = "", searchtxt = "") {
                    $.ajax({
                        url: "<?php echo route("upload/load"); ?>",
                        type: "post",
                        data: {path: folderpath, search: searchtxt},
                        dataType: "json",
                        success: function(response){
                            $("#upload_load").html(response.html);
                            $("#upload_path").val(response.path);
                        },
                    });
                }
            });
            </script>';
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
    public function getHtmlFilePDF($path='',$replace=array()){
        $html = '';
        if ($fh = fopen(DOCUMENT_ROOT.$path.'.php', 'r')) {
            while (!feof($fh)) {
                $line = fgets($fh);
                $html .= $line;
            }
            fclose($fh);
        }
        $html = strtr($html, $replace);
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

        if (curl_error($c)){
            die(curl_error($c));
        }
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
        $base_path = str_replace('adminFsoftpro88', '', BASE.'system/db/'.DB.".php");
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
        // $base_path = str_replace('adminFsoftpro88/', '', BASE.'system/db/'.DB.".php");
        // $base_path = str_replace('mep/', '', BASE.'system/db/'.DB.".php");
        // echo DOCUMENT_ROOT;exit();
        require_once(DOCUMENT_ROOT.'system/db/'.DB.".php");
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
        $file_name = $data['file_name'];
        $path = $data['path'];
        $result['size'] = 0;
        $result['path_file'] = $data['path'];
        // echo $path;exit();
        // echo $data['path'].'<';exit();
        // echo $html;exit();
        ob_end_clean();
        $html2pdf = new Html2Pdf();
        $html2pdf->setDefaultFont("thsarabunb");
        $html2pdf->writeHTML($html);
        $html2pdf->output($path,'F');

        // echo $data['path'];exit();
        
        if (file_exists($data['path'])) {
            $result['size'] = filesize($data['path']);
        }
        return $result;
    }
    public function setTitle(){
        
    }
    public function utf8_substr($string, $offset, $length = null) {
        if ($length === null) {
            return mb_substr($string, $offset, utf8_strlen($string));
        } else {
            return mb_substr($string, $offset, $length);
        }
    }
}