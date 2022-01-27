# Michelin Barcode Project

START CONFIG
1. run cmd cmd/commandline_startup.txt สร้าง folder upload set permission 777

MOCKUP DATA
1. import group (index.php?route=import) 
2. import barcode (index.php?route=import) 
3. generate history จาก query ในไฟล์ cmd/generate_history.sql
4. generate default barcode กด update ทีละอัน (หลังจากข้อ1 แล้ว จะมีเลข group บางอันยังไม่ถูกแอด จำเป็นต้องมี default) (index.php?route=clear) 
5. load barcode range กด loading ตรง load barcode range (index.php?route=clear) 
6. ตรวจสอบ mb_master_config (load_freegroup=0, load_year=1, load_barcode=1, load_date=1)

DUMP DATA (MAC - XAMPP)
/Application/XAMPP/xamppfiles/bin/mysqldump
./mysqldump -u root -p dbname > /path/file.sql

Project Michelin Barcode
Git Branch main คือ สาขา แหลมกับพระประแดง
Git Branch prefix คือ สาขา ที่ใช้ตัวอักษรนำหน้า barcode



เซท permission folder upload
chmod -R 777 uploads/

แก้ไข config ใน required/config.php
ENVIRONMENT developer คือเครื่องเรา

ตัวใหม่ใช้ db ชื่อ fsoftpro_barcode_p3 
หรือจะแก้เป็นชื่อเดิม fsoftpro_barcode ก็ได้เช่นกัน

import db ที่ db/structure_default_data.sql เป็นไฟล์ตั้งต้น data

กรณี error เยอะ เช็ค php.ini ตรงกันไหม