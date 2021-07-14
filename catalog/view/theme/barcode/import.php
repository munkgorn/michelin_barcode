<div class="page-wrapper">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Import Consumed Barcde</h4>
			<p class="text-muted mb-0">cut barcode</p>
		</div>


		<!--end card-header-->
		<div class="card-body bootstrap-select-1">
			<div class="row">
				<div class="col-sm-12">

					<?php echo !empty($success) ? '<div class="alert alert-success border-0" role="alert">'.$success.'</div>' : '';?>
					<?php echo !empty($error) ? '<div class="alert alert-danger border-0" role="alert">'.$error.'</div>' : '';?>
					<form action="#" method="post" enctype="multipart/form-data" id="form-import-group">
                        <div class="form-group row mb-0">
                            <label for="" class="col-sm-12 text-left">Import Fyt (.csv)</label>
                            <div class="col-sm-12">
                                <div class="input-group">
									<div class="custom-file">
										<input type="file" name="import_file_group" class="custom-file-input" id="import_file_group" aria-describedby="inputGroupFileAddon04" required accept=".csv" />
										<label class="custom-file-label" for="import_file_group">Browse Fyt File (.csv)</label>
									</div>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="btnupload">Import</button>
                                    </div>
                                </div>
                            </div>
							
							<div id="result_submit_form" class="col-sm-12">Message</div>
                        </div>
						<div class="progress mt-0">
                        <div class="progress-bar progress-bar-striped progress-bar-animated py-1" id="barload" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">Loading 0%</div>
                        </div>
                    </form>
				
				</div>
			</div>

		</div>
	</div>
	
</div>



<div class="modal" tabindex="-1" id="modal_show_range">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Range Group <span id="grouptitle"></span> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<input type="hidden" name="barcodegroup" value="" />
	   <input type="hidden" name="barcodeall" value="[]" />
	   <input type="hidden" name="barcodemax" value="[]" />
        <table class="table table-bordered">
			<thead>
				<tr>
					<th>Range Barcode</th>
					<th>QTY</th>
				</tr>
			</thead>
			<tbody id="modallist">
			</tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ignore</button>
        <button type="button" data-group="" class="btnrm rmall btn btn-primary" >Remove all</button>
		<button type="button" data-group="" class="btnrm rmmax btn btn-danger" >Remove qty < (<?php echo $maximum;?>)</button>
      </div>
    </div>
  </div>
</div>


<style>
#barload {
    -webkit-transition: width 100ms;
    -moz-transition: width 100ms;
    -o-transition: width 100ms;
    transition: width 100ms;
}
</style>
<script type="text/javascript">
$(document).ready(function () {
	$('[type="file"]').on('change', function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label.custom-file-label').html('<span class="text-dark">'+fileName+'</span>');
	});

	let loading = (percent) => {
        // console.log(percent+'%');
        const eleload = $('#barload')
        eleload.attr('aria-valuenow', percent.toFixed(2)).css('width', percent.toFixed(2)+'%').html('Loading '+percent.toFixed(2)+'%');
    }

	let pad = (str, max) => {
		str = str.toString();
		return str.length < max ? pad("0" + str, max) : str;
	}

	

	let uploadForm = (fd) => {
		let filename = '';
		var ajaxTime = new Date().getTime();
		console.log('========== CHECK FILE ==========');
		$.ajax({
			url: 'index.php?route=barcode/importCSV',
			type: 'POST',
			data: fd,
			mimeType: 'multipart/form-data', // this too
			contentType: false,
			cache: true,
			async: false,
			processData: false,
			dataType: 'json',
			success: function(data) {
				// console.log(data);

				var totalTime = new Date().getTime()-ajaxTime;
				loading(10);
				filename = data.file;
				if (filename.length<=0) {
					console.log('%c Fail cannot upload csv ', 'color: #dc3545'); // success 198754 | danger dc3545
					alert('Upload file fail. please try again');
					$('#result_submit_form').html('Upload file csv fail.');
				} else {
					console.log('%c Upload success ', 'color: #198754'); // success 198754 | danger dc3545
					$('#result_submit_form').html('Upload file csv successfull.');
				}
			},
			error: function (request, status, error) {
        alert(request.responseText);
			}
		});
		return filename;
	}

	let read_group = (pathfile) => {
		
		let result = {};
		let url = pathfile.split('/');
		let thisurl = '<?php echo MURL;?>uploads/import_cutbarcode/'+url[url.length-1];
		console.log('Read file on this path : '+thisurl);
		// console.log('uploads/import_cutbarcode/'+url[url.length-1]);
		$.ajax({	
			type: "POST",
			url: thisurl,
			// dataType: "json",
			cache: false,
			async: false,
			success: function (response) {
					
				result.barcode = [];

				if (response.length>0) {
					let rows = response.split(/\r\n|\n/);
					rows.forEach((value,index) => {
						if (index>0) {
							let cols = value.split(";");
							if (typeof cols[9] != 'undefined') {
								let str = cols[9].replace(/"/g,"");
								let thisbarcode = (str);
								// if (isNaN(thisbarcode)==false) {
									result.barcode.push(thisbarcode);
								// }
							}
						}
						
					});
					console.log('%c Found '+result.barcode.length+' barcode in file ', 'color: #198754'); // success 198754 | danger dc3545
				} else {
					console.log('%c Fail cannot read file ', 'color: #dc3545'); // success 198754 | danger dc3545
					alert('Fail read file.');
				}
				
			},
			error: function (request, status, error) {
        alert(request.responseText);
			}
		});
		// console.log(result);

		// localStorage.setItem('savegroup', null);
		return result;
	}

	let loop_group = (response) => {
		let realgroup = [];
		let realgroupid = [];
		let barcode = [];


		let percent = 10 / parseInt(response.barcode.length);
		let nowpercent = 10;
		response.barcode.forEach(value => {
			let thisbarcode = value;
			barcode.push(thisbarcode);

			let groupcode = thisbarcode.substr(0,3);
			if (jQuery.inArray(groupcode, realgroup) == -1) {
				// Check id this group
				setTimeout(() => {
					$.ajax({
						type: "POST",
						url: "index.php?route=barcode/ajaxGetGroupByGroupCode",
						data: {group: groupcode},
						dataType: "json",
						cache: false,
						async:false,
						success: function(response) {
							if (response){
								realgroupid.push(response);
								realgroup.push(groupcode);
							}
						}
					});
				},100);
				
			}
			nowpercent += percent;
			loading(nowpercent);
		});
		loading(20);
		// console.log('Found group in file');
		// console.log(realgroup);
		// console.log(realgroupid);
		$('#result_submit_form').html('Loop get group in file successfull');



		setTimeout(() => {
			console.log(realgroup);
			console.log(realgroupid);
			get_group(realgroup,realgroupid,barcode);
		},100);
	}

	let get_group = (filegroup,filegroupid,barcode) => {
		let dbgroup = [];
		$.ajax({
			type: "POST",
			url: "index.php?route=barcode/ajaxGetGroup",
			dataType: "json",
			cache:false,
			async:false,
			success: function (response) {
				console.log(response);
				$.each(response, (index,value) => {
					dbgroup.push(value.group_code);
				});
				loading(25);
				// console.log('Get group in db');
				console.log(dbgroup);
				$('#result_submit_form').html('Get group in db successfull.');
				setTimeout(() => {
					check_group(filegroup, filegroupid, dbgroup, barcode);
				},100);
			}
		});
	}
	
	let check_group = (filegroup, filegroupid, dbgroup, barcode) => {
		let realgroup = [];
		let realgroupid = [];
		console.log('========== CHECK ==========');
		console.log('CHECK FILE GROUP');
		console.log(filegroup);
		console.log('CHECK DB GROUP');
		console.log(dbgroup);
		// loop db condition with in file
		dbgroup.forEach((value,index) => {
			if (jQuery.inArray(value,filegroup)!==-1 && jQuery.inArray(value, dbgroup)!==-1) {
				realgroup.push(value);
				realgroupid.push(filegroupid[index]);
			}
		});
		realgroup.sort();
		// console.log('readlid',realgroupid);

		// localStorage.setItem('savegroup', realgroup);

		// loop barcode only in real group for ready to check db
		let barcode_forupdate = [];
		// console.log('barcode',barcode);
		barcode.forEach((value,index) => {
		// $.each(barcode, (index,value) => {
			let str = value;
			// console.log(str);
			let prefix = str.substr(0,3);
			console.log(prefix, realgroup, jQuery.inArray(prefix, realgroup));
			if (jQuery.inArray(prefix, realgroup)>=0) {
				barcode_forupdate.push(value);
			}
		});

		// console.log('barcode_forupdate', barcode_forupdate);

		loading(30);
		$('#result_submit_form').html('Compere dbgroup and filegroup successfull.');
		console.log('%c SUCCESS marge group ', 'color: #198754'); // success 198754 | danger dc3545
		// console.log(realgroup);
		// console.log(barcode_forupdate);
		// console.log(realgroup[0]);
		// console.log(realgroup[realgroup.length-1]);

		setTimeout(() => {
			sendToUpdate(barcode_forupdate, realgroupid[0], realgroupid[realgroupid.length-1]);
		},100);

	}

	let sendToUpdate = (barcodes, start, max) => {
		let percent = 70 / (parseInt(barcodes.length));
		let nowpercent = 30;
		loading(nowpercent);
		console.log('========== CHECK LOOP SEND BARCODE TO USED ==========');
		console.log(barcodes, start, max);
		$.each(barcodes, (index,value) => {
			setTimeout(() => {
				$.ajax({
					type: "POST",
					url: "index.php?route=barcode/ajaxUpdate",
					data: {barcode: value},
					dataType: "json",
					cache: false,
					async:false,
					success: function(response) {
						nowpercent += percent;
						if (response==false) {
							console.log('%c Fail update barcode '+value, 'color: #dc3545'); // success 198754 | danger dc3545
						}
						$('#result_submit_form').html('Query to db for update this barcode ('+value+') is used.');
						loading(nowpercent);
						if (nowpercent+1 >= 100) {
							loading(100);
							redirect_success(start,max);
							// redirect('loading/rangeall&round=1&status=1&flag=0&group='.$data['group'][0].'&max='.$data['group'][count($data['group'])-1].'&redirect=barcode/removeConditionRangeBarcode');
						}
					}
				});
			},100);
		});
	}

	let redirect_success = (start,max) => {
		console.log('Maybe send update success.');
		let time = 10;
		let timeid = false;
		timeid = setInterval(() => {
			time--;
			$('#result_submit_form').html('Query successfull, please wait redirect page in '+time+' sec.');
			if (time==0) {
				clearInterval(timeid);
				// let grouplocal = localStorage.getItem('savegroup', realgroup);
				window.location.href="index.php?route=loading/rangeall&round=1&status=1&flag=0&group="+start+"&max="+max+"&redirect=loading";
				// window.location.href="index.php?route=loading/rangeall&round=1&status=1&flag=0&storage=true&redirect=loading";
			}

		}, 1000);
	}


	// let filename = '';
	let realgroup = [];
	$( "#btnupload" ).click(function( event ) {
		$('#result_submit_form').html('');
		var fd = new FormData();
		var files = $('#import_file_group')[0].files[0];
		fd.append('import_file',files);

		// console.log(fd);

		let file = uploadForm(fd);
		setTimeout(() => {
			let result = read_group(file);
			setTimeout(() => {
				loop_group(result);
			},100);
		},100);
		// console.log(filename);
		
	});
});
</script>
<script>
$(document).ready(function () {
	function addCommas(nStr) {
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
});
</script>