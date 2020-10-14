<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Add group</h4>
			<p class="text-muted mb-0">Add group</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<form action="" id="formresult">
				<div class="row">
					<div class="col-12 mb-3">
						<div class="form-group">
							<label class="mb-2">Group name</label>
							<input type="text" class="form-control" name="group_name" value="">
						</div>
					</div>
				</div>
			
				<hr />
				<div class="row mt-4">
					<div class="col-12">
						<div class="float-left">
							<a href="<?php echo route('user/group'); ?>" class="btn btn-default">back</a>
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


<script>
$(document).ready(function(){
	$('#config').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>