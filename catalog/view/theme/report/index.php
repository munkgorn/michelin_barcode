<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Report remaining stock barcode</h4>
			<p class="text-muted mb-0">this list barcode received and you can use.</p>
		</div>
		<!--end card-header-->
		<div class="card-body bootstrap-select-1 py-0">
		</div>
	</div>
	<div class="card">
        <form action="<?php echo $action;?>" method="post">
		<div class="card-header">
            <div class="row">
                <div class="col-sm-6">
					<a href="<?php echo $export_excel; ?>" target="new" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                </div>
                <div class="col-sm-6 text-right">
                </div>
            </div>
		</div>
		<!--end card-header-->
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="makeEditable">
					<thead>
						<tr>
							<th class="text-center" width="30%">Group prefix</th>
							<th class="text-center" width="50%">Range barcode</th>
                            <th class="text-center" width="20%">Remaining QTY</th>
						</tr>
					</thead>
					<tbody>
                    <?php if (count($barcodes)>0) : ?>
                    <?php foreach ($barcodes as $barcode): ?>
                    <tr>
                        <td class="text-center"><?php echo $barcode['group'];?></td>
                        <td class="text-center"><?php echo $barcode['name'];?></td>
                        <td class="text-center"><?php echo number_format($barcode['count'],0);?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php else: ?>
                    <tr>
                    <td colspan="8" class="text-center">Not found barcode ready to use.</td>
                    </tr>
                    <?php endif; ?>
					</tbody>
				</table>
			</div>
			<!--end table-->
		</div>
		<!--end card-body-->
        </form>
	</div>
	<!--end card-->
</div>
<script>
$(document).ready(function(){
	$('#barcode').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
});
</script>