<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Report remaining stock barcode</h4>
			<p class="text-muted mb-0">this list barcode received and you can use.</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<form>
				<div class="row">
					<div class="col-3">
							<label for="">Group Prefix</label>
							<select name="" id="groupFilter" class="form-control select2group">
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
	</div>
	<div class="card">
        <form action="<?php echo $action;?>" method="post">
		<div class="card-header">
            <div class="row">
                <div class="col-sm-6">
					<a href="<?php echo $export_excel; ?>" id="linkexport" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                </div>
                <div class="col-sm-6 text-right">
                </div>
            </div>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="table_result">
					<thead>
						<tr>
							<th class="text-center" width="25%">Group prefix</th>
							<th class="text-center" width="50%">Range barcode</th>
                            <th class="text-center">Remaining QTY</th>
						</tr>
					</thead>
					<tbody>
                   
					</tbody>
				</table>
			</div>
			<!--end table-->
		</div>
		<!--end card-body-->
        </form>
	</div>
	<!--end card-->
</div>
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});

$(document).ready(function(){
	const trnotfound = '<tr><td colspan="3" class="text-center">Please select group</td></tr>';
	const trloading = '<tr><td colspan="3" class="text-center"><img src="assets/loading.gif" height="30" /><br>Loading...</td></tr>';
	const table = $('#table_result tbody');
	const inputDate = $('#datefilter');
	const inputGroup = $('#groupFilter');
	const linkexport = $('#linkexport');

	let pad = (str, max) => { // zero left pad
		str = str.toString();
		return str.length < max ? pad("0" + str, max) : str;
	}


	linkexport.attr('disabled','disabled').addClass('disabled');

	$.post("index.php?route=barcode/ajaxGetGroupByDate", {status:0},
		function (data, textStatus, jqXHR) {
			let option = '<option></option>';
			option += '<option value="0">All Group</option>';
			$.each(data, function(index,value) {
				// option += '<option value="'+value.barcode_prefix+'">'+value.barcode_prefix+'</option>';
				option += '<option value="'+value.group_code+'">'+pad(value.group_code,3)+'</option>';
				// console.log(value);
			});
			$('#groupFilter').html(option).select2({
				placeholder: "Select group"
			});
		},
		"json"
	);

	table.html(trnotfound);
	$('#btnsearch').click(function(){
		linkexport.attr('disabled','disabled').addClass('disabled');
		table.html(trloading);
		// const filterDate = inputDate.val();
		const filterGroup = inputGroup.val();
		$break = false;
		if (filterGroup==0) {
			$confirm = confirm('Loading data all group is so many data and slowly, Are you sure loading?');
			if ($confirm==false) {
				$break = true;
				// table.html(trnotfound);
				linkexport.attr('disabled','disabled').addClass('disabled');
			} else {
				$break = true;
				window.location.href = "index.php?route=report/all";
			}
		}
		if ($break==false) {
			$.post("index.php?route=barcode/calcurateBarcode", {group: filterGroup, status: 0, flag: true},
				function (data, textStatus, jqXHR) {
					console.log(data);
					if (data.length > 0) {
						let sum = 0;
						let start = '';
						let end = '';
						let prefix = '';
						let html = '';
						$.each(data, function(index,value) {
							if (start=='') {
								start = value.barcode_start;
							}
							prefix = value.group_code;
							end = value.barcode_end;
							html += '<tr>';
							html += '<td class="text-center">'+pad(value.group_code,3)+'</td>';
							html += '<td class="text-center">'+pad(value.barcode_start,8)+' - '+pad(value.barcode_end,8)+'</td>';
							html += '<td class="text-center">'+(value.barcode_qty)+'</td>';
							html += '</tr>';
							sum += parseInt(value.barcode_qty);
						});

						html += '<tr>';
						html += '<td class="text-right" colspan="2">Total</td>';
						html += '<td class="text-center">'+sum+'</td>';
						html += '</tr>';
						table.html(html);
						linkexport.removeAttr('disabled').removeClass('disabled').attr('href', "index.php?route=export/report&group=" + inputGroup.val());
					} else {
						linkexport.attr('disabled','disabled').addClass('disabled');
						table.html('<tr><td colspan="3" class="text-center">Not found data in group '+inputGroup.val()+'</td></tr>');
					}
				},
				"json"
			);
		}
	});

		
});
</script>