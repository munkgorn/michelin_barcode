<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">New Barcode Ordering</h4>
			<p class="text-muted mb-0">Purchase group barcode.</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<form action="<?php echo $action; ?>" method="GET">
				<input type="hidden" name="route" value="purchase">
				<div class="row">
					<div class="col-2">
						<label class="mb-3">Find start group</label>
						<div class="input-group">
							<select name="start_group" class="form-control select2start">
								<?php foreach ($result_group as $val) { ?>
								<option value="<?php echo $val['group']; ?>" <?php echo ($start_group==$val['group']?'selected':''); ?>>
									<?php echo $val['group']; ?>
								</option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-3">to group</label>
						<div class="input-group">
							<select name="end_group" class="form-control select2end">
								<?php foreach ($result_group as $key => $val) { ?>
								<option value="<?php echo $val['group']; ?>" <?php echo ($end_group==$val['group']?'selected':''); ?>>
									<?php echo $val['group']; ?>
								</option>
								<?php } ?>
							</select>
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
	</div>
	<?php if(!empty($start_group)){ ?>
	<div class="card">
	<form action="<?php echo $action; ?>" method="POST">
		<div class="card-header">
			<div class="row">
				<div class="col-12"><h4>Today : <?php echo date('Y-m-d'); ?></h4></div>
				<div class="col-6">
					<a href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</a>
				</div>
				<div class="col-6 text-right">
					<button type="submit" class="btn btn-outline-primary"><i class="fas fa-check-double"></i> Save</button>
				</div>
			</div>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<?php if($result){ ?>
				<div class="alert alert-success">
					<b>Update success</b>
				</div>
			<?php } ?>
			
				<input type="hidden" name="start_group" value="<?php echo $start_group; ?>">
				<input type="hidden" name="end_group" value="<?php echo $end_group; ?>">
				<div class="row">
					<div class="col-12">
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="makeEditable">
								<thead>
									<tr>
										<th class="text-center" rowspan="2">Group</th>
										<th class="text-center" colspan="3">Next Order</th>
										<th class="text-center"><?php echo $date_first_3_year;?></th>
										<th class="text-center"><?php echo $date_lasted_order;?></th>
										<th class="text-center" colspan="3">Prefix</th>
										<th rowspan="2">Status</th>
									</tr>
									<tr>
										<th class="text-center">Start</th>
										<th class="text-center">End</th>
										<th class="text-center">Qty</th>
										<th class="text-center">Start<br>(First NB from oldest order)</th>
										<th class="text-center">End<br>(Last NB from lastest order)</th>
										<th class="text-center">Start</th>
										<th class="text-center">End</th>
										<th class="text-center">Range</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($getMapping as $key => $val){ ?>
									<tr>
										<td class="text-center"><?php echo $val['group_code']; ?></td>
										<td class="text-center"><label for="" class="start"><?php echo sprintf('%06d', $val['barcode_start']); ?></label></td>
										<td class="text-center"><label for="" class="end"><?php echo sprintf('%06d', $val['barcode_end']); ?></label></td>
										<td class="text-center">
											<input 
												type="text" 
												class="form-control qty_group <?php echo $val['status_id']==0&&$val['remaining_qty']>0?'is-invalid':'';?>" 
												placeholder="QTY." 
												start="<?php echo $val['barcode_start'];?>"
												end="<?php echo $val['barcode_end'];?>" 
												name = "qty[<?php echo $val['group_code']; ?>]"
												value="<?php echo $val['status_id']==0&&$val['remaining_qty']>0 ? $val['remaining_qty'] : '';?>"
												<?php echo $val['status_id']==0&&$val['remaining_qty']>0 ? 'disabled="disabled"' : '';?>
											>
										</td>
										<td class="text-center"><?php echo $val['barcode_start_year'];?></td>
										<td class="text-center"><?php echo $val['barcode_end_year'];?></td>
										<td class="text-center">
											<?php echo $val['default_start'];?>
											<!-- <input type="text" class="form-control default_start" id_group="<?php echo $val['id_group'];?>" value="<?php echo $val['default_start'];?>"> -->
										</td>
										<td class="text-center">
											<?php echo $val['default_end'];?>
											<!-- <input type="text" class="form-control default_end" id_group="<?php echo $val['id_group'];?>" value="<?php echo $val['default_end'];?>"> -->
										</td>
										<td class="text-center">
											<?php echo number_format($val['default_range'], 0);?>
											<!-- <input type="text" class="form-control default_range" id_group="<?php echo $val['id_group'];?>" value="<?php echo $val['default_range'];?>"> -->
										</td>
										<td class="text-center"><?php echo $val['status'];?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 text-right">
						
					</div>
				</div>
		</div>
		<!--end card-body-->
	</form>
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
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>
<script>
	$('.select2start, .select2end').select2({
		placeholder: "Select group barcode"
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
	$(document).on('keyup','.default_start',function(e){
		var val = $(this).val();
		var id_group = $(this).attr('id_group');
		$.ajax({
			url: 'index.php?route=purchase/updateDefaultGroup',
			type: 'GET',
			dataType: 'json',
			data: {
				value: val,
				id_group: id_group,
				type: 'default_start'
			},
		})
		.done(function() {
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
	$(document).on('keyup','.default_end',function(e){
		var val = $(this).val();
		var id_group = $(this).attr('id_group');
		$.ajax({
			url: 'index.php?route=purchase/updateDefaultGroup',
			type: 'GET',
			dataType: 'json',
			data: {
				value: val,
				id_group: id_group,
				type: 'default_end'
			},
		})
		.done(function() {
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
	$(document).on('keyup','.default_range',function(e){
		var val = $(this).val();
		var id_group = $(this).attr('id_group');
		$.ajax({
			url: 'index.php?route=purchase/updateDefaultGroup',
			type: 'GET',
			dataType: 'json',
			data: {
				value: val,
				id_group: id_group,
				type: 'default_range'
			},
		})
		.done(function() {
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
	//
	$(document).on('keyup','.qty_group',function(e){
		var ele = $(this);

		var qty = parseInt(ele.val());
		var start = parseInt(ele.attr('start'));
		var end = parseInt(ele.attr('end')); // ? Not Use

		var sum_end_qty = start + qty - 1; // ! Change `End` 
		var end_string = pad(sum_end_qty,6);
		if (isNaN(end_string)==false) {
			ele.parents('tr').find('.end').text(end_string);
		}
		
	});
	function pad(num, size) {
	    var s = num+"";
	    while (s.length < size) s = "0" + s;
	    return s;
	}
</script>
