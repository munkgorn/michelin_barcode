<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title"><?php echo $title;?></h4>
			<p class="text-muted mb-0">you can change your password on this page.</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<?php if (!empty($success)): ?>
			<div class="alert alert-success" role="alert"><?php echo $success; ?></div>
			<?php endif; ?>
			<?php if (!empty($error)): ?>
			<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
			<?php endif; ?>
			<form action="<?php echo $action; ?>" id="formresult" method="POST">
				<input type="hidden" id="id_user" name="id_user" value="<?php echo $id_user; ?>">
				<div class="row">
					<div class="col-3">
						<label class="mb-2">New Password</label>
						<div class="">
							<input type="password" class="form-control" name="password" value="" required>
						</div>
					</div>
					<div class="col-3">
						<label class="mb-2">Confim Password</label>
						<div class="">
							<input type="password" class="form-control" name="confirm-password" value="" required>
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
