<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Fyt PPD</h4>
			<p class="text-muted mb-0">Fyt PPD</p>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<span class="float-left"> 
				<h3>Import Cut Stock</h3>
			</span>
			<span class="float-right">
				<a href="#" class="btn btn-success">Import Excel</a>
			</span>
		</div>
		<div class="card-body bootstrap-select-1">
			<form action="" id="formresult">
				<?php for ($i=105;$i<=106;$i++) { ?>
				<div class="row mb-3">
					<div class="col-3">
						<label class="mb-2">Start Barcode</label>
						<div class="">
							<input type="text" class="form-control" name="" value="<?php echo $i;?>00043">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-2">End Barcode</label>
						<div class="">
							<input type="text" class="form-control" name="" value="<?php echo $i;?>99999">
						</div>
					</div>
				</div>
				<?php } ?>
				
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
