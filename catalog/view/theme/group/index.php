<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Barcode Reception</h4>
			<p class="text-muted mb-0">Group barcode purchased status.</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<form action="<?php echo $action; ?>" method="GET">
				<input type="hidden" name="route" value="group">
				<div class="row">
					<div class="col-3">
						<label class="mb-3">Purchase date</label>
						<select name="date" id="" class="form-control select2date">
							<?php foreach ($date_group as $date) : ?>
							<option 
								value="<?php echo date('Y-m-d', strtotime($date['date_purchase']));?>"
								<?php echo date('Y-m-d', strtotime($date['date_purchase']))==$filter_date ? 'selected' : '';?>>
								<?php echo date('d/m/Y', strtotime($date['date_purchase']));?>
							</option>
							<?php endforeach; ?>
						</select>
						<!-- <div class="input-group">
							<input type="text" class="form-control datepicker" 
							id="date" 
							name="date" 
							value="<?php echo $filter_date; ?>">
							<div class="input-group-append">
								<span class="input-group-text"><i class="dripicons-calendar"></i></span>
							</div>
						</div> -->
					</div>
					<div class="col-3">
						<label class="mb-3">Group barcode</label>
						<select name="group" class="form-control select2prefix">
							<option></option>
							<!-- <option value="" >-- Search group prefix --</option> -->
                            <?php foreach ($groups as $group) : ?>
                            <option value="<?php echo $group;?>" <?php echo $filter_group==$group?'selected':'';?>><?php echo sprintf('%03d',$group);?></option>
                            <?php endforeach; ?> 
						</select>
					</div>
					<div class="col-3">
						<label class="mb-3">Status</label>
						<select name="status" class="form-control select2status">
							<option></option>
							<!-- <option value="-1" >-- Search status --</option> -->
							<option value="waiting" <?php echo $filter_status==="waiting"?'selected':'';?>>Waiting</option>
							<option value="received" <?php echo $filter_status==="received"?'selected':'';?>>Received</option>
						</select>
					</div>
					<div class="col-3">
						<label class="mb-3">&nbsp;</label>
						<div class="input-group">
							<button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i> Search</button>
                            <a href="<?php echo $link_clear;?>" class="btn btn-outline-secondary ml-2">Clear</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php if(!empty($filter_date)){ ?>
	<div class="card">
        <form action="<?php echo $action_checkbox;?>" method="post">
		<div class="card-header">
            <div class="row">
                <div class="col-sm-6">
					<!-- <a href="<?php echo route('barcode/export_excel_range_barcode&date='.$filter_date); ?>" class="btn btn-success">Export Excel</a> -->
					<a href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                    <!-- <a href="<?php echo route('purchase'); ?>" class="btn btn-danger">Add Barcode</a> -->
                </div>
                <div class="col-sm-6 text-right">
					<button type="submit" class="btn btn-outline-primary"><i class="fas fa-check-double"></i> Waiting <i class="fas fa-chevron-right"></i> Receive</button>
                </div>
            </div>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="makeEditable">
					<thead>
						<tr>
							<th class="text-center" width="5%">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="checkall" data-parsley-multiple="groups" data-parsley-mincheck="2" />
										<label class="custom-control-label" for=""></label>
									</div>
								</div>
							</th>
							<th class="text-center" width="12%">Group prefix</th>
							<th class="text-center" width="12%">Start</th>
							<th class="text-center" width="12%">End</th>
							<th class="text-center" width="12%">Qty</th>
							<th class="text-center" width="15%">Status</th>
							<th>Purchase date</th>
							<th>Create by</th>
							<th width="5%">Remove</th>
						</tr>
					</thead>
					<tbody>
                    <?php if (count($lists)>0) : ?>
						<?php foreach($lists as $val){ ?>
						<tr>
							<th class="text-center">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input check cb" name="checkbox[]" data-parsley-multiple="groups" data-parsley-mincheck="2" value="<?php echo $val['id_group'];?>" <?php echo $val['barcode_use']==1?'disabled="disabled"':'';?> />
										<label class="custom-control-label" for=""></label>
									</div>
								</div>
							</th>
							<td class="text-center"><?php echo sprintf('%03d',$val['group_code']); ?></td>
							<td class="text-center">
							<?php 
							$start = $val['start'] - $val['remaining_qty'];
							if ($start<$val['default_start']) {
								$num1 = $val['start'] - $val['default_start'];
								$num2 = $val['default_end'] - ($val['remaining_qty'] - $num1);
								$start = $num2 + 1;
							}
							echo sprintf('%08d',$start); 
							?>
							</td>
							<td class="text-center"><?php echo sprintf('%08d',($val['start']-1)); ?></td>
							<td class="text-center"><?php echo number_format($val['remaining_qty'], 0);?></td>
							<td class="text-center">
								<?php if($val['barcode_use']==1) : ?>
								<span class="text-primary">Received</span>
								<?php else: ?>
								<a href="<?php echo $link_changestatus."&id=$val[id_group]";?>" class="btn btn-outline-info btn-sm">Waiting <i class="fas fa-chevron-right"></i> Receive</a>
								<?php endif; ?>
							</td>
							<td><?php echo $val['date_modify']; ?></td>
							<td><?php echo $val['username']; ?></td>
							<td class="text-center">
                                <a href="<?php echo $val['barcode_use']==0?$link_del.'&id='.$val['id_group']:'#';?>" class="btn btn-danger btn-sm <?php echo $val['barcode_use']==1?'disabled':'';?>" onclick="return confirm('Are you sure delete this purchase group?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
						</tr>
						<?php } ?>
                    <?php else: ?>
                    <tr>
                    <td colspan="8" class="text-center">Not found in search date:<?php echo $filter_date;?><?php echo !empty($filter_group)?", Group Prefix: $filter_group":'';?><?php echo $filter_status>=0?", Status: ".($filter_status==1?'Received':'Waiting'):'';?></td>
                    </tr>
                    <?php endif; ?>
					</tbody>
				</table>
			</div>
			<!--end table-->
		</div>
		<!--end card-body-->
        </form>
	</div>
	<!--end card-->
	<?php } ?>
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
	$('.select2date').select2({
		placeholder: 'Search group barcode',
		allowClear: true
	}) ;
	$('.select2prefix').select2({
		placeholder: 'Search group barcode',
		allowClear: true
	});
	$('.select2status').select2({
		placeholder: 'Search status purchased',
		allowClear: true
	});

	<?php if (!empty($textalert)): ?>
		//alert("<?php echo $textalert;?>");
		$('#modal_textalert').modal('show');
    <?php endif; ?>
    
    $('#checkall').change(function(){
        if( $(this).is(':checked') ) {
            $('.cb').prop('checked',true);
        } else {
            $('.cb').prop('checked',false);
        }
        $('.cb').each(function(index,el){
            if ($(el).attr('disabled') == 'disabled') {
                $(el).prop('checked', false);
            }
        });
    });
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