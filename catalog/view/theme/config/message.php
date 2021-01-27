<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">How to Patch Update Software</h4>
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
						<p>วิธีอัพเดทแพทโปรแกรม</p>
            <ul>
              <li>Login ระบบ ที่ปลดล็อค firewall เพื่อโหลดข้อมูลจากอินเตอร์เน็ตภายนอกได้และสามารถเชื่อมต่อกับ Server ได้โดยตรง</li>
              <li>โหลดไฟล์ล่าสุดจากเว็บผู้พัฒนา <a href="http://fsoftpro.com/production/michelin_barcode/update_source/Archive.zip" class="btn btn-success btn-sm" target="new"><u>http://fsoftpro.com/production/michelin_barcode/update_source/Archive.zip</u></a></li>
              <li>เมื่อได้ไฟล์ Archive.zip มาแล้วให้ทำการ แตกไฟล์ และ scan virus ตาม Policy ของ michelin</li>
              <li>เปิดโปรแกรม FileZilla (หากไม่มีให้ติดตั้ง)</li>
              <li>เข้า Server (เข้าด้วย ip/username/password) path "/var/www/html/"</li>
              <li>ไฟล์ที่แตกจาก .zip ให้โยนเข้าไปใน /var/www/html/ และกดยืนยันการอัพโหลดทับ</li>
              <li>* หากมีการอัพเดทนอกเหนือจากเปลี่ยนแปลงไฟล์ เช่นการเปลี่ยนแปลง โครงสร้าง DB จะแจ้งทางข้อความให้ทราบ</li>
            </ul>
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
							<a href="index.php?route=clear" class="btn btn-secondary">back</a>
						</div>
						<div class="float-right">
							<!-- <a href="#" class="btn btn-primary" id="btn-update">Update</a> -->
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>