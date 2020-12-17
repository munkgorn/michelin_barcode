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

		<div class="progress mb-3" style="height:20px;">
			<div id="mainload" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
		</div>

			<div class="table-responsive">
				<table class="table table-bordered" id="table_result">
					<thead>
						<tr>
							<th>Group Prefix</th>
							<th width="50%">Loading</th>
							<th width="25%">Message</th>
							<!-- <th width="15%">Action</th> -->
						</tr>
					</thead>
					<tbody>
                    <?php foreach ($group as $value) : ?>
                        <tr>
                            <td><?php echo sprintf('%03d',$value);?></td>
							<td>
								<div class="progress">
									<div class="load<?php echo $value;?> progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
								</div>
							</td>
							<td></td>
							<!-- <td><button class="findrange btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_show_range" data-group="<?php echo (int)$value;?>">Find range not used.</button></td> -->
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
	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});

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

	const countall = parseInt('<?php echo count($group);?>');
	const perItem = Math.ceil(100 / countall);

	function setMain(add) {
		const ele = $('#mainload');
		let nowpercent = parseFloat(ele.attr('aria-valuenow'));
		let newpercent = nowpercent + parseFloat(add);
		if (newpercent>100) {
			newpercent = 100.00;
		} 
		newpercent = newpercent.toFixed(2);

		ele.attr('aria-valuenow', newpercent).css('width', newpercent+'%').html(newpercent+'%');

		if (newpercent==100.00) {
			// if (nowpercent!=100.00) {
				alert('Process is successfull.');
				// window.location.href="index.php?route=loading/someone&key=freegroup,barcode&redirect=barcode/clearSession";
				window.location.href="index.php?route=barcode/clearSession";
			// }
			
			//$.get("index.php?route=barcode/clearSession", data,
			//	function (data, textStatus, jqXHR) {
			//		window.location.href="index.php?route=loading/someone&redirect=association&key=freegroup,year,barcode";		
			//	},
			//);/
			
		}
	}

	const success = [ 'background: green', 'color: white', 'display: block', 'text-align: center'].join(';');
	const failure = [ 'background: red', 'color: white', 'display: block', 'text-align: center'].join(';');

	// Loop check all
	$('#table_result tbody tr').each(function(index,value){
		let el = $(this).children('td:eq(0)');
		let groupcode = parseInt(el.html());
		let msg = $(this).children('td:eq(2)');
		let barcodeRange = [];
		$('.load'+groupcode).attr('aria-valuenow','5').css('width','5%');
		console.log(groupcode + ' start loading...');
		msg.html('Start loading...');
		$.ajax({
			type: "POST",
			url: "index.php?route=barcode/ajaxGetRange",
			data: {group:groupcode},
			dataType: "json",
			success: function (response) {
				// console.info('%c '+groupcode+' success get range', success);
				if (response.length>0) {
					$('.load'+groupcode).attr('aria-valuenow','20').css('width','20%');
					let nowwidth = parseInt($('.load'+groupcode).attr('aria-valuenow'));
					$.each(response, function(i,v){
						if (v.qty < parseInt('<?php echo $maximum;?>')) {
							barcodeRange.push(v.start+'-'+v.end);
							nowwidth++;
							$('.load'+groupcode).attr('aria-valuenow',nowwidth).css('width',nowwidth+'%');
						}
					});

					// console.log('Barcode Range : ');
					console.info('%c '+groupcode+' success get range ('+(barcodeRange.length)+') ', success);
					if (barcodeRange.length>0) {
						msg.html('Found barcode range < <?php echo $maximum;?> : '+barcodeRange.length+' unit.');
						console.table(barcodeRange);
						$.ajax({
							type: "POST",
							url: "index.php?route=barcode/ajaxRemoveRange",
							data: {group:groupcode,barcode:JSON.stringify(barcodeRange)},
							success: function (res) {
								console.info('%c '+groupcode+' done ', success);
								msg.html('Done, auto remove barcode range < <?php echo $maximum;?> success.');
								setMain(perItem);
								$('.load'+groupcode).attr('aria-valuenow','100').css('width','100%');
							}
						});
					} else {
						msg.html('Not found barcode range < <?php echo $maximum;?>');
						console.info('%c '+groupcode+' not found range length < 50 ', failure);
						setMain(perItem);
						$('.load'+groupcode).attr('aria-valuenow','100').css('width','100%');
					}

					
					
				} else {
					msg.html('Not found someone, done!!');
					console.info('%c '+groupcode+' not found range ', failure);
					setMain(perItem);
					$('.load'+groupcode).attr('aria-valuenow','100').css('width','100%');
				}
			}
		});
	});
	// let table = $('#table_result');
	// table.children('tbody').each('tr', function(index, value){
	// 	console.log(value);
		// $.ajax({
		// 	type: "POST",
		// 	url: "index.php?route=barcode/ajaxGetRange",
		// 	data: {group:groupdata},
		// 	dataType: "json",
		// 	success: function (response) {
		// 		console.log(response);
		// 		$('.load'+groupdata).attr('aria-valuenow','100').css('width','100');
		// 	}
		// });
		// $.post("index.php?route=barcode/ajaxGetRange", {group: groupdata},
		// 	function (data, textStatus, jqXHR) {
		// 		console.log(data);
		// 		$('.load'+groupdata).attr('aria-valuenow','100').css('width','100');
		// 	},
		// 	"json"
		// );
	// });


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
	// $('#modal_show_range').on('show.bs.modal', function (event) {
	// 	var button = $(event.relatedTarget) // Button that triggered the modal
	// 	var groupdata = button.data('group') // Extract info from data-* attributes
	// 	var modal = $(this)
	// 	var table = $('#modallist');
	// 	modal.find('#grouptitle').html(groupdata);
	// 	modal.find('.btnrm').attr('data-group', groupdata);

	// 	modal.find('[name=barcodeall],[name=barcodemax]').val('[]');
	// 	modal.find('[name=barcodegroup]').val(groupdata);

	// 	modal.find('button').attr('disabled','disabled').addClass('disabled');
	// 	table.html('<tr><td colspan="2" class="text-center"><i class="fas fa-spinner fa-pulse"></i> Loading please wait...</td></tr>');
	// 	$.post("index.php?route=barcode/ajaxGetRange", {group: groupdata},
	// 		function (data, textStatus, jqXHR) {
	// 			if (data.length==0) {
	// 				table.html("<tr><td colspan='2' class='text-center'>Not found</td></tr>");
	// 			} else if (data.length>0) {
	// 				modal.find('button').removeAttr('disabled').removeClass('disabled');
	// 				var html = '';
	// 				var ball = JSON.parse(modal.find('[name=barcodeall]').val());
	// 				var bmax = JSON.parse(modal.find('[name=barcodemax]').val());
	// 				$.each(data, function (index,value) { 
	// 					var style = '';
	// 					ball.push(value.start+'-'+value.end);
	// 					if (value.qty < parseInt('<?php echo $maximum;?>')) {
	// 						style = 'text-danger';
	// 						bmax.push(value.start+'-'+value.end);
	// 					}
	// 					 html += '<tr><td class="'+style+'">'+value.start+' - '+value.end+'</td><td class="'+style+'">'+addCommas(value.qty)+'</td></tr>';
	// 				});
	// 				modal.find('[name=barcodeall]').val(JSON.stringify(ball));
	// 				modal.find('[name=barcodemax]').val(JSON.stringify(bmax));

	// 				if (ball.length==0) {
	// 					modal.find('button.rmall').attr('disabled','disabled').addClass('disabled');
	// 				}
	// 				if (bmax.length==0) {
	// 					modal.find('button.rmmax').attr('disabled','disabled').addClass('disabled');
	// 				}

	// 				table.html(html);
	// 			}
	// 			// console.log(data.length);
	// 		},
	// 		"json"
	// 	);
	// })
});
</script>