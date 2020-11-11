<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Import Consumed Barcde</h4>
			<p class="text-muted mb-0">cut barcode</p>
		</div>


		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-sm-12">
                    <form action="<?php echo $action_import; ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label for="" class="col-sm-12 text-left">Import CSV</label>
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <!-- <input type="hidden" name="date_wk" value="<?php echo $_GET['date_wk']; ?>">-->
                                        <input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required  />  >
                                        <label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse CSV File (.csv)</label>
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
				
			</span>
			<span class="float-right">
				<!-- <button type="button" class="btn btn-sm btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#ModalAddMenual">Add Barcode</button> -->
			</span>
		</div>
		<!--end card-header-->
		<div class="card-body">

			<div class="table-responsive">
				<table class="table table-bordered" id="table_result">
					<thead>
						<tr>
							<th width="25%">Group Prefix</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
                    <?php foreach ($group as $value) : ?>
                        <tr>
                            <td><?php echo $value;?></td>
							<td><button class="findrange btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_show_range" data-group="<?php echo (int)$value;?>">Find range not used.</button></td>
                        </tr>
                    <?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<!--end card-body-->
	</div>
	<!--end card-->
</div>



<div class="modal" tabindex="-1" id="modal_show_range">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Range Group <span id="grouptitle"></span> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<input type="hidden" name="barcodegroup" value="" />
	   <input type="hidden" name="barcodeall" value="[]" />
	   <input type="hidden" name="barcodemax" value="[]" />
        <table class="table table-bordered">
			<thead>
				<tr>
					<th>Range Barcode</th>
					<th>QTY</th>
				</tr>
			</thead>
			<tbody id="modallist">
			</tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ignore</button>
        <button type="button" data-group="" class="btnrm rmall btn btn-primary" >Remove all</button>
		<button type="button" data-group="" class="btnrm rmmax btn btn-danger" >Remove qty < (<?php echo $maximum;?>)</button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function () {
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
	// $('.findrange').click(function() {
	// 	let group = $(this).data('group');
	// 	let modal = $('#modal_show_range');

	// 	modal
	// 	console.log(group);
	// });
	$('.rmall').click(function(){
		if (confirm('Are you sure remove all?')) {
			var groupcode = $('#modal_show_range [name=barcodegroup]').val();
			var b = $('#modal_show_range [name=barcodeall]').val();
			if (groupcode>0) {
				console.log('all' + groupcode);
				$.post("index.php?route=barcode/ajaxRemoveRange", {group:groupcode, barcode: b},
					function (data, textStatus, jqXHR) {
						// if (data==1) {
							$('#modal_show_range').modal('hide');
						// }
					}
				);
			}
		}
	});
	$('.rmmax').click(function(){
		if (confirm('Are you sure remove maximum?')) {
			var groupcode = $('#modal_show_range [name=barcodegroup]').val();
			var b = $('#modal_show_range [name=barcodemax]').val();
			if (groupcode>0) {
				console.log('max' + groupcode);
				$.post("index.php?route=barcode/ajaxRemoveRange", {group:groupcode, barcode: b},
					function (data, textStatus, jqXHR) {
						// if (data==1) {
							$('#modal_show_range').modal('hide');
						// }
					}
				);
			}
		}
	});
	$('#modal_show_range').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Button that triggered the modal
		var groupdata = button.data('group') // Extract info from data-* attributes
		var modal = $(this)
		var table = $('#modallist');
		modal.find('#grouptitle').html(groupdata);
		modal.find('.btnrm').attr('data-group', groupdata);

		modal.find('[name=barcodeall],[name=barcodemax]').val('[]');
		modal.find('[name=barcodegroup]').val(groupdata);

		modal.find('button').attr('disabled','disabled').addClass('disabled');
		table.html('<tr><td colspan="2" class="text-center"><i class="fas fa-spinner fa-pulse"></i> Loading please wait...</td></tr>');
		$.post("index.php?route=barcode/ajaxGetRange", {group: groupdata},
			function (data, textStatus, jqXHR) {
				if (data.length==0) {
					table.html("<tr><td colspan='2' class='text-center'>Not found</td></tr>");
				} else if (data.length>0) {
					modal.find('button').removeAttr('disabled').removeClass('disabled');
					var html = '';
					var ball = JSON.parse(modal.find('[name=barcodeall]').val());
					var bmax = JSON.parse(modal.find('[name=barcodemax]').val());
					$.each(data, function (index,value) { 
						var style = '';
						ball.push(value.start+'-'+value.end);
						if (value.qty < parseInt('<?php echo $maximum;?>')) {
							style = 'text-danger';
							bmax.push(value.start+'-'+value.end);
						}
						 html += '<tr><td class="'+style+'">'+value.start+' - '+value.end+'</td><td class="'+style+'">'+addCommas(value.qty)+'</td></tr>';
					});
					modal.find('[name=barcodeall]').val(JSON.stringify(ball));
					modal.find('[name=barcodemax]').val(JSON.stringify(bmax));

					if (ball.length==0) {
						modal.find('button.rmall').attr('disabled','disabled').addClass('disabled');
					}
					if (bmax.length==0) {
						modal.find('button.rmmax').attr('disabled','disabled').addClass('disabled');
					}

					table.html(html);
				}
				// console.log(data.length);
			},
			"json"
		);
	})
});
</script>