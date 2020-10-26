<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Recode Consumed Barcde</h4>
			<p class="text-muted mb-0">Find list barcode</p>
		</div>


		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-sm-6">
					<form action="<?php echo route('barcode'); ?>" method="GET">
						<input type="hidden" name="route" value="barcode">
						<div class="row">
							<div class="col-6">
								<label class="mb-3">Find by genarater date</label>
								<div class="input-group">
									<input type="text" class="form-control datepicker" id="date" name="date" value="<?php echo $date; ?>">
									<div class="input-group-append">
										<span class="input-group-text"><i class="dripicons-calendar"></i></span>
									</div>
								</div>
							</div>
							<div class="col-6">
								<label class="mb-3">&nbsp;</label>
								<div class="input-group">
									<button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i> Search</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-6">
					<form action="<?php echo $action_import; ?>" method="post" enctype="multipart/form-data">
						<div class="form-group row">
							<label for="" class="col-sm-12 text-left">Import Excel</label>
							<div class="col-sm-12">
								<div class="input-group">
									<div class="custom-file">
										<!-- <input type="hidden" name="date_wk" value="<?php echo $_GET['date_wk']; ?>">-->
										<input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
										<label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse Excel File (.xlsx)</label>
									</div>
									<div class="input-group-append">
										<button class="btn btn-outline-primary" type="submit" id="">Import</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

		</div>
	</div>
	<div class="card">
		<?php echo !empty($success) ? '<div class="alert alert-success border-0" role="alert">'.$success.'</div>' : '';?>
		<?php echo !empty($error) ? '<div class="alert alert-danger border-0" role="alert">'.$error.'</div>' : '';?>
		<div class="card-header">

			<span class="float-left">
				<?php if (!empty($date)) {?>
					<a href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
					<!--<a href="#" class="btn btn-warning" id="import_excel">Import Excel</a>-->
				<?php }?>
			</span>
			<span class="float-right">
				<button type="button" class="btn btn-sm btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#ModalAddMenual">Add Barcode</button>
			</span>
		</div>
		<!--end card-header-->
		<div class="card-body">

			<?php if (!empty($date)) {?>
			<div class="table-responsive">
				<table class="table table-bordered" id="">
					<thead>
						<tr>
							<th width="25%">Group Prefix</th>
							<!-- <th width="25%">Barcode</th> -->
							<th>Range Barcode</th>
							<th>Qty</th>
							<!-- <th>Status</th> -->
							<!-- <th>Used date</th> -->
							<!-- <th>Create by</th> -->
							<!-- <th name="buttons" style="width:50px;"></th> -->
						</tr>
					</thead>
					<tbody>
						<?php if ($getImportBarcode) {?>
							<?php foreach ($getImportBarcode as $val) {?>
							<tr>
								<td><?php echo $val['group']; ?></td>
								<td><?php echo $val['name']; ?></td>
								<!-- <td><span class="text-success">Use</span></td> -->
								<td><?php echo $val['count']; ?></td>
								<!-- <td><?php echo $val['username']; ?></td> -->
								<!-- <td name="buttons">
									<div class=" pull-right">
										<button id="bElim" type="button" class="btn btn-sm btn-soft-danger btn-circle" onclick="rowElim(this);">
										<i class="dripicons-trash" aria-hidden="true">
										</i>
										</button>
									</div>
								</td> -->
							</tr>
							<?php }?>
						<?php } else {?>
							<tr>
								<td colspan="10" class="text-center">Empty</td>
							</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
			<!-- <div>Count row: <?php echo number_format($nums_row); ?></div> -->
			<?php }?>
		</div>
		<!--end card-body-->
	</div>
	<!--end card-->
</div>
<div class="modal" tabindex="-1" id="modal_textalert">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm remove barcode not use?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure remove some barcode <b><br><?php echo $textalert; ?></b></p>
      </div>
      <div class="modal-footer">
		<a href="<?php echo 'index.php?route=barcode/unconfirmImportBarcode'; ?>" type="button" class="btn btn-warning">Ignore</a>
        <a href="<?php echo $confirm_remove_barcode; ?>" type="button" class="btn btn-primary">Confirm</a>
      </div>
    </div>
  </div>
</div>
<!-- <script src="assets/plugins/daterangepicker/daterangepicker.js"></script> -->



<div class="modal fade" id="ModalAddMenual" tabindex="-1" role="dialog" aria-labelledby="ModalAddMenual1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h6 class="modal-title m-0 text-white" id="ModalAddMenual1">Add Barcode <?php echo $date;?></h6><button type="button"
                    class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i
                            class="la la-times text-white"></i></span></button>
            </div>
			<!--end modal-header-->
			<form method="POST" action="<?php echo $action_addmenual;?>">
            <div class="modal-body">
                <div class="row">
					<div class="col-12">
						<label for="">Prefix Barcode</label>
						<div>
						<select class="select2 form-control mb-3 custom-select" name="barcode_prefix">
							<option hidden value="">Please select prefix barcode</option>
						<?php foreach ($groups as $group): ?>
							<option value="<?php echo $group['group'];?>"><?php echo sprintf('%03d',$group['group']); ?></option>
						<?php endforeach;?>
						</select>
						</div>
					</div>
				</div>
				<div class="row mt-2">
					<div class="col-6">
						<label for="">Start Barcode</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"></span>
							</div>
							<input type="text" name="barcode_code_start" pattern="\d*" maxlength="5" class="form-control" placeholder="00000" />
						</div>
						
					</div>
					<div class="col-6">
						<label for="">End Barcode</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"></span>
							</div>
							<input type="text" name="barcode_code_end" pattern="\d*" maxlength="5" class="form-control" placeholder="00999" />
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-12 text-center">
						<p>Add Barcode : <b id="textrange"></b></p>
					</div>
				</div>
            </div>
            <!--end modal-body-->
            <div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btn-sm">Add Barcode</button>
			</div>
			<!--end modal-footer-->
			</form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>



<form
	action="<?php echo $action_import_excel; ?>"
	method="POST"
	id="form-import-excel"
	enctype="multipart/form-data"
	style="display:none;"
>

	<input type="file" name="file_import" id="import_file"
	accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
	<input type="text" class="form-control" name="date" value="<?php echo $date; ?>">
</form>
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>
<script>
$(document).ready(function(){

let sprintf = (range, prefix, text) => {
	let length = text.length;
	let returntext = "";
	for (let i = 1; i <= range; i++) {
		if (length<i) {
			returntext += prefix;
		}
	}
	returntext += text;
	return returntext;
}

let textRange = () => {
	let prefix = $('#ModalAddMenual [name="barcode_prefix"]').val();
	let start = $('#ModalAddMenual [name="barcode_code_start"]').val();
	let end = $('#ModalAddMenual [name="barcode_code_end"]').val();
	console.log(parseInt(start)+ ' ' + parseInt(end));
	let alert = (parseInt(end) < parseInt(start) || isNaN(parseInt(start)) || isNaN(parseInt(end)) || parseInt(start)==parseInt(end)) ? true : false;
	prefix = sprintf(3, '0', prefix);
	start = sprintf(5, '0', start);
	end = sprintf(5, '0', end);
	var text = '';
	if (alert == true) {
		text = prefix + start + ' - <span class="text-danger">' + prefix + end + '</span>';
		$('#ModalAddMenual [type="submit"]').attr('disabled','disabled');
	}  else {
		text = '<span class="text-primary">' + prefix + start + ' - ' + prefix + end + '</span>';
		$('#ModalAddMenual [type="submit"]').removeAttr('disabled','disabled');
	}
	return text;
}

var modalInit = () => {
	$('#ModalAddMenual [type="submit"]').attr('disabled','disabled');
	$('#ModalAddMenual [name="barcode_prefix"]').val(null).trigger('change');
	$('#ModalAddMenual [name="barcode_code_start"]').val('');
	$('#ModalAddMenual [name="barcode_code_end"]').val('');
	$('#textrange').html('');
}

	$('#ModalAddMenual [type="submit"]').attr('disabled','disabled');
	$('#ModalAddMenual').on('hide.bs.modal', function () {
		modalInit();
	});

	$('#ModalAddMenual [name="barcode_prefix"]').select2({
		placeholder: "Select prefix barcode",
		allowClear: true
	});
	
	$('#ModalAddMenual [name="barcode_prefix"]').on('select2:select', function (e) {
		var prefix = $(this).val();
		prefix = sprintf(3, '0', prefix);
		$('#ModalAddMenual .input-group-text').html(prefix);
		$('#textrange').html(textRange());
	});
	$('#ModalAddMenual [name="barcode_code_start"]').keyup(function(){
		$('#textrange').html(textRange());
	});
	$('#ModalAddMenual [name="barcode_code_end"]').keyup(function(){
		$('#textrange').html(textRange());
	});

});
<?php if (!empty($textalert)): ?>
		//alert("<?php echo $textalert; ?>");
		$('#modal_textalert').modal('show');
	<?php endif;?>
	$(document).on('click','#import_excel',function(e){
		$('#import_file').trigger('click');
	});
	// $(document).on('change','#import_file',function(e){
	// 	var ele = $(this);

	// 	var date = $('#date').val();

	// 	var file_data = $('#import_file').prop('files')[0];
	//     var form_data = new FormData();
	//     form_data.append('file_import', file_data);
	//     form_data.append('date', date);
	// 	$.ajax({
	// 		url: 'index.php?route=barcode',
	// 		cache: false,
	//         contentType: false,
	//         processData: false,
	//         dataType: 'text',
	// 		type: 'POST',
	// 		dataType: 'json',
	// 		data: form_data,
	// 	})
	// 	.done(function(e) {
	// 		window.location = 'index.php?route=barcode&date='+date+'&result=success';
	// 		console.log(e);
	// 		console.log("success");
	// 	})
	// 	.fail(function(a,b,c) {
	// 		console.log(a);
	// 		console.log(b);
	// 		console.log(c);
	// 		console.log("error");
	// 	})
	// 	.always(function() {
	// 		console.log("complete");
	// 	});
	// 	// location.reload();
	// });
	// $('#form-import-excel').submit();
</script>