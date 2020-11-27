<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Setting</h4>
			<p class="text-muted mb-0">Default</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<?php if (!empty($success)): ?>
			<div class="alert alert-success" role="alert"><?php echo $success; ?></div>
			<?php endif; ?>
			<?php if (!empty($error)): ?>
			<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
			<?php endif; ?>
			<ul class="nav nav-pills">
				<li class="nav-item">
					<a class="nav-link <?php echo $tab=='config_default'?'active':'';?> " data-toggle="tab" href="#config_default" role="tab" aria-controls="config_default" aria-selected="true">Default</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php echo $tab=='config_relationship'?'active':'';?> " data-toggle="tab" href="#config_relationship" role="tab" aria-controls="config_relationship" aria-selected="false">Relationship</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php echo $tab=='config_barcode'?'active':'';?> " data-toggle="tab" href="#config_barcode" role="tab" aria-controls="config_barcode" aria-selected="false">Barcode</a>
				</li>
				<!-- <li class="nav-item">
					<a class="nav-link <?php echo $tab=='config_status'?'active':'';?> " data-toggle="tab" href="#config_status" role="tab" aria-controls="config_status" aria-selected="false">Status</a>
				</li> -->
			</ul>
			<div class="tab-content pt-5" id="myTabContent">
				<div class="tab-pane fade <?php echo $tab=='config_default' ? 'show active' : '';?>" id="config_default" role="tabpanel" aria-labelledby="config_default">
					<!-- Default -->
					<form method="post" action="<?php echo $action_default; ?>">
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-4 col-form-label text-left">Nb of days that barcode prefix cannot be used with the new size association<br>จำนวนวันที่ไม่อนุญาตให้ใช้กลุ่มบาร์โค้ดเดิมกับไซส์ใหม่</label>
							<div class="col-sm-9 col-md-8">
								<input type="number" name="config_date_size" class="form-control" min="0" value="<?php echo $config_date_size; ?>" required/>
								<small>เช่น ถ้า 105xxxxx เคยใช้กับไซส์ A ไปแล้ว ต้องรออีก X วันถึงจะกลับมาใช้กับไซส์ B ได้</small>
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-4 col-form-label text-left">Nb of days that barcode cannot be repeated<br>จำนวนวันที่ไม่อนุญาตให้ใช้เลขบาร์โค้ดซ้ำ</label>
							<div class="col-sm-9 col-md-8">
								<input type="number" name="config_date_year" class="form-control" min="0" value="<?php echo $config_date_year; ?>" required/>
								<small>เช่น ห้ามซ้ำกัน 3 ปี ให้คีย์ 365x3 = 1095 วัน (1 ปี เท่ากับ 365 วัน)</small>
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-4 col-form-label text-left">Nb of skipped barcodes that will be automatically changed status to "consumed"<br>หากเลขบาร์โค้ดกระโดข้ามช่วงไปน้อยกว่าจำนวนที่ระบุนี้ โปรแกรมจะทำการตัดสต๊อกให้อัตโนมัติ</label>
							<div class="col-sm-9 col-md-8">
								<input type="number" name="config_maximum_alert" class="form-control" min="0" value="<?php echo $config_maximum_alert; ?>" required/>
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-4 col-form-label text-left">วันที่กำหนด ระยะเวลาค้นหา lastweek ล่าสุดของ association</label>
							<div class="col-sm-9 col-md-8">
								<input type="number" name="config_lastweek" class="form-control" min="0" value="<?php echo $config_lastweek; ?>" required/>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-12">
								<hr />
								<button type="submit" class="btn btn-primary float-right">Save</button>
							</div>
						</div>
					</form>
				</div>
				<div class="tab-pane fade <?php echo $tab=='config_barcode' ? 'show active' : '';?>" id="config_barcode" role="tabpanel" aria-labelledby="config_barcode">
					<!-- Barcode -->
					<form action="<?php echo $action_barcode; ?>" method="post" enctype="multipart/form-data">
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-2 col-form-label text-left">Import Excel Config</label>
							<div class="col-sm-9 col-md-10">
								<div class="input-group">
									<div class="custom-file">
										<input type="file" name="import_file" class="custom-file-input" id="inputImportConfigFlexibleGroup" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
										<label class="custom-file-label" for="inputImportConfigFlexibleGroup">Browse Excel File (.xlsx)</label>
									</div>
									<div class="input-group-append">
										<button class="btn btn-outline-primary" type="submit" id=""><i class="fas fa-file-excel"></i> Import</button>
									</div>
								</div>
								<small>อัพโหลดไฟล์ สำหรับกำหนดเงื่อนไขการใช้ตัวเลข barcode ในสาขานี้</small>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-12 ">
							<a href="<?php echo route('export/setting_barcode');?>" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export</a>
						</div>
					</div>
					<hr />
					<p class="mb-0">อัพโหลดครั้งล่าสุดเมื่อ <?php echo date('d/m/Y H:i', strtotime($lastupdate_barcode)); ?></p>
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Group</th>
								<!-- <th>Code</th> -->
								<th>Start</th>
								<th>End</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($barcodes) > 0): ?>
							<?php foreach ($barcodes as $barcode): ?>
							<tr>
								<td><?php echo sprintf('%03d',$barcode['group']); ?></td>
								<td><?php echo sprintf('%08d',$barcode['start']);?></td>
								<td><?php echo sprintf('%08d',$barcode['end']); ?></td>
								<td><?php echo $barcode['total']; ?></td>
							</tr>
							<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>

				</div>
				<div class="tab-pane fade <?php echo $tab=='config_relationship' ? 'show active' : '';?>" id="config_relationship" role="tabpanel" aria-labelledby="config_relationship">
					<!-- Relationship-->
					<form action="<?php echo $action_importrelationship; ?>" method="post" enctype="multipart/form-data">
						<div class="form-group row">
							<label for="" class="col-sm-3 col-md-2 col-form-label text-left">Import Excel Config</label>
							<div class="col-sm-9 col-md-10">
								<div class="input-group">
									<div class="custom-file">
										<input type="file" name="import_file" class="custom-file-input" id="inputImportConfigRelationship" aria-describedby="inputGroupFileAddon04" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  >
										<label class="custom-file-label" for="inputImportConfigRelationship">Browse Excel File (.xlsx)</label>
									</div>
									<div class="input-group-append">
										<button class="btn btn-outline-primary" type="submit" id="">Import</button>
									</div>
								</div>
								<small>อัพโหลดไฟล์ สำหรับกำหนดเงื่อนไขการใช้ตัวเลข barcode ในสาขานี้</small>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-12 ">
							<a href="<?php echo route('export/setting_relation');?>" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export</a>
						</div>
					</div>
					<hr /> 
					<p class="mb-0">อัพโหลดครั้งล่าสุดเมื่อ <?php echo date('d/m/Y H:i', strtotime($lastupdate_relationship)); ?></p>
					<form action="<?php echo $action_relationship;?>" id="formresult" method="post">
						<div class="row">
							<div class="col-12">
								<table class="table table-bordered">
									<thead >
										<tr>
											<th width="30%">Prefix Group</th>
											<th width="30%">Size</th>
											<th>Remark</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($relationships as $val) { ?>
										<tr>
											<td><?php echo sprintf('%03d', $val['group']);?></td>
											<td><?php echo $val['size'];?></td>
											<td><?php echo $val['comment'];?></td>
											<!-- <td><input type="text" class="form-control" name="group" value="<?php echo sprintf('%03d',$val['group']); ?>"></td> -->
											<!-- <td><input type="text" class="form-control" name="size" value="<?php echo $val['size']; ?>"></td> -->
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>

						<!-- <div class="row mt-4">
							<div class="col-12">
								<div class="float-left">
									<a href="<?php echo route('barcode'); ?>" class="btn btn-default">back</a>
								</div>
								<div class="float-right">
									<input type="submit" value="Save" class="btn btn-primary">
								</div>
							</div>
						</div> -->
					</form>

				</div>
				<div class="tab-pane fade <?php echo $tab=='config_status' ? 'show active' : '';?>" id="config_status" role="tabpanel" aria-labelledby="config_status">
					<!-- Value -->
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Status</th>
								<th width="20%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($status)): ?>
							<?php foreach ($status as $key => $value): ?>
							<tr>
								<td><?php echo $value['status']; ?></td>
								<td><a href="<?php echo route('setting/delStatus') . '&id=' . $value['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Remove</a></td>
							</tr>
							<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right">
									<a data-toggle="modal" data-target="#ModalStatus" class="btn btn-success btn-sm">Add Status</a>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="ModalStatus" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="ModalStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalStatusLabel">Add Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form action="<?php echo $action_addstatus;?>" method="post">
				<div class="form-group">
					<label for="">Status</label>
					<input type="text" class="form-control" name="status">
				</div>
				<button type="submit" class="btn btn-primary">Save</button>
			</form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
	$('#config').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});
});
</script>