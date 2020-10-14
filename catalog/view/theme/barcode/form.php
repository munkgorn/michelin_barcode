<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Add barcode</h4>
			<p class="text-muted mb-0">Add barcode</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-3">
					<label class="mb-3">Group</label> 
					<select class="select2 mb-3 select2-multiple" style="width: 100%" multiple="multiple" data-placeholder="Choose">
						<?php for ($i=105; $i < 600; $i++) {  ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-6">
					<label class="mb-3">&nbsp;</label>
					<div class="input-group">
						<button type="submit" class="btn btn-primary">Search</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<p class="text-muted mb-0">Result select</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<form action="" id="formresult">
				<div class="row">
					<div class="col-2">
						<label class="mb-2">Group</label>
						<div class="">
							<b>105</b>
							<br>
							<small>Range <span class="text-danger">99,999</span></small>
							<input type="hidden" class="form-control" name="barcode_prefix" value="105">
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Barcode start</label>
						<div class="">
							<input type="text" class="form-control" name="barcode_start" value="10512002" readonly>
							<small><span class="text-success">10500001</span> <a href="#" title="Range that PPD/TC can order">?</a></small>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Barcode End</label>
						<div class="">
							<input type="text" class="form-control" name="barcode_end"  value="10512002" readonly>
							<small><span class="text-success">10599999</span> <a href="#" title="Range that PPD/TC can order">?</a></small>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Qty</label>
						<div class="input-group">
							<input type="text" class="form-control" name="barcode_qty">
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">21 Aug 20 (Start) <a href="#" title="First NB from oldest order">?</a></label>
						<div class="input-group">
							<label for="">10500001</label>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">22 Jan 22 (End) <a href="#" title="Last NB from lastest order">?</a></label>
						<div class="input-group">
							<label for="">10512002</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-2">
						<label class="mb-2">Group</label>
						<div class="">
							<b>106</b>
							<br>
							<small>Range <span class="text-danger">99,999</span></small>
							<input type="hidden" class="form-control" name="barcode_prefix" value="105">
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Barcode start</label>
						<div class="">
							<input type="text" class="form-control" name="barcode_start" value="10612002" readonly>
							<small><span class="text-success">10600001</span> <a href="#" title="Range that PPD/TC can order">?</a></small>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Barcode End</label>
						<div class="">
							<input type="text" class="form-control" name="barcode_end"  value="10612002" readonly>
							<small><span class="text-success">10699999</span> <a href="#" title="Range that PPD/TC can order">?</a></small>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">Qty</label>
						<div class="input-group">
							<input type="text" class="form-control" name="barcode_qty">
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">21 Aug 20 (Start) <a href="#" title="First NB from oldest order">?</a></label>
						<div class="input-group">
							<label for="">10600001</label>
						</div>
					</div>
					<div class="col-2">
						<label class="mb-2">22 Jan 22 (End) <a href="#" title="Last NB from lastest order">?</a></label>
						<div class="input-group">
							<label for="">10612002</label>
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-12">
						<div class="float-left">
							<a href="<?php echo route('barcode'); ?>" class="btn btn-default">back</a>
						</div>
						<div class="float-right">
							<input type="submit" value="Save" class="btn btn-primary">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.select2').on('select2:select', function (e) {
		console.log(e);
		// appendHTML();
	});

	function appendHTML(group) {
		var html = '<div class="row">'+
			'	<div class="col-2">'+
			'		<label class="mb-2">Group</label>'+
			'		<div class="">'+
			'			<b>'+group+'</b>'+
			'			<br>'+
			'			<small>Range <span class="text-danger">99,999</span></small>'+
			'			<input type="hidden" class="form-control" name="barcode_prefix" value="105">'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-2">'+
			'		<label class="mb-2">Barcode start</label>'+
			'		<div class="">'+
			'			<input type="text" class="form-control" name="barcode_start" value="10512002" readonly>'+
			'			<small><span class="text-success">10500001</span> <a href="#" title="Range that PPD/TC can order">?</a></small>'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-2">'+
			'		<label class="mb-2">Barcode End</label>'+
			'		<div class="">'+
			'			<input type="text" class="form-control" name="barcode_end"  value="10512002" readonly>'+
			'			<small><span class="text-success">10599999</span> <a href="#" title="Range that PPD/TC can order">?</a></small>'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-2">'+
			'		<label class="mb-2">Qty</label>'+
			'		<div class="input-group">'+
			'			<input type="text" class="form-control" name="barcode_qty">'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-2">'+
			'		<label class="mb-2">21 Aug 20 (Start) <a href="#" title="First NB from oldest order">?</a></label>'+
			'		<div class="input-group">'+
			'			<label for="">10500001</label>'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-2">'+
			'		<label class="mb-2">22 Jan 22 (End) <a href="#" title="Last NB from lastest order">?</a></label>'+
			'		<div class="input-group">'+
			'			<label for="">10512002</label>'+
			'		</div>'+
			'	</div>'+
			'</div>';
		$('#formresult').append(html);
	}
});
</script>