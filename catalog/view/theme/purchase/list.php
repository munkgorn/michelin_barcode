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
									<?php echo sprintf('%03d', $val['group']); ?>
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
									<?php echo sprintf('%03d', $val['group']); ?>
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
					<a href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success <?php echo !$validated?'disabled':''?>" <?php echo !$validated?'disabled="disabled"':''?>><i class="fas fa-file-excel"></i> Export Excel</a>
				</div>
				<div class="col-6 text-right">
					<button type="submit" class="btn btn-outline-primary"><i class="fas fa-check-double"></i> Validated</button>
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
										<th class="text-center"><span id="default_start_year"></span></th>
										<th class="text-center"><span id="default_end_year"></span></th>
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
										<td class="text-center"><?php echo sprintf('%03d', $val['group_code']); ?></td>
										<td class="text-center"><label for="" class="start"><?php echo sprintf('%08d', $val['barcode_start']); ?></label></td>
										<td class="text-center"><label for="" class="end"><?php echo sprintf('%08d', $val['barcode_end']); ?></label></td>
										<td class="text-center">
											<input 
												type="text" 
												class="form-control qty_group <?php echo $val['status_id']==0&&$val['remaining_qty']>0?'is-invalid':'';?>" 
												placeholder="QTY." 
												data-id="<?php echo $val['group_code'];?>"
												start="<?php echo $val['barcode_start'];?>"
												end="<?php echo $val['barcode_end'];?>" 
												default_start="<?php echo $val['default_start'];?>"
												default_end="<?php echo $val['default_end'];?>"
												name = "qty[<?php echo $val['group_code']; ?>]"
												maxlength="6"
												value="<?php echo $val['status_id']==0&&$val['remaining_qty']>0 ? $val['remaining_qty'] : '';?>"
												<?php echo $val['status_id']==0&&$val['remaining_qty']>0 ? 'disabled="disabled"' : '';?>
												autocomplete="off"
											>
										</td>
										<td class="text-center">
										<span class="load_default_start" data-group="<?php echo sprintf('%03d', $val['group_code']);?>"></span>
										<!-- <?php echo !empty($val['barcode_start_year']) ? sprintf('%08d', $val['barcode_start_year']) : '';?> -->
										</td>
										<td class="text-center">
										<span class="load_default_end" data-group="<?php echo sprintf('%03d', $val['group_code']);?>"></span>
										<!-- <?php echo !empty($val['barcode_end_year']) ? sprintf('%08d', $val['barcode_end_year']) : '';?> -->
										</td>
										<td class="text-center">
											
											<?php echo sprintf('%08d', $val['default_start']);?>
											<!-- <input type="text" class="form-control default_start" id_group="<?php echo $val['id_group'];?>" value="<?php echo $val['default_start'];?>"> -->
										</td>
										<td class="text-center">
											<?php echo sprintf('%08d', $val['default_end']);?>
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
<script type="text/javascript">
$(document).ready(function () {
	
	init();
	
	$(document).on('keyup','.qty_group',function(e){
		var ele = $(this);

		var qty = parseInt(ele.val());
		var start = parseInt(ele.attr('start'));
		var end = parseInt(ele.parent('td').prev('td').children('.end').html());
		var default_start = parseInt(ele.attr('default_start'));
		var default_end = parseInt(ele.attr('default_end'));
		var groupcode = ele.data('id');


		// Check calcurate with condition when 'barcode end' is more than 'default end' just reset to 'default start' and calcurate
		let check = () => {
			let newend = start + qty - 1;	
			if (newend > default_end) {
				let cal = (default_start-1) + (qty - (default_end - start) - 1);
				newend = cal;
				console.log('This barcode is running more than default end : ' + default_end);
			}	
			return newend;
		}
		// send now 'barcode end' to check in database used? or not? in 'x' day
		let sendBarcodeCheck = (num1) => {
			$.ajax({
				type: "POST",
				url: "index.php?route=purchase/checkBarcodeUsed",
				data: {barcode: num1},
				dataType: "json",
				success: function (response) {
					var obj = JSON.parse(response);
					console.log(obj);
					if (obj.id_barcode > 0) {
						console.log('Found barcode is cannot use');
						ele.val('');
						ele.parents('tr').find('.end').text('00000000');
						alert('ไม่สามารถใช้ barcode '+pad(num1, 8)+' ได้ เนื่องจากอยู่ภายใต้เงื่อนไขใช้ซ้ำภายในจำนวน x วัน');
						barcodeUsed = true;
					} else {
						console.log('Can use');
					}
				}
			});
		}

		let barcodeUsed = false;
		let newstart = 0;
		let sum_end_qty = 0;
		if (qty>100000) {
			console.log('Alert bug input qty is more than 100,000')
			alert('This barcode is more than limit input, please key in maximum 100,000. ');
			ele.val('');
			ele.parents('tr').find('.end').text('00000000');
			return 0;
		} else {
			newstart = check();
			console.log('Barcode End : ' + newstart);
			sendBarcodeCheck(newstart);
			if (qty>0 && !barcodeUsed) {
				sum_end_qty = newstart > 0 ? newstart : (start + qty - 1); // ! Change `End` 
				var end_string = pad(sum_end_qty,8);
				if (isNaN(end_string)==false) {
					ele.parents('tr').find('.end').text(end_string);
				}
				var dataPost = {
					start_group: '<?php echo $start_group;?>',
					end_group: '<?php echo $end_group;?>',
					group_code: groupcode,
					change_qty: qty,
					change_end: end_string
				}
				saveForExport(dataPost);
			} else {
				ele.parents('tr').find('.end').text('000000');
			}
		}
	});

	


	
});
</script>
<script>
const loading = '<img src="assets/loading.gif" height="30" /> Loading...';
let init = () => {
	$('.select2start, .select2end').select2({
		placeholder: "Select group barcode"
	});
	loadYear();
	loadBarcode();
}
let loadYear = () => {
	console.log('loading year...');
	$('#default_start_year').html(loading);
	$('#default_end_year').html(loading);
	$.ajax({
		type: "GET",
		url: "index.php?route=purchase/ajaxDefaultDate",
		dataType: "json",
		async:true,
		success: function (response) {
			console.log("Load year success");
			const obj = jQuery.parseJSON(response);
			$('#default_start_year').html(obj.start);
			$('#default_end_year').html(obj.end);
		}
	});
}
let loadBarcode = () => {
	console.log('Loading barcode...');
	$('.load_default_start').each(function(){
		$(this).html(loading);
	});
	$('.load_default_end').each(function(){
		$(this).html(loading);
	});
	$.ajax({
		type: "GET",
		url: "index.php?route=purchase/ajaxGroupDefault",
		dataType: "json",
		success: function (response) {
			console.log("Load barcode success");
			const obj = jQuery.parseJSON(response);
			$.each(obj, function(index, value){
				$('.load_default_start[data-group='+pad(index,3)+']').html(value.start);
				$('.load_default_end[data-group='+pad(index,3)+']').html(value.end);
			});
		}
	});
}
let saveForExport = (dataPost) => {
	$.ajax({
		type: "POST",
		url: "<?php echo $action_ajax;?>",
		data: dataPost,
		success: function (response) {
			// console.log(response);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status);
			console.log(thrownError);
		}
	});
}
let pad = (str,max) => {
	str = str.toString();
	return str.length < max ? pad("0" + str, max) : str;
}
</script>
