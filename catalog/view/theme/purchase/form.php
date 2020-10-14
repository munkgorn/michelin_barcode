<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Add barcode</h4>
			<p class="text-muted mb-0">Add barcode</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<form action="">
				<div class="row">
					<div class="col-3">
						<label class="mb-3">Code prefix</label>
						<div class="input-group">
							<input type="text" class="form-control" name="barcode_prefix">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-3">Barcode start</label>
						<div class="input-group">
							<input type="date" class="form-control" name="barcode_start">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-3">Barcode End</label>
						<div class="input-group">
							<input type="date" class="form-control" name="barcode_end">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-3">Factory</label> 
						<select class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
							<option>Select All Factory</option>
							<optgroup label="Bangkok">
							<option value="">Phra Pradaeng</option>
							<option value=""></option>
						</optgroup>
						</select>
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