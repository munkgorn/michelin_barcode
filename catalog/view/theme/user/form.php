<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Add barcode</h4>
			<p class="text-muted mb-0">Add barcode</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<?php if(get('result')=='success'){?>
				<div class="alert alert-success"><b>Success</b></div>
			<?php } ?>
			<form action="<?php echo $action; ?>" id="formresult" method="POST">
				<input type="hidden" id="id_user" name="id_user" value="<?php echo $id_user; ?>">
				<div class="row">
					<div class="col-3">
						<label class="mb-2">Username</label>
						<div class="">
							<input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-2">Password</label>
						<div class="">
							<input type="password" class="form-control" name="password" value="" autocomplete="off" autocomplete="new-password">
						</div>
					</div>
					<div class="col-3">
						<label class="mb-2">Group name</label>
						<div class="">
							<select name="id_user_group" id="" class="select2 form-control">
								<?php foreach($listUserGroup as $val){?>
								<?php if ($val['id_user_group']!=1) : ?>
								<option value="<?php echo $val['id_user_group']; ?>" 
									<?php echo ($user['id_user_group']==$val['id_user_group']?'selected':'') ?>>
									<?php echo $val['group_name']; ?>
								</option>
								<?php endif; ?>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-12">
						<div class="float-left">
							<a href="<?php echo route('user'); ?>" class="btn btn-default">back</a>
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