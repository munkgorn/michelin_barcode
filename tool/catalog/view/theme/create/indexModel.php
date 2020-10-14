<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card mt-3 border-0">
				<div class="card-body">
					<form action="#" method="POST" id="formGenarateModel">
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
								<label><b>Database</b></label> <a href="#" title="เลือก table database ที่จะสร้าง Model">?</a>
							</div>
							<div class="col-md-2">
								<select name="database_name" id="database_name" class="form-control">
									<option value="">Select</option>
									<?php 
										foreach($table as $val){ 
											$db_name = str_replace(PREFIX,'',$val['Tables_in_'.DB_DB]);
									?>
									<option value="<?php echo $db_name; ?>"><?php echo $db_name; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-md-2">
								<label><b>Model Name</b></label>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control" id="model_name" name="model_name">
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-2">
								<label><b>Basic Query</b></label><br>
								<input type="checkbox" id="chkAdd" name="chkAdd" checked> <label for="chkAdd">Add</label><br>
								<input type="checkbox" id="chkEdit" name="chkEdit" checked> <label for="chkEdit">Edit</label><br>
								<input type="checkbox" id="chkDelete" name="chkDelete" checked> <label for="chkDelete">Delete</label>
							</div>
							<div class="col-md-2">
								<label><b>Function</b></label><br>
								<input type="checkbox" id="getList" name="getList" checked> <label for="getList">getList(Select)</label><br>
								<input type="checkbox" id="getLists" name="getLists" checked> <label for="getLists">getLists</label>
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
	$(document).on('submit','#formGenarateModel',function(e){
		var form = $(this);
		$.ajax({
			url: 'index.php?route=create/submitAddModel',
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
	$('#database_name').on('change',function(e){
		var ele = $(this);
		$.ajax({
			url: 'index.php?route=create/genarateModelName',
			type: 'GET',
			dataType: 'json',
			data: {database_name: ele.val()},
		})
		.done(function(json) {
			$('#model_name').val(json);
			console.log("success");
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		
	});
</script>