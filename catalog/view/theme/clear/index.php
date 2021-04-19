<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Clear Data </h4>
			<p class="text-muted mb-0">version 2.0.0</p>
		</div>
		<div class="card-body bootstrap-select-1">
			<?php if (!empty($success)): ?>
			<div class="alert alert-success" role="alert"><?php echo $success; ?></div>
			<?php endif; ?>
			<?php if (!empty($error)): ?>
			<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
			<?php endif; ?>
            

                <div class="row">
                    <div class="col-sm-4">
                        <form action="<?php echo route('clear/removeBarcode');?>" method="POST">
                            <div class="form-group">
                                <label for="">Date of barcode</label>
                                <div>
                                    <input type="date" name="date" class="form-control" max="<?php echo date('Y-m-d', time());?>" value=""  />
                                    <button type="button" class="btnsubmit btn btn-danger mt-3">ยืนยันการลบ Barcode</button>
                                </div>
                                <small class="text-danger">เมื่อกดยืนยัน barcode ที่เก่ากว่าวันที่ที่เลือก (เฉพาะที่ใช้ไปแล้วหรือลบไปแล้ว) จะถูกลบทิ้งทั้งหมดไม่สามารถกู้คืนได้</small>
                                <small class="text-danger">ขั้นตอนการลบอาจจะใช้เวลานาน กรุณารอจนกว่าจะมีสถานะแจ้งเตือนขึ้นมาแสดง</small>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <form action="<?php echo route('clear/removeAssociation');?>" method="POST">
                            <div class="form-group">
                                <label for="">Date of association</label>
                                <div>
                                    <select name="association" id="" class="form-control">
                                        <?php if (count($dateass)>0) : ?>
                                        <option value="all">All</option>
                                        <?php endif;?>
                                    <?php foreach ($dateass as $date): ?>
                                        <option value="<?php echo $date['date_wk'];?>"><?php echo $date['date_wk'];?></option>
                                    <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btnsubmit btn btn-danger mt-3">ยืนยันการลบ Association</button>
                                </div>
                                <small class="text-danger">เมื่อกดยืนยัน ระบบจะลบ association ตามวันที่ที่เลือก ทั้งหมดและไม่สามารถกู้คืนได้</small>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <form action="<?php echo route('clear/removeFile');?>" method="POST">
                            <div class="form-group">
                                <label for="">Clear file import&export</label>
                                <!-- <select name="" id="" class="form-control"></select> -->
                                <div>
                                <button type="button" class="btnsubmit btn btn-danger">ยืนยันการลบไฟล์</button>
                                </div>
                                <small class="text-danger">ระบบมีการเก็บไฟล์ล่าสุดลงในเครื่อง server หากต้องการให้พื้นที่คงเหลือ สามารถลบไฟล์ได้ หากมีการ Export และ Import ใหม่ระบบก็จะทำงานได้ปกติ (ไม่มีผลกระทบต่อระบบ)</small>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <form action="<?php echo route('loading/rangeall');?>">
                            <input type="hidden" name="route" value="loading/rangeall" />
                            <input type="hidden" name="round" value="1" />
                            <input type="hidden" name="status" value="1" />
                            <input type="hidden" name="flag" value="0" />
                            <input type="hidden" name="redirect" value="clear" />
                            <div class="form-group">
                                <label for="">Load Range Barcode</label>
                                <div>
                                    <input type="number" name="group" class="form-control" placeholder="Start Group" value="<?php echo $group_start;?>" />
                                    <input type="number" name="max" class="form-control" placeholder="End Group" value="<?php echo $group_end;?>" />
                                </div>
                                <button typye="submit" class="btn btn-danger mt-2">Loading</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <form action="index.php?route=clear/updateConfig" method="post">
                            <?php foreach ($configm as $kt=> $t) : ?>
                            <div class="form-check">
                                <input class="form-check-input" name="listconfig[]" type="checkbox" value="<?php echo $t;?>" id="config<?php echo $kt;?>" checked>
                                <label class="form-check-label" for="config<?php echo $kt;?>"><?php echo $t;?></label>
                            </div>
                            <?php endforeach;?>
                            <input type="text" class="form-control" name="numvalue" value="1" />
                            <input type="submit" class="btn btn-danger mt-2" value="update" />
                        </form>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Desc.</th>
                                    <th>Date</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Update Default Group</td>
                                    <td>วิธีการคำนวณหน้า Purchase ผิดพลาดเนื่องจาก default_start เปลี่ยนไป แก้ให้เป็น default คือ 00000 - 99999</td>
                                    <td></td>
                                    <td>
                                        <a href="<?php echo route('group/updateDefaultStart');?>" onclick="return confirm('Confirm')" class="btn btn-sm btn-danger" target="new">Update</a><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Association Clean Saved</td>
                                    <td>ล้างค่าเก่าที่ Save ไว้ใน Association พวก RemainingQty, Propose, ProposeRemaining</td>
                                    <td>02/04/2021</td>
                                    <td>
                                        <a href="<?php echo route('api/updateProduct');?>" onclick="return confirm('Confirm')" class="btn btn-sm btn-danger" target="new">Update</a><br />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- <a href="<?php echo route('clear/updateTableBarcodeWithGroupCode');?>" onclick="return confirm('Confirm')" target="new">Update Table barcode</a><br /> -->
                    </div>
                    <div class="col-sm-12"><hr />
                        <!-- <a href="#" onclick="alert('เนื่องจาก ระบบ patch เป็นการเชื่อมต่ออินเตอร์เน็ตภายนอก และนำไฟล์เข้ามาใน server โดยไม่ได้ผ่านการ scan virus และติดปัญหา firewall จึงไม่ผ่าน Policy ของทาง michelin และไม่สามารถอัพเดทได้');" class="btn btn-primary">Patch</a> -->
                        <a href="<?php echo route('config/message');?>" class="btn btn-secondary">How to patch</a>
                    </div>
                </div>
			
		</div>
	</div>
</div>


<script>
$(document).ready(function(){
    $('.btnsubmit').click(function(event){
        const boolconfirm = confirmPrompt();
        if (boolconfirm===true) {
            console.log($(this).parents('form').attr('src'));
            $(this).html('กำลังดำเนินการ').attr('disabled','disabled').parents('form').submit();
        } else {
            event.stopPropagation();
        }
    });
    function confirmPrompt() {
        const value = prompt('ยืนยันการลบด้วยการพิมพ์ว่า `confirm`');
        if (value==='confirm') {
            return true;
        } else {
            return false;
        }
    }
    // $('button').on('click', function() {
    //     $(this).attr('disabled','disabled').parents('form').submit();
    // });
	$('#clear').addClass('mm-active').children('ul.mm-collapse').addClass('mm-show');
	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
		console.log(fileName);
	});
});
</script>