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

            <form action="index.php?route=import&table=<?php echo $get_table;?>" method="post" enctype="multipart/form-data">
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
                <div class="form-group">
                    <label for="">Table</label>
                    <select name="table" class="form-control">
                        <option></option>
                        <?php foreach ($table as $value) : ?>
                        <option value="<?php echo $value;?>" <?php echo $get_table==$value?'selected':'';?>><?php echo $value;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Column</label>
                    <div class="inputadded">
                        <select name="column[]" class="form-control column">
                            <option></option>
                            <?php foreach ($column as $value) : ?>
                            <option value="<?php echo $value;?>"><?php echo $value;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {
    $('[name=table]').change(function(){
        window.location.href = 'index.php?route=import&table=' + $(this).val();
    });

    $('.inputadded').on('change','.column',function(){
        var html = getSelectColumn();
        console.log(html);
        $('.inputadded').append(html);
    });

    function getSelectColumn() {
        var html = '<select name="column[]" class="form-control column">';
        html += '<option></option>';
        <?php foreach ($column as $value) : ?>
        html += '<option value="<?php echo $value;?>"><?php echo $value;?></option>';
        <?php endforeach; ?>
        html += '</select>';
        return html;
    }
});
</script>
