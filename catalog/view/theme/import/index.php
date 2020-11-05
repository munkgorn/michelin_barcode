<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Import</h4>
			<p class="text-muted mb-0">Default</p>
		</div>
		<div class="card-body">
			<?php if (!empty($success)): ?>
			<div class="alert alert-success" role="alert"><?php echo $success; ?></div>
			<?php endif; ?>
			<?php if (!empty($error)): ?>
			<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
			<?php endif; ?>

            <form action="index.php?route=import" method="post" enctype="multipart/form-data">
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

            <hr>

            <form action="index.php?route=import/importAssociation" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for=""></label>
                    <input type="text" name="date" class="form-control">
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
