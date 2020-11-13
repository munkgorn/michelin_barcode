<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Recode Consumed Barcde</h4>
			<p class="text-muted mb-0">Find list barcode</p>
		</div>


		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-sm-8">
					<form>
						<input type="hidden" name="route" value="barcode">
						<div class="row">
							<div class="col-3">
								<label class="">Date</label>
								<select name="date" id="datefilter" class="form-control select2date">
								<option></option>
								</select>
							</div>
							<div class="col-3">
									<label for="">Group Prefix</label>
									<select name="" id="groupFilter" class="form-control select2group">
										<option value="">Please select date</option>
									</select>
							</div>
							<div class="col-3">
								<label class="">&nbsp;</label>
								<div class="input-group">
									<button type="button" class="btn btn-outline-primary" id="btnsearch"><i class="fas fa-search"></i> Search</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-4 text-right">
						<div><a href="index.php?route=barcode/import" class="btn btn-outline-primary">Import</a></div>
					<!-- <form action="<?php echo $action_import; ?>" method="post" enctype="multipart/form-data">
						<div class="form-group row">
							<label for="" class="col-sm-12 text-left">Import CSV</label>
							<div class="col-sm-12">
								<div class="input-group">
									<div class="custom-file">
										<input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required  />  >
										<label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse CSV File (.csv)</label>
									</div>
									<div class="input-group-append">
										<button class="btn btn-outline-primary" type="submit" id="">Import</button>
									</div>
								</div>
							</div>
						</div>
					</form> -->
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

			<div class="table-responsive">
				<table class="table table-bordered" id="table_result">
					<thead>
						<tr>
							<th width="25%">Group Prefix</th>
							<th>Range Barcode</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
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


<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');

	

	

	// $.ajaxSetup({async: true}); 
});
</script>
<script>
$(document).ready(function(){
	const trnotfound = '<tr><td colspan="3" class="text-center">Please filter date and group</td></tr>';
	const trloading = '<tr><td colspan="3" class="text-center"><img src="assets/loading.gif" height="30" /><br>Loading...</td></tr>';
	const table = $('#table_result tbody');
	const inputDate = $('#datefilter');
	const inputGroup = $('#groupFilter');

	inputGroup.select2({placeholder: "Please select date."});
	inputDate.html('<option>Loading...</option>');
	$('#btnsearch').attr('disabled','disabled');

	$.post("index.php?route=barcode/ajaxDateBarcode", {},
		function (data, textStatus, jqXHR) {
			console.log("Loadding barcode success");
			const result = jQuery.parseJSON(data);
			console.log(result);
			if (result.length>0) {
				let option = '<option></option>';
				$.each(result, function(index, value){
					option += '<option value="'+value.date_modify+'">'+value.date_modify+'</option>';
				});
				inputDate.html(option).select2({
					placeholder: "Select date"
				});
			} else {
				option = '<option></option>';
				inputDate.html(option).select2({
					placeholder: "Not found date barcode used"
				});
			}
		},
		"json"
	);

	table.html(trnotfound);
	$('#btnsearch').click(function(){
		table.html(trloading);
		const filterDate = inputDate.val();
		$.post("index.php?route=barcode/calcurateBarcode", {group: inputGroup.val(), status: 1, date: filterDate},
			function (data, textStatus, jqXHR) {
				console.log(data);
				console.log(data.length);
				if (data.length > 0) {
					let html = '';
					$.each(data, function(index,value) {
						html += '<tr>';
						html += '<td class="text-center">'+value.barcode_prefix+'</td>';
						html += '<td class="text-center">'+value.start+' - '+value.end+'</td>';
						html += '<td class="text-center">'+value.qty+'</td>';
						html += '</tr>';
					});
					table.html(html);
				} else {
					table.html(trnotfound);
				}
			},
			"json"
		);
	});

	inputDate.change(function(){
		const filterDate = $(this).val();
		table.html(trnotfound);
		$('#btnsearch').attr('disabled','disabled');
		inputGroup.select2('destroy').html('<option>Loading...</option>');
		$.post("index.php?route=barcode/ajaxGetGroupByDate", {date: filterDate},
			function (data, textStatus, jqXHR) {
				let option = '';
				$.each(data, function(index,value) {
					// option += '<option value="'+value.barcode_prefix+'">'+value.barcode_prefix+'</option>';
					option += '<option value="'+value.group_code+'">'+value.group_code+'</option>';
				});
				$('#groupFilter').html(option).select2({
					placeholder: "Select group"
				});
				$('#btnsearch').removeAttr('disabled');
			},
			"json"
		);
	});

	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});

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