<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">List barcode</h4>
			<p class="text-muted mb-0">Find list barcode</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-6">
					<form action="<?php echo route('barcode/association'); ?>" method="GET">
						<input type="hidden" name="route" value="barcode/association">
						<div class="row">
							<div class="col-6">
								<label class="mb-3">Find by genarater date</label>
								<div class="input-group">
									<select name="date_wk" id="date_wk" class="form-control select2">
										<option value="">-</option>
										<?php foreach($listDateWK as $val){ ?>
										<option value="<?php echo $val['date_wk']; ?>" <?php echo ($val['date_wk']==$date_wk?'selected':''); ?>>
											<?php echo $val['date_wk']; ?>
										</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-6">
								<div class="input-group">
									<label class="mb-3">&nbsp;</label>
									<div class="input-group">
										<button class="btn btn-primary" type="submit">Find</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-6">
					<form action="<?php echo $action_import;?>" id="form-import-xlsx" method="POST" enctype="multipart/form-data">
						<div class="row">
							<div class="col-6">
								<label class="mb-3">Upload file</label>
								<div class="input-group">
									<input type="file" name="excel_input" id="excel_input">
								</div>
							</div>
							<div class="col-6">
								<label class="mb-3">&nbsp;</label>
								<div class="input-group">
									<button type="submit" class="btn btn-warning">Import</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php if(!empty($date_wk)){ ?>
	<div class="card">
		<div class="card-header">
			<span class="float-left">
				<h4 class="card-title">Result</h4>
			</span>
			<span class="float-right">
				<a href="<?php echo $export_excel; ?>" class="btn btn-danger btn-sm">Export Excel</a>
				<button type="button" 
				class="btn btn-outline-primary btn-sm" 
				data-toggle="modal" 
				data-target="#exampleModalCenter">+ Add size</button>
			</span>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<form action="<?php echo $action_validate;?>" method="POST">
				<input type="hidden" name="date_wk" value="<?php echo $date_wk;?>" />
				<div class="row">
					<div class="col-12 mb-3">
						<div class="text-right">
							<button type="submit"  class="btn btn-primary">Validate</button>
						</div>
					</div>
					<div class="col-6">
						<div class="table-responsive">
							<table class="table table-bordered " id="makeEditable">
								<thead>
									<tr>
										<th witdh="10%"><input type="checkbox" class="form-control" id="check_all" /></th>
										<th width="20%" class="text-center">Size</th>
										<th width="20%" class="text-center">Sum prod.</th>
										<th >Last wk mapping</th>
										<th width="25%" class="text-center">Remaining Qty</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($list as $key => $val) { ?>
									<tr>
										<td witdh="10%"><input type="checkbox" class="form-control check" name="checkbox[]" /></td>
										<td class="text-center"><?php echo $val['size']; ?></td>
										<td class="text-center"><?php echo number_format($val['sum_prod'],0); ?></td>
										<td>
											<span class="last_wk" row="<?php echo $key;?>">
											<?php echo $val['last_wk0'];?>
											</span>
										</td>
										<td>
											<?php echo !empty($val['remaining_qty']) ? $val['remaining_qty'] : (!empty($val['last_wk0']) ? 0 : '');?>
										<?php /* if (!empty($val['last_wk0'])) { ?>
											<?php if (!empty($val['group_code'])&&$val['group_code']==$val['last_wk0']): ?>
												<?php echo number_format($val['group_code_remaining_qty'], 0); ?>
											<?php elseif (!empty($val['group_code'])&&$val['group_code']!=$val['last_wk0']): ?>
												<?php echo number_format($val['remaining_qty'], 0); ?>
											<?php elseif($val['sum_prod']>=$val['remaining_qty']) : ?>
												<span class="text-danger"><?php echo !empty($val['remaining_qty']) ? number_format($val['remaining_qty'],0) : ''; ?></span>
											<?php else : ?>
												<?php echo !empty($val['remaining_qty']) ? number_format($val['remaining_qty'],0) : '';?>
											<?php endif; ?>
										<?php } else { ?>
											<?php if (!empty($val['group_code_remaining_qty'])):?>
											<?php echo number_format($val['group_code_remaining_qty'],0); ?>
											<?php endif; ?>
										<?php }  */ ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-6">
						<div class="table-responsive">
							<table class="table table-bordered" id="makeEditableRight">
								<thead>
									<tr>
										<th style="width:150px;">Propose Wk0</th>
										<th style="width:150px;">Remaining Qty</th>
										<td>Message</td>
										<th style="width:150px;">Validated Wk0</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($list as $key => $val) { ?>
									<tr>
										<td>
										<span class="propose" row="<?php echo $key;?>"><?php echo $val['propose'];?></span>
										</td>
										<td><?php echo $val['propose_remaining_qty'];?></td>
										<td><?php echo $val['message'];?></td>
										<td class="p-0">
											<input type="text" name="id_group[<?php echo $val['id_product'];?>]" class="form-control form-control-sm txt_group" value="<?php echo $val['save'];?>" style="height:43px;border-radius:0;" />
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>
		<!--end card-body-->
	</div>
	<?php } ?>
</div>
<input type="hidden" name="date_wk" id="date_wk" value="<?php echo $date_wk; ?>">
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title m-0" id="exampleModalCenterTitle">Add size</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true"><i class="la la-times"></i></span>
				</button>
			</div>
			<!--end modal-header-->
			<div class="modal-body">
				<form action="#" id="form_add_size" method="POST">
					<div class="row">
						<div class="col-6 text-left align-self-center">
							<label for="">Size</label>
						</div>
						<div class="col-6 text-left align-self-center">
							<input type="text" class="form-control" id="add_size" name="add_size">
						</div>
						<!--end col-->
					</div>
					<div class="row">
						<div class="col-6 text-left align-self-center">
							<label for="">Sum prod.</label>
						</div>
						<div class="col-6 text-left align-self-center">
							<input type="text" class="form-control" id="add_sum_prod" name="add_sum_prod">
						</div> 
					</div>
					<div class="row">
						<div class="col-6 text-left align-self-center">
							<label for="">Remaining Qty</label>
						</div>
						<div class="col-6 text-left align-self-center">
							<input type="text" class="form-control" id="add_remaining_qty" name="add_remaining_qty">
						</div>
						<!--end col-->
					</div>
					<!--end row-->
				</form>
			</div>
			<!--end modal-body-->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary btn-sm" id="btn-add-form">Add</button>
			</div>
			<!--end modal-footer-->
		</div>
		<!--end modal-content-->
	</div>
	<!--end modal-dialog-->
</div>
<script>
$(document).ready(function () {

	$.get('index.php?route=barcode/jsonFreeGroup', function(data){
		var json = JSON.parse(data);
		// console.log(json);
		var i = 0;
		$('.propose').each(function(index,value){
			var thishtml = parseInt($(this).html());
			var row = $(this).attr('row');
			var last_wk = parseInt($('.last_wk[row="'+row+'"]').html());

			if (last_wk!=$(this).html()) {
				$(this).addClass('text-danger');
			}
			if (isNaN(thishtml)) {
				$(this).html(json[i]).parent('td').next('td').next('td').html('Free Group');
				if (last_wk!=json[i]) {
					$(this).addClass('text-danger');
				}
				i++;
			}
		});
	},'json');

	$('#check_all').change(function(){
		if ($(this).is(':checked')) {
			$('.check').prop('checked',true);
		} else {
			$('.check').prop('checked',false);
		}
	});

	$(document).on('focusin','.txt_group',function(e){
		var row = $(this).parent('td').parent('tr').index();
		$('#makeEditableRight tbody tr:eq('+row+')').addClass('trhover');
		$('#makeEditable tbody tr:eq('+row+')').addClass('trhover');
	});
	$(document).on('focusout','.txt_group',function(e){
		var row = $(this).parent('td').parent('tr').index();
		$('#makeEditableRight tbody tr:eq('+row+')').removeClass('trhover');
        $('#makeEditable tbody tr:eq('+row+')').removeClass('trhover');
		var ele = $(this);
		var date_wk = $('#date_wk').val();
		var group =parseInt( ele.val() );
		// ele.addClass('animateload');
		if(group>0){
			// $.ajax({
			// 	url: 'index.php?route=barcode/updateWkMapping',
			// 	type: 'GET',
			// 	dataType: 'json',
			// 	data: {
			// 		group: ele.val(),
			// 		size: ele.attr('size'),
			// 		date_wk: date_wk
			// 	},
			// })
			// .done(function(json) {
			// 	console.log(json);
			// 	ele.removeClass('animateload');
			// 	ele.addClass('text-success');
			// })
			// .fail(function(a,b,c) {
			// 	console.log(a);
			// 	console.log(b);
			// 	console.log(c);
			// 	console.log("error");
			// 	ele.removeClass('animateload');
			// 	ele.addClass('text-warning');
			// })
			// .always(function() {
			// 	ele.removeClass('animateload');
			// 	ele.addClass('text-success');
			// 	console.log("complete");
			// });
		}
	});
	$('#makeEditableRight tbody tr').hover(function(){
		var row = $(this).index();
		$('#makeEditableRight tbody tr:eq('+row+')').addClass('trhover');
		$('#makeEditable tbody tr:eq('+row+')').addClass('trhover');
	}, function () {
        var row = $(this).index();
		$('#makeEditableRight tbody tr:eq('+row+')').removeClass('trhover');
        $('#makeEditable tbody tr:eq('+row+')').removeClass('trhover');
    });
});
</script>
<script>
	$(document).on('click','#btn-add-form',function(e){
		// alert(1);
		// $('#form_add_size').submit(function(e){
			var add_size = $('#add_size').val();
			var add_sum_prod = $('#add_sum_prod').val();
			var date_wk = $('#date_wk').val();
			$.ajax({
				url: 'index.php?route=barcode/add_row_barcode',
				type: 'GET',
				dataType: 'json',
				data: {
					'add_size' : add_size,
					'add_sum_prod' : add_sum_prod,
					'date_wk'	: date_wk
				},
			})
			.done(function(a) {
				window.location = 'index.php?route=barcode/association&date_wk='+$('#date_wk').val();
				console.log(a);
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
		// });
		// alert(3);
	});
	
	$(function(e){
		// $('#form-import-xlsx').submit(function(e){
		// 	var form = $(this);
		// 	var file_data = $("#excel_input").prop("files")[0];   
		//     var form_data = new FormData();
		//     form_data.append("excel_input", file_data);

		// 	$.ajax({ 
		// 		url: 'index.php?route=size',
		// 		type: 'POST',
		// 		cache: false,
		//         contentType: false,
		//         processData: false,
		// 		data: form_data,
		// 	})
		// 	.done(function(json) {
		// 		location.reload();
		// 		console.log(json);
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
		// 	e.preventDefault();
		// });
	});	
</script>