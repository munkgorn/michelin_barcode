<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">List Groups</h4>
			<p class="text-muted mb-0"></p>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<div class="row">
				<div class="col-12">
					<div class="input-group mb-3">
						<a href="<?php echo route('user/addGroup'); ?>" class="btn btn-primary">Add Group</a>
					</div>
				</div>
			</div>
			<?php if(!empty(get('result'))){?>
			<div class="row">
				<div class="col-12">
					<p class="text-success alert alert-success"><?php echo get('result'); ?></p>
				</div>
			</div>
			<?php } ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="makeEditable">
					<thead>
						<tr>
							<th style="width:50px;">No</th>
							<th style="">Group name</th>
							<th name="buttons" style="width:150px;">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=1;foreach($listUserGroup as $val){ ?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td><?php echo $val['group_name']; ?></td>
							<td name="buttons">
								<div class=" pull-right">
									<a class="btn btn-sm btn-soft-warning mr-2 btn-circle" href="<?php echo route('user/editGroup&id_user_group='.$val['id_user_group']); ?>">
										<i class="dripicons-pencil"></i>
									</a>
									<a class="btn btn-sm btn-soft-danger btn-circle" href="<?php echo route('user/deleteGroup&id_user_group='.$val['id_user_group']); ?>">
										<i class="dripicons-trash" aria-hidden="true"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<!-- <div>
				<nav aria-label="Page navigation example">
					<ul class="pagination">
						<li class="page-item">
							<a class="page-link" href="#" aria-label="Previous">
								<span aria-hidden="true">«</span> <span class="sr-only">Previous</span>
							</a>
						</li>
						<li class="page-item">
							<a class="page-link" href="#">1</a>
						</li>
						<li class="page-item">
							<a class="page-link" href="#">2</a>
						</li>
						<li class="page-item">
							<a class="page-link" href="#">3</a>
						</li>
						<li class="page-item">
							<a class="page-link" href="#" aria-label="Next">
								<span aria-hidden="true">»</span> <span class="sr-only">Next</span>
							</a>
						</li>
					</ul>
				</nav>
			</div> -->
			<!--end table-->
		</div>
		<!--end card-body-->
	</div>
	<!--end card-->
</div>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>