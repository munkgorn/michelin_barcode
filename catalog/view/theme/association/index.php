<div class="page-wrapper">
    
    
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Barcode Association</h4>
			<p class="text-muted mb-0">Relationship size with group barcode.</p>
            
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-6">
					<form action="<?php echo $action_search; ?>" method="GET">
						<input type="hidden" name="route" value="association">
						<div class="row">
							<div class="col-6">
								<label class="">Import Date</label>
								<select name="date_wk" id="date_wk" class="form-control select2">
									<option value="">-</option>
									<?php foreach ($listDateWK as $val) {?>
									<option value="<?php echo $val['date_wk']; ?>" <?php echo ($val['date_wk'] == $date_wk ? 'selected' : ''); ?>>
										<?php echo $val['date_wk']; ?>
									</option>
									<?php }?>
								</select>
							</div>
							<div class="col-6">
								<label class="">&nbsp;</label>
								<div class="input-group">
									<button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Search</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-6">
					<form action="<?php echo $action_import; ?>" id="form-import-xlsx" method="POST" enctype="multipart/form-data">
						<div class="row">
							<div class="col-12">
								<label class="">Import excel file association</label>
								<div class="input-group">
									<div class="custom-file">
										<input type="file" name="excel_input" class="custom-file-input" id="inputFileImport" required />
										<label class="custom-file-label" for="inputFileImport">Choose file (.xlsx)</label>
									</div>
									<div class="input-group-append"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-file-excel"></i> Import</button></div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
            <div class="row mt-3">
                <div class="col-12">
                    <?php echo !empty($success) ? '<div class="alert alert-success" role="alert">'.$success.'</div>' : ''; ?>
                    <?php echo !empty($error) ? '<div class="alert alert-danger" role="alert">'.$error.'</div>' : ''; ?>
                </div>
            </div>
		</div>
	</div>
	<?php if (!empty($date_wk)) {?>
	<div class="card">
		<form action="<?php echo $action; ?>" method="POST">
		<div class="card-header">
			<div class="row">
				<div class="col-6">
					<a type="button" href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success "><i class="fas fa-file-excel"></i> Export Excel</a>
					<!-- <button type="button" class="btn btn-outline-info " data-toggle="modal" data-target="#ModalSize" <?php echo $hasValidated ? 'disabled="disabled"' : '';?>><i class="fas fa-plus-circle"></i> Add Menual Size</button> -->
				</div>
				<div class="col-6 text-right">
					<button type="submit"  class="btn btn-outline-primary "><i class="fas fa-check-double"></i> Validate Check</button>
				</div>
			</div>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<input type="hidden" name="date_wk" value="<?php echo $date_wk; ?>" />
			<div class="row">
				<div class="col-12">
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="makeEditable">
							<thead>
								<tr>
									<th witdh="5%" class="text-center">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="check_all" data-parsley-multiple="groups" data-parsley-mincheck="2">
											<label class="custom-control-label" for="check_all"></label>
										</div>
									</th>
									<th width="10%" class="text-center">Size Product</th>
									<th width="10%" class="text-center">Sum Product</th>
									<th width="10%" >Last Wk Mapping</th>
									<th width="10%" class="text-center">Remaining Qty</th>
									<th width="10%" class="text-center">Propose Wk0</th>
									<th width="10%" class="text-center">Remaining Qty</th>
									<th width="10%" class="text-center">Message</th>
									<th class="text-center">Validate Wk0</th>
								</tr>
							</thead>
							<tbody>
                                <?php if (count($list)>0): ?>
								<?php foreach ($list as $key => $val) {?>
								<tr>
									<td witdh="5%" class="text-center">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input cb" name="checkbox[<?php echo $val['id_product'];?>]" id="listcheck<?php echo $key;?>" data-parsley-multiple="groups" data-parsley-mincheck="2" value="1" <?php echo !empty($val['save']) ? 'checked':''; ?>>
											<label class="custom-control-label" for="listcheck<?php echo $key;?>"></label>
										</div>
									</td>
									<td class="text-center tdid tdsize" data-idproduct="<?php echo $val['id_product'];?>"><?php echo $val['size']; ?></td>
									<td class="text-center tdid tdsumprod" data-idproduct="<?php echo $val['id_product'];?>"><?php echo number_format($val['sum_prod'], 0); ?></td>
									<td class="text-center tdid tdlast" data-idproduct="<?php echo $val['id_product'];?>"><span class="last_wk" row="<?php echo $key; ?>"><?php echo $val['last_wk0']; ?></span></td>
									<td class="text-center tdid tdqty" data-idproduct="<?php echo $val['id_product'];?>"></td>
									<td class="text-center tdid tdpropose" data-idproduct="<?php echo $val['id_product'];?>"></td>
									<td class="text-center tdid tdproposeqty" data-idproduct="<?php echo $val['id_product'];?>"></td>
									<td class="text-center tdid tdmsg" data-idproduct="<?php echo $val['id_product'];?>" data-text="<?php echo $val['plain_message'];?>"></td>
									<td class="p-0">
										<input type="hidden" name="propose[<?php echo $val['id_product'];?>]" data-size="<?php echo $val['size'];?>" data-key="<?php echo $val['id_product'];?>" class="txt_propose" value="<?php echo (int)strip_tags($val['propose']);?>" />
										<input type="text" name="id_group[<?php echo $val['id_product']; ?>]" data-size="<?php echo $val['size'];?>" data-key="<?php echo $val['id_product'];?>" class="form-control form-control-sm txt_group" value="<?php echo $val['save']; ?>" style="height:43px;border-radius:0;" />
									</td>
								</tr>
								<?php } ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Not found data</td>
                                </tr>
                                <?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!--end card-body-->
		</form>
	</div>
	<?php }?>
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

<div class="modal fade" id="ModalSize" tabindex="-1" role="dialog" aria-labelledby="ModalSize1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h6 class="modal-title m-0 text-white" id="ModalSize1">Add Menual Size <?php echo $date_wk;?></h6><button type="button"
                    class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i
                            class="la la-times text-white"></i></span></button>
            </div>
			<!--end modal-header-->
			<form method="POST" action="<?php echo $action_addmenual;?>">
            <div class="modal-body">
                <input type="hidden" class="form-control" name="date_wk" value="<?php echo $date_wk;?>" />
                <div class="row">
					<div class="col-12">
						<label for="">Size</label>
						<input type="text" name="size_product_code" class="form-control" placeholder="Size product code" required />
					</div>
					<div class="col-12 mt-2">
						<label for="">Sum Product</label>
						<input type="number" name="sum_product" class="form-control" placeholder="Sum product" required />
					</div>
					<!-- <div class="col-12">
						<label for="">Remaining Qty</label>
						<input type="number" name="remaining_qty" class="form-control" placeholder="Remaining Qty" required />
					</div> -->
				</div>
            </div>
            <!--end modal-body-->
            <div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btn-sm">Add Size</button>
			</div>
			<!--end modal-footer-->
			</form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>

<script>
$(document).ready(function () {

	const loading = '<img src="assets/loading.gif" height="30" />Loading...';

	$('.tdqty').each(function (index, element) {
		// console.log(element);
		let idproduct = $(element).data('idproduct');
		let idgroup = $('.tdlast[data-idproduct='+idproduct+'] .last_wk').html();
		let idsize =  parseInt($('.tdsize[data-idproduct='+idproduct+']').html());
		let idsumprod =  $('.tdsumprod[data-idproduct='+idproduct+']').html();
		let oldmsg = $('.tdmsg[data-idproduct='+idproduct+']').data('text');

		$('.tdqty[data-idproduct='+idproduct+']').html(loading);
		$('.tdpropose[data-idproduct='+idproduct+']').html(loading);
		$('.tdproposeqty[data-idproduct='+idproduct+']').html(loading);
		if (oldmsg!='Relationship') {
			$('.tdmsg[data-idproduct='+idproduct+']').html(loading);
		}
		if (idgroup>0) {
			$.ajax({
				type: "POST",
				url: "index.php?route=association/ajaxCountBarcodeNotuse",
				data: {group: idgroup, id_product: idproduct},
				dataType: "json",
				async: true,
				cache:true,
				success: function (data) {
					console.log('Size '+idsize+' Group '+idgroup+' Qty '+data);

					if (data>0) {
						$(element).html(addCommas(data));
					} else {
						$(element).html(0);
					}

					$.ajax({
						type: "POST",
						url: "index.php?route=association/ajaxCondition",
						data: {size: idsize, sum_prod: idsumprod, last_wk: idgroup, qty: data, id_product: idproduct},
						dataType: "json",
						async: false,
						cache:true,
						success: function (data2) {
							if (data2.propose!=null) {
								$('.tdpropose[data-idproduct='+idproduct+']').html(data2.propose);
								$('.txt_propose[data-key='+idproduct+']').val(data2.propose);
							}
							if (data2.propose_remaining_qty!=null) {
								$('.tdproposeqty[data-idproduct='+idproduct+']').html(data2.propose_remaining_qty);
							}
							if (data2.message!=null) {
								$('.tdmsg[data-idproduct='+idproduct+']').html(data2.message);
								if (data2.message=='Free Group') {
									$('.tdpropose[data-idproduct='+idproduct+']').addClass('text-danger');
									$('.tdproposeqty[data-idproduct='+idproduct+']').addClass('text-danger');
									$('.tdmsg[data-idproduct='+idproduct+']').addClass('text-danger');
								}
							}

							let thisprq_str = data2.propose_remaining_qty;
							let thisprq = thisprq_str.replace(',','');

							$.ajax({
								type: "POST",
								url: "index.php?route=association/ajaxSavePropose",
								data: {id_product: idproduct, remaining_qty: data, propose: data2.propose, propose_remaining_qty: thisprq, message: data2.message},
								dataType: "json",
								async: false,
								success: function (response) {
									console.log(response);
								}
							});
						}
					});

				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log('Group '+idgroup+' Error');
					console.log(xhr.status);
					console.log(thrownError);
					$(element).html('');
				}

			});
		} else {
			$('.tdqty[data-idproduct='+idproduct+']').html('');
			$('.tdpropose[data-idproduct='+idproduct+']').html('');
			$('.tdproposeqty[data-idproduct='+idproduct+']').html('');
			if (oldmsg!='Relationship') {
				$('.tdmsg[data-idproduct='+idproduct+']').html('');	
			} else {
				$('.tdmsg[data-idproduct='+idproduct+']').html('<span class="text-primary">Relationship</span>');	
			}
		}
	});

	pasteFreegroup();

	function pasteFreegroup() {
		$.get('index.php?route=barcode/jsonFreeGroup', function(data){
			var json = JSON.parse(data);
			var temp = json;

			// Freegroup
			let jsonfreeuse = [];
			$.each(temp, function(i,v){
				jsonfreeuse.push(v.group);
			});

			// Group sync with old association on config day
			let oldsync = [];
			$.ajax({
				type: "GET",
				url: "index.php?route=association/ajaxCheckOldSync",
				dataType: "json",
				async: false,
				cache:true,
				success: function (response) {
					$.each(response, (i,v) => {
						oldsync.push(v);
					});
				}
			});

			// Diff 2 array
			// Real freegroup can use
			let indexFree = [];
			// let difference = []; // not use
			$.grep(jsonfreeuse, function(el, index) {
				if (jQuery.inArray(el, oldsync) == -1) {
					indexFree.push(index); // Save index freegroup
					// difference.push(el); // save group (not use)
				}
			});
			// console.log(difference);

			var i = 0;
			$('.tdpropose').each(function(index,value){
				var thishtml = parseInt($(this).html());
				
				
				var idproduct = $(this).data('idproduct');
				var thisqtylk_str = $('.tdqty[data-idproduct='+idproduct+']').html();
				var thisqtylk = parseInt(thisqtylk_str.replace(',', ''));
				var thismsg = $('.tdmsg[data-idproduct='+idproduct+']').data('text');

				var thissumpod_str = $('.tdsumprod[data-idproduct='+idproduct+']').html();
				var thissumpod = parseInt(thissumpod_str.replace(',',''));
				
				var row = $(this).attr('row');
				var last_wk = parseInt($('.tdlast[data-idproduct='+idproduct+'] .last_wk').html());
				var thissave = $('.txt_group[data-key='+idproduct+']').val();

				if (last_wk!=$(this).html()&&thismsg!='Relationship') {
					$(this).addClass('text-danger');
					// $(this).addClass('text-danger');
					$('.tdproposeqty[data-idproduct='+idproduct+']').addClass('text-danger');
					$('.tdmsg[data-idproduct='+idproduct+']').addClass('text-danger');
				}

				if (isNaN(thishtml)&&thissave.length==0 && typeof temp[indexFree[i]] != 'undefined' && thissumpod > 0&&thismsg!='Relationship') {
					// var oldqty = $(this).parent('td').prev('td').prev('td').prev('td').html();
					let thisgroup = pad(temp[indexFree[i]].group, 3); 
					let thisqty_str = temp[indexFree[i]].qty;
					let thisqty = parseInt(thisqty_str.replace(',',''));
					$(this).html(thisgroup);
					$('.txt_propose[data-key='+idproduct+']').val(thisgroup);
					$('.tdproposeqty[data-idproduct='+idproduct+']').html(addCommas(thisqty));
					$('.tdmsg[data-idproduct='+idproduct+']').html('Free Group');
					if (last_wk!=thisgroup) { // free group is not save 
						// $.ajax({
						// 	type: "POST",
						// 	url: "index.php?route=association/ajaxSavePropose",
						// 	data: {id_product: idproduct, remaining_qty: thisqtylk, propose: thisgroup, propose_remaining_qty: thisqty, message: 'Free Group'},
						// 	dataType: "json",
						// 	async: false,
						// 	success: function (response) {
						// 		console.log(response);
						// 	}
						// });
					}
					i++;
				}
			});
		},'json');
	}


	function pad (str, max) { // zero left pad
		str = str.toString();
		return str.length < max ? pad("0" + str, max) : str;
	}

	function addCommas(nStr) {
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		// console.log(fileName);
	});

	$('.select2').select2({
		placeholder: 'Select date upload file',
		allowClear: true
	});

	// $('#maxxkeEditable tbody tr').each(function(index, value) {
	// 	let thistr = $(this);
	// 	let lastwk = thistr.children('td:eq(3)').children('.last_wk').html();
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "index.php?route=association/ajaxCountBarcode",
	// 		data: {group: lastwk},
	// 		async: true,
	// 		success: function (response) {
	// 			thistr.children('td:eq(4)').html(addCommas(response));
	// 		}
	// 	});
		
	// });

	

	$('#check_all').change(function(){
		if ($(this).is(':checked')) {
			$('.cb').prop('checked',true);
		} else {
			$('.cb').prop('checked',false);
		}
	});

	$(document).on('keyup','.txt_group',function(e){
		let proposevalue = $(this).prev('input[type=hidden]').val();
		let thisvalue = $(this).val();
		let thiskey = $(this).data('key');

		$('.txt_propose').each(function(i,ele){
			let proposesize = $(ele).data('size');
			let elevalue = parseInt($(ele).val());
			let keyvalue = parseInt(thisvalue);
			let key = $(ele).data('key');
			if (key!=thiskey && elevalue==keyvalue) {
				alert('Incorrect propose with size : ' + proposesize);
			}
		});

		$('.txt_group').each(function(i,ele){
			let size = $(ele).data('size');
			let elevalue = parseInt($(ele).val());
			let key = $(ele).data('key');
			if (key!=thiskey && elevalue==thisvalue) {
				alert('Incorrect validated with size : ' + size);
			}
		});
		

		if (isNaN(parseInt(thisvalue))==false && parseInt(thisvalue)>0) {
			$('[name="checkbox['+thiskey+']"]').prop('checked', true);
		} else {
			$('[name="checkbox['+thiskey+']"]').prop('checked', false);
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
		}
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
	});

</script>