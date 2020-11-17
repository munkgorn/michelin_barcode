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
        <div class="col-sm-6">
        
            <div class="card">
                <div class="card-header">
                    <h3>Import Group&Barcode</h3>
                </div>
                <div class="card-body">
                    <form action="index.php?route=import/importAll" method="post" enctype="multipart/form-data">
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
        <div class="col-sm-6">

            <div class="card">
                <div class="card-header"><h3>Remove Data</h3></div>
                <div class="card-body">
                    <form action="index.php?route=import/removeData" method="post">
                        <?php foreach ($tablerm as $kt=> $t) : ?>
                        <div class="form-check">
                            <input class="form-check-input" name="listtable[]" type="checkbox" value="<?php echo $t;?>" id="table<?php echo $kt;?>">
                            <label class="form-check-label" for="table<?php echo $kt;?>"><?php echo $t;?></label>
                        </div>
                        <?php endforeach; ?>
                        <input type="submit" class="btn btn-outline-primary mt-2" value="TRUNCATE" onclick="return confirm('Are you sure?')" />
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    $('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});

});
</script>