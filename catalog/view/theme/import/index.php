<div class="page-wrapper py-5 px-3">
    <div class="row">
        <div class="col-12">
        <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3>Import Group & Barcode</h3>
                </div>
                <div class="card-body">
                    <form action="#" method="post" enctype="multipart/form-data" id="form-import-group">
                    
                        <div class="form-group">
                            <label for="">File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="import_file_group" class="custom-file-input" id="import_file_group" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
                                    <label class="custom-file-label" for="import_file_group">Browse Excel File (.xlsx)</label>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit" id=""><i class="fas fa-file-excel"></i> Import</button>
                                </div>
                            </div>
                            <div id="result_submit_form"></div>
                        </div>
                        <div class="progress mt-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="barload" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h3>Import Group Only</h3>
                </div>
                <div class="card-body">
                    <form action="index.php?route=import/importGroup" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="">File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
                                    <label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse Excel File (.xlsx)</label>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit" id=""><i class="fas fa-file-excel"></i> Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h3>Import Barcode Only</h3>
                </div>
                <div class="card-body">
                    <form action="index.php?route=import/importBarcode" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="">File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
                                    <label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse Excel File (.xlsx)</label>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit" id=""><i class="fas fa-file-excel"></i> Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header"><h3>Import Association With Date</h3></div>
                <div class="card-body">
                    <form action="index.php?route=import/importAssociation" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for=""></label>
                            <input type="date" name="date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">FileAssociation</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
                                    <label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse Excel File (.xlsx)</label>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit" id=""><i class="fas fa-file-excel"></i> Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">

            <div class="card">
                <div class="card-header"><h3>Truncate Data</h3></div>
                <div class="card-body">
                    <form action="index.php?route=import/removeData" method="post">
                        <?php foreach ($tablerm as $kt=> $t) : ?>
                        <div class="form-check">
                            <input class="form-check-input" name="listtable[]" type="checkbox" value="<?php echo $t;?>" id="table<?php echo $kt;?>">
                            <label class="form-check-label" for="table<?php echo $kt;?>"><?php echo $t;?></label>
                        </div>
                        <?php endforeach; ?>
                        <input type="submit" class="btn btn-outline-danger mt-2" value="Truncate" onclick="return confirm('Are you sure?')" />
                    </form>
                </div>
            </div>

        </div>
        <div class="col-sm-6"> 
            <div class="card">
                <div class="card-header"><h3>Remove Save Association</h3></div>
                <div class="card-body">
                    <form action="index.php?route=import/removeAssoiation" method="post">
                        <select name="dateass" id="" class="form-control">
                        <?php foreach ($assdate as $ass) : ?>
                            <option value="<?php echo $ass['date_wk'];?>"><?php echo $ass['date_wk'];?></option>
                        <?php endforeach; ?>
                        </select>
                        <input type="submit" class="btn btn-outline-danger mt-2" value="Remove" onclick="return confirm('Are you sure?')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header"><h3>Production Ziping 'catalog/*'</h3></div>
                <div class="card-body">
                    <a href="index.php?route=import/zip" class="btn btn-outline-success">Zip file to production</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header"><h3>Update Config</h3></div>
                <div class="card-body">
                    <form action="index.php?route=import/updateConfig" method="post">
                        <?php foreach ($configm as $kt=> $t) : ?>
                        <div class="form-check">
                            <input class="form-check-input" name="listconfig[]" type="checkbox" value="<?php echo $t;?>" id="config<?php echo $kt;?>">
                            <label class="form-check-label" for="config<?php echo $kt;?>"><?php echo $t;?></label>
                        </div>
                        <?php endforeach;?>
                        <input type="text" class="form-control" name="numvalue" value="1" />
                        <input type="submit" class="btn btn-outline-danger mt-2" value="update" />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header"><h3>Update Date Barcode Range with date in barcode</h3></div>
                <div class="card-body">
                    <a href="index.php?route=import/updateDateRange" class="btn btn-primary" onclick="return confirm('Are you sure?');">Update</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#barload {
    -webkit-transition: width 300ms;
    -moz-transition: width 300ms;
    -o-transition: width 300ms;
    transition: width 300ms;
}
</style>
<script>
$(document).ready(function () {
    $('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});

    let loading = (percent) => {
        console.log(percent);
        const eleload = $('#barload')
        eleload.attr('aria-valuenow', percent).css('width', percent+'%');
    }

    $( "#form-import-group" ).submit(function( event ) {
        $('#result_submit_form').html('');
        // var jform = new FormData();
        // jform.append('import_file',$('#import_file_group').val());

        var fd = new FormData();
        var files = $('#import_file_group')[0].files[0];
        fd.append('import_file',files);


        $.ajax({
            url: 'index.php?route=import/importAll',
            type: 'POST',
            data: fd,
            // dataType: 'html',
            mimeType: 'multipart/form-data', // this too
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
        })
        .done(function(data) {
            console.log(data);
            loading(10);
            $('#result_submit_form').html('Upload '+data.status+'. Please waiting for create file...');
            if(data.status=="Success"){
                $.ajax({
                    url: 'index.php?route=import/importAll_gen_barcode',
                    type: 'GET',
                    data: {
                        xlsx: data.xlsx
                    },
                    // dataType: 'html',
                    // mimeType: 'multipart/form-data', // this too

                    // contentType: false,
                    // cache: false,
                    // processData: false,
                    dataType: 'json',
                })
                .done(function(data_ajax2) {
                    // console.log(data);
                    loading(20);
                    $('#result_submit_form').html('Create file '+data_ajax2.result +' File count: '+ data_ajax2.count_file);

                    let peritem = 80 / parseInt(data_ajax2.count_file);
                    let count = 20;
                    for (i = 1; i <= data_ajax2.count_file; i++) {
                        console.log(i);
                        $.ajax({
                            url: 'index.php?route=import/importAll_import_barcode',
                            type: 'GET',
                            dataType: 'html',
                            data: {file_name: i},
                            async: true
                        })
                        .done(function() {
                            $('#result_submit_form').html('Importing csv to database : ' + data_ajax2.count_file);
                            console.log("success"+'importAll_import_barcode');
                        })
                        .fail(function() {
                            console.log("error"+'importAll_import_barcode');
                        })
                        .always(function() {
                            console.log("complete"+'importAll_import_barcode');
                            count += peritem;
                            loading(count);
                            if (count+1 >= 100) {
                                $('#result_submit_form').html('Import success!!');
                            }
                        });
                        
                    }
                })
                .fail(function(a,b,c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                });
            }
        })
        .fail(function(a,b,c) {
            console.log(a);
            console.log(b);
            console.log(c);
        });
    event.preventDefault();
    });
});
</script>