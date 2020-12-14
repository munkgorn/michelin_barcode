<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Report remaining stock barcode</h4>
			<p class="text-muted mb-0">this list barcode received and you can use.</p>
		</div>
		<!--end card-header-->
		
	</div>
	<div class="card">
        <form action="<?php echo $action;?>" method="post">
		<div class="card-header">
            <div class="row">
                <div class="col-sm-6">
					<a href="index.php?route=export/reportAll" id="linkexport" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</a>
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
	const loading = '<img src="assets/loading.gif" height="30" /> Loading...';
	const trloading = '<tr><td colspan="3" class="text-center">'+loading+'</td></tr>';
	const table = $('#table_result tbody');
	const inputDate = $('#datefilter');
	const inputGroup = $('#groupFilter');
	const linkexport = $('#linkexport');


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

	linkexport.attr('disabled','disabled').addClass('disabled');

	const success = [ 'background: green', 'color: white', 'display: block', 'text-align: center'].join(';');
	const failure = [ 'background: red', 'color: white', 'display: block', 'text-align: center'].join(';');

	table.html(trnotfound);

	$.post("index.php?route=barcode/ajaxGroupReceived", {},
		function (data, textStatus, jqXHR) {
			let tr = '';
			if (data.length > 0) {
				$.each(data, function(i, v){
					tr += '<tr class="trgroup'+v.group_code+'">';
						tr += '<td class="text-center">'+v.group_code+'</td>';
						tr += '<td class="text-center"><span class="loadrange" data-group="'+v.group_code+'">'+loading+'</span></td>';
						tr += '<td class="text-center"><span class="loadqty" data-group="'+v.group_code+'">'+loading+'</span></td>';
					tr += '</tr>';
				});
				table.html(tr);
				getAll();
			}
			
		},
		"json"
	);


	function getAll() {
		const success = [ 'background: green', 'color: white', 'display: block', 'text-align: center'].join(';');
		const failure = [ 'background: red', 'color: white', 'display: block', 'text-align: center'].join(';');

		let json = [];

		const countall = $('#table_result tbody tr td .loadrange').length;
		// console.log(countall);
		let num = 0;

		$('#table_result tbody tr td .loadrange').each(function(index, ele){
			let valuegroup = $(ele).data('group');
			
			// console.log(valuegroup);
			$.post("index.php?route=barcode/calcurateBarcode", {header:false, group: valuegroup, status:0, flag:0},
				function (data, textStatus, jqXHR) {
					num++;
					console.log(num+' '+countall);
					// console.log(data);
					if (data.length==0) {
						$(ele).parent('td').parent('tr.trgroup'+valuegroup).remove();
						// console.info('%c Group:'+valuegroup+' not found range, remove it! ', failure);
					} else {
						// console.log('Group : ' + valuegroup + ', Found : ' + data.length);
						
						let grouprange = [];
						let groupqty = [];
						$.each(data, function(i,v){
							grouprange.push(v.start + ' - ' + v.end);
							groupqty.push(addCommas(v.qty));
							
						});
						json.push(
							{
								group: valuegroup,
								range: grouprange,
								qty: groupqty
							}
						);
						console.info('%c Group:'+valuegroup+' Found : ' + data.length, success);
						if (grouprange.length>0) {
							$(ele).html(grouprange.join('<br>'));
							// console.table(grouprange);
						}
						if (groupqty.length>0) {
							$(ele).parent('td').next('td').children('.loadqty[data-group='+valuegroup+']').html(groupqty.join('<br>'));
						}

						
						if (num==countall) {
							// console.log(JSON.stringify(json));
							$.post("index.php?route=report/saveJson", {data: JSON.stringify(json)},
								function (data2, textStatus2, jqXHR2) {
									alert('You can export all group in type excel');
									$('#linkexport').removeAttr('disabled').removeClass('disabled');
								}
							);
						}
					}
				},
				"json"
			);
		});
		
	}
	


	// table.html(trnotfound);
	// $('#btnsearch').click(function(){
	// 	linkexport.attr('disabled','disabled').addClass('disabled');
	// 	table.html(trloading);
	// 	// const filterDate = inputDate.val();
	// 	const filterGroup = inputGroup.val();
	// 	$break = false;
	// 	if (filterGroup==0) {
	// 		$confirm = confirm('Loading data all group is so many data and slowly, Are you sure loading?');
	// 		if ($confirm==false) {
	// 			$break = true;
	// 			table.html(trnotfound);
	// 			linkexport.attr('disabled','disabled').addClass('disabled');
	// 		}
	// 	}
	// 	if ($break==false) {
	// 		$.post("index.php?route=barcode/calcurateBarcode", {status: 0, flag: true},
	// 			function (data, textStatus, jqXHR) {
	// 				if (data.length > 0) {
	// 					let sum = 0;
	// 					let start = '';
	// 					let end = '';
	// 					let prefix = '';
	// 					let html = '';
	// 					$.each(data, function(index,value) {
	// 						if (start=='') {
	// 							start = value.start;
	// 						}
	// 						prefix = value.barcode_prefix;
	// 						end = value.end;
	// 						html += '<tr>';
	// 						html += '<td class="text-center">'+value.barcode_prefix+'</td>';
	// 						html += '<td class="text-center">'+value.start+' - '+value.end+'</td>';
	// 						html += '<td class="text-center">'+value.qty+'</td>';
	// 						html += '</tr>';
	// 						sum += parseInt(value.qty);
	// 					});

	// 					html += '<tr>';
	// 					html += '<td class="text-right" colspan="2">Total</td>';
	// 					html += '<td class="text-center">'+sum+'</td>';
	// 					html += '</tr>';
	// 					table.html(html);
	// 					linkexport.removeAttr('disabled').removeClass('disabled').attr('href', "index.php?route=export/report&group=" + inputGroup.val());
	// 				} else {
	// 					linkexport.attr('disabled','disabled').addClass('disabled');
	// 					table.html('<tr><td colspan="3" class="text-center">Not found data in group '+inputGroup.val()+'</td></tr>');
	// 				}
	// 			},
	// 			"json"
	// 		);
	// 	}
	// });

		
});
</script>