<div class="page-wrapper">
    
    
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Barcode FreeGroup</h4>
            
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			
	</div>
	<div class="card">
		<form action="<?php echo $action; ?>" method="POST">
		<div class="card-header">
			<div class="row">
				<div class="col-6">
					<!-- <button type="button" class="btn btn-outline-info " data-toggle="modal" data-target="#ModalSize" <?php echo $hasValidated ? 'disabled="disabled"' : '';?>><i class="fas fa-plus-circle"></i> Add Menual Size</button> -->
				</div>
				<div class="col-6 text-right">
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
									<th class="text-center">Free Group</th>
									<th class="text-center">Qty</th>
								</tr>
							</thead>
							<tbody>
                
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!--end card-body-->
		</form>
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
	function pad (str, max) {
		str = str.toString();
		return str.length < max ? pad("0" + str, max) : str;
	}
  $.ajax({
    type: "POST",
    url: "index.php?route=association/jsonFreeGroup",
    dataType: "json",
    success: function (response) {
      
      let string = response[0];
      let json = JSON.parse(string);
      console.log(json);
      let html = '';
      $.each(json, function (indexInArray, valueOfElement) { 
         html += "<tr>";
         html += "<td class='text-center'>"+pad(valueOfElement.group,3)+"</td>";
         html += "<td class='text-center'>"+addCommas(valueOfElement.qty)+"</td>";
         html += "</tr>";
      });
      console.log(html);
      $('#makeEditable tbody').html(html);
    }
  });
});
</script>