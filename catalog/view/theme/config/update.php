<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Patch Update software</h4>
			<p class="text-muted mb-0"></p>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<p class="text-muted mb-0">Note</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<form action="" id="formresult">
				<div class="row">
					<div class="col-12">
						<p class="text-success">connect public network and click button update for update patch.</p>
						<p id="msg"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-12">
						<div class="float-left">
							<a href="#" class="btn btn-secondary">back</a>
						</div>
						<div class="float-right">
							<a href="#" class="btn btn-primary" id="btn-update">Update</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$(function(e){
		$('#btn-update').click(function(e){
			$('#btn-update').text('Loading...');
			$('#btn-update').attr('disabled','disabled');
			$('#msg').html('<img src="assets/loading.gif" height="30" />Loading...');
			$.ajax({
				url: 'index.php?route=config/update_source',
				type: 'POST',
				dataType: 'json'
			})
			.done(function(json) {
				$('#btn-update').removeAttr('disabled').html('Update');
				$('#msg').html("Update success");
				// alert('download success');
				console.log(json);
				console.log("success");
			})
			.fail(function(a,b,c) {
				console.log(a);
				console.log(b);
				console.log(c);
				$('#btn-update').removeAttr('disabled').html('Update');
				$('#msg').html("Update fail");
			})
			.always(function() {
				console.log("complete");
			});
		});
	});
</script>