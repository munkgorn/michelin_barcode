<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">List barcode</h4>
			<p class="text-muted mb-0">Find list barcode</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<form action="<?php echo $action; ?>" method="GET">
				<input type="hidden" name="route" value="barcode/listGroup">
				<div class="row">
					<div class="col-3">
						<label class="mb-3">Order date</label>
						<div class="input-group">
							<input type="text" class="form-control datepicker" 
							id="date" 
							name="date" 
							value="<?php echo $date; ?>">
							<div class="input-group-append">
								<span class="input-group-text"><i class="dripicons-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="col-3">
						<label class="mb-3">Group Prefix</label>
						<select class="form-control">
							<option value="" hidden>-- Search group prefix --</option>
						</select>
					</div>
					<div class="col-3">
						<label class="mb-3">Status</label>
						<select class="form-control">
							<option value="" hidden>-- Search status --</option>
							<option value="">Waiting</option>
							<option value="">Received</option>
						</select>
					</div>
					<div class="col-3">
						<label class="mb-3">&nbsp;</label>
						<div class="input-group">
							<button type="submit" class="btn btn-primary">Search</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php if(!empty($date)){ ?>
	<div class="card">
		<div class="card-header">
			<? /*<form action="<?php echo $action_import; ?>" method="post" enctype="multipart/form-data">
				<div class="form-group row">
					<label for="" class="col-sm-3 col-md-2 col-form-label text-left">Import Excel</label>
					<div class="col-sm-9 col-md-10">
						<div class="input-group">
							<div class="custom-file">
								<input type="hidden" name="date_wk" value="<?php echo $_GET['date_wk'];?>">
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
			<hr />*/?>
			<span class="float-left">
				<div>
					<!-- <div>
						<input type="radio" name="rdo" value="rdo" value="" id="rdo1" checked><label for="rdo1">Duratack-PG</label>
						<input type="radio" name="rdo" value="rdo" value="" id="rdo2"><label for="rdo2">Duratack-PG</label>
					</div> -->
					<!-- <a href="<?php echo route('barcode/PPDOrder'); ?>" class="btn btn-info">Export PDF</a> -->
					<a href="<?php echo route('barcode/export_excel_range_barcode&date='.$date); ?>" class="btn btn-success">Export Excel</a>
				</div>
			</span>
			<span class="float-right">
				<a href="<?php echo route('purchase'); ?>" class="btn btn-danger">Add Barcode</a>
			</span>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<?php if(get('result')=='success'){?>
				<div class="alert alert-success"><b>Success</b></div>
			<?php } ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="makeEditable">
					<thead>
						<tr>
							<!--<th><input type="checkbox" /></th>-->
							<th class="text-center" width="12%">Group prefix</th>
							<th class="text-center" width="12%">Start</th>
							<th class="text-center" width="12%">End</th>
							<th class="text-center" width="12%">Qty</th>
							<th class="text-center" width="15%">Status</th>
							<th>Purchase date</th>
							<th>Create by</th>
							<th width="10%"></th>
							<!-- <th name="buttons" style="width:50px;"></th> -->
						</tr>
					</thead>
					<tbody>
						<?php foreach($list_group as $val){ ?>
						<tr>
							<!--<th><input type="checkbox" /></th>-->
							<td class="text-center"><?php echo $val['group_code']; ?></td>
							<td class="text-center"><?php echo $val['start']; ?></td>
							<td class="text-center"><?php echo $val['end']; ?></td>
							<td class="text-center"><?php echo number_format($val['remaining_qty'], 0);?></td>
							<td class="text-center">
								<?php if($val['barcode_use']==1) : ?>
								<span class="text-primary">Received</span>
								<?php else: ?>
								<a href="index.php?route=barcode/changeStatus&id=<?php echo $val['id_group'];?>&status=1&date=<?php echo get('date');?>" class="btn btn-outline-primary btn-sm">Waiting -> Received</a>
								<?php endif; ?>
							</td>
							<td><?php echo $val['date_added']; ?></td>
							<td><?php echo $val['username']; ?></td>
							<td class="text-center"><a href="#" class="btn btn-danger btn-sm">Remove</a></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<!--end table-->
		</div>
		<!--end card-body-->
	</div>
	<!--end card-->
	<?php } ?>
</div>
<form 
	action="<?php echo $action_import_excel;?>" 
	method="POST" 
	id="form-import-excel" 
	enctype="multipart/form-data"
	style="display:none;"
>

	<input type="file" name="file_import" id="import_file" 
	accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
	<input type="text" class="form-control" name="date" value="<?php echo $date; ?>">
</form>
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
        <p>Are you sure remove some barcode <b><?php echo $textalert;?></b></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a href="<?php echo $confirm_remove_barcode;?>" type="button" class="btn btn-primary">Confirm</a>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>
<script>
$(document).ready(function () {
	<?php if (!empty($textalert)): ?>
		//alert("<?php echo $textalert;?>");
		$('#modal_textalert').modal('show');
	<?php endif; ?>
});
	$(document).on('click','.btn-del',function(e){
		var id_group = $(this).attr('id_group');
		$.ajax({
			url: 'index.php?route=barcode/deleteGroup',
			type: 'POST',
			dataType: 'json',
			data: {
				id_group:id_group
			},
		})
		.done(function(a) {
			location.reload();
			console.log("success");
		})
		.fail(function(a,b,c) {
			console.log(a);
			console.log(b);
			console.log(c);
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	});
	$(document).on('click','#import_excel',function(e){
		$('#import_file').trigger('click');
	});
	$(document).on('change','#import_file',function(e){
		var ele = $(this);
		var date = $('#date').val();

		var file_data = $('#import_file').prop('files')[0];   
	    var form_data = new FormData();                  
	    form_data.append('file_import', file_data);
	    form_data.append('date', date);
		$.ajax({
			url: 'index.php?route=barcode/listGroup',
			cache: false,
	        contentType: false,
	        processData: false,
	        dataType: 'text',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(e) { 
			location.reload();
			// window.location = 'index.php?route=barcode/listGroup&date='+date+'&result=success';
			console.log(e);
			console.log("success");
		})
		.fail(function(a,b,c) {
			console.log(a);
			console.log(b);
			console.log(c);
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		// location.reload();
	});
	$(document).on('change', '.change_status', function(){
		var ele = $(this);
		var valid = $(this).data('id');
		var valstatus_id = $(this).val();

		$.ajax({
			url: 'index.php?route=barcode/changeStatus',
			type: 'POST',
			data: {
				id: valid,
				status_id: valstatus_id
			},
		})
		.done(function(e) { 
			// window.location = 'index.php?route=barcode/listGroup&date='+date+'&result=success';
			console.log(e);
			console.log("success");
			// location.reload();
		})
		.fail(function(a,b,c) {
			console.log(a);
			console.log(b);
			console.log(c);
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	});
</script>