<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">List barcode</h4>
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
									<button type="submit" class="btn btn-primary">Search</button>
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
										<!-- <input type="hidden" name="date_wk" value="<?php echo $_GET['date_wk'];?>">-->
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
		<?php if(get('result')=='success'){?>
			<div class="alert alert-success"><b>Success</b></div>
		<?php } ?>
		<div class="card-header">
			
			<span class="float-left">
				<?php if(!empty($date)){ ?>
					<a href="<?php echo $export_excel; ?>" class="btn btn-success">Export Excel</a>
					<!--<a href="#" class="btn btn-warning" id="import_excel">Import Excel</a>-->
				<?php } ?>
			</span>
			<span class="float-right">
				<a href="<?php echo route('purchase'); ?>" class="btn btn-danger">Add Barcode</a>
			</span>
		</div>
		<!--end card-header-->
		<div class="card-body">
		
			<?php if(!empty($date)){ ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="">
					<thead>
						<tr>
							<th style="width:30%;">Prefix</th>
							<th style="width:30%;">Barcode</th>
							<!-- <th>Status</th> -->
							<th>Used date</th>
							<th>Create by</th>
							<th name="buttons" style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<?php if($getImportBarcode){ ?>
							<?php foreach($getImportBarcode as $val) { ?>
							<tr>
								<td><?php echo $val['barcode_prefix']; ?></td>
								<td><?php echo $val['barcode_code']; ?></td>
								<!-- <td><span class="text-success">Use</span></td> -->
								<td><?php echo $val['date_added']; ?></td>
								<td><?php echo $val['username']; ?></td>
								<td name="buttons">
									<div class=" pull-right">
										<button id="bElim" type="button" class="btn btn-sm btn-soft-danger btn-circle" onclick="rowElim(this);">
										<i class="dripicons-trash" aria-hidden="true">
										</i>
										</button>
									</div>
								</td>
							</tr>
							<?php } ?>
						<?php }else{ ?>
							<tr>
								<td colspan="10" class="text-center">Empty</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div>Count row: <?php echo number_format($nums_row);?></div>
			<?php } ?>
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
        <p>Are you sure remove some barcode <b><br><?php echo $textalert;?></b></p>
      </div>
      <div class="modal-footer">
		<a href="<?php echo 'index.php?route=barcode/unconfirmImportBarcode';?>" type="button" class="btn btn-warning">Ignore</a>
        <a href="<?php echo $confirm_remove_barcode;?>" type="button" class="btn btn-primary">Confirm</a>
      </div>
    </div>
  </div>
</div>
<!-- <script src="assets/plugins/daterangepicker/daterangepicker.js"></script> -->
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
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>
<script>

<?php if (!empty($textalert)): ?>
		//alert("<?php echo $textalert;?>");
		$('#modal_textalert').modal('show');
	<?php endif; ?>
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
			url: 'index.php?route=barcode',
			cache: false,
	        contentType: false,
	        processData: false,
	        dataType: 'text',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(e) { 
			window.location = 'index.php?route=barcode&date='+date+'&result=success';
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
	// $('#form-import-excel').submit();
</script>