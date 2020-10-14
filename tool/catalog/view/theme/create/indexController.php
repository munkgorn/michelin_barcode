<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card mt-3 border-0">
				<div class="card-body">
					<form action="#" id="formGenarateController">
						<div class="row">
							<div class="col-md-2">
								<label><b>Folder/Path</b></label> <a href="#" title="เลือก path folder ที่จะสร้าง Model">?</a>
							</div>
							<div class="col-md-2">
								<select name="folder" id="folder" class="form-control">
									<option value="">Default Root</option>
									<?php foreach($folder as $val){ ?>
									<option value="<?php echo $val; ?>"><?php echo $val; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-md-2">
								<label>Controller Name</label> <a href="#" title="ชื่อ Controller">?</a>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control" name="controller_name">
							</div>
							<div class="col-md-2">
								<label>Model Name</label> <a href="#" title="ชื่อ Model">?</a>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control" name="model_name">
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-3">
								<label><b>Create View</b></label><br>
								<input type="radio" id="chkView" name="chkView" checked value="NotView"> <label for="chkView">Not View</label><br>
								<input type="radio" id="chkView" name="chkView" value="View"> <label for="chkView">Create View Blank</label>
							</div>
							<div class="col-md-3">
								<label><b>Include script</b></label><br>
								<input type="checkbox" id="chkSelect2" name="chkSelect2" checked value="select2"> <label for="chkSelect2">Select2</label><br>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-12 text-right">
								<input type="submit" class="btn btn-primary">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).on('submit','#formGenarateController',function(e){
		var form = $(this);
		$.ajax({
			url: 'index.php?route=create/submitAddController',
			type: 'POST',
			// dataType: 'json',
			data: form.serialize(),
		})
		.done(function(html) {
			console.log(html);
			console.log("success");
		})
		.fail(function(a,b,c) {
			console.log(a);
			console.log(b);
			console.log(c);
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		e.preventDefault();
	});
</script>