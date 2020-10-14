<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card mt-3 border-0">
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-md-12">
							<h4>ลูกค้า</h4>
						</div>
					</div>
					<!-- <div class="row mb-3">
						<div class="col-md-4">
							<input type="text" class="form-control" placeholder="customer id">
						</div>
						<div class="col-md-4">
							<input type="text" class="form-control" placeholder="name">
						</div>
						<div class="col-md-4">
							<button class="btn btn-primary">Search</button>
						</div>
					</div> -->
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>ลำดับ.</th>
										<th>ชื่อลูกค้า</th>
										<th>ประเภทลูกค้า</th>
										<th>เบอร์ลูกค้า</th>
										<th>อีเมล</th>
										<th>วันที่สมัคร</th>
										<th class="text-center" width="10%"></th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1;
										foreach($list_customer as $val){
									 ?>
									<tr>
										<td><?php echo $i++; ?>.</td> 
										<td><?php echo $val['customer_name']; ?></td>
										<td><?php echo $val['customer_type']; ?></td>
										<td><?php echo $val['customer_phone']; ?></td>
										<td><?php echo $val['customer_email']; ?></td>
										<td><?php echo $val['date_added']; ?></td>
										<td class="text-center">
											<?php /*<a href="<?php echo route('customer/edit'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
											<button  class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>*/ ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#customer').addClass('active');
	});
</script>