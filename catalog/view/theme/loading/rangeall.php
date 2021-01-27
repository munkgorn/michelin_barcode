<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-spinner fa-pulse"></i> <span id="headpercent"><?php echo round($percent,2);?>%</span> Loading range barcode... </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="progress mb-2" style="height:20px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="loadall" role="progressbar" aria-valuenow="<?php echo round($percent,2);?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo round($percent,2);?>%;height:20px;"><?php echo round($percent,2);?>%</div>
            </div>
            <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" id="barload" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="1" style="width: 0%"></div>
            </div>
            <textarea class="form-control mt-2" id="msg" cols="6" rows="20"></textarea>
        </div>
    </div>
</div>
<style>
#barload {
    -webkit-transition: width 10ms;
    -moz-transition: width 10ms;
    -o-transition: width 10ms;
    transition: width 10ms;
}
</style>
<script>
$(document).ready(function () {
   

    let showmsg = (message='', tab=false, status=true) => {
        const elemsg = $('#msg')
        if (status==false) {
            alert('Error : ' + message);
        }
        elemsg.append((status!=null?(status?'[Successful]':'[Error]'):'')+' '+message+'\n');
        if (tab==true) {
            elemsg.append('------------------------------\n');
        }
        elemsg.trigger('change');
    }

    let loading = (percent) => {
        const eleload = $('#barload')
        eleload.attr('aria-valuenow', percent).css('width', percent+'%');
    }

    
    let process_split = (list) => {
        console.log('process split');
        let indexofarray = 0;
        let result = {};
        // console.log(list);

        let olddate = null;
        
        $.each(list, (index,val) => {
            let group = parseInt(val.barcode_prefix);
            let barcodecode = parseInt(val.barcode_code);
            let valuedate = val.date_added.replace(/-/g, '').toString();
            let textgroup = group+'_'+valuedate;

            let prev = (typeof list[index-1] !== 'undefined') ? parseInt(list[index-1].barcode_code) : '';
            let more = (typeof list[index] !== 'undefined') ? parseInt(list[index].barcode_code) : '';
            let next = (typeof list[index+1] !== 'undefined') ? parseInt(list[index+1].barcode_code) : '';


            if (typeof result[group] === 'undefined') {
                result[group] = {};
                showmsg('Calcurate with group '+group, true);
                loading(5);
            }

            if (typeof result[group][valuedate] === 'undefined') {
                result[group][valuedate] = [];
                indexofarray = 0;
                result[group][valuedate][indexofarray] = [];
                // showmsg('Found new range '+temp, false);
            }

            if (typeof result[group][valuedate][indexofarray] === 'undefined'){
                result[group][valuedate][indexofarray] = [];
            }


            let obj = {thisbarcode:barcodecode, prevbarcode: prev, nextbarcode: next};

            if (barcodecode-1!=prev && barcodecode+1!=next) {
                if (result[group][valuedate][indexofarray].length > 0) {
                    indexofarray++;
                    result[group][valuedate][indexofarray] = [];
                }
                result[group][valuedate][indexofarray].push(barcodecode);
                
            }
            else if (barcodecode-1!=prev && barcodecode+1==next) {
                if (result[group][valuedate][indexofarray].length > 0) {
                    indexofarray++;
                    result[group][valuedate][indexofarray] = [];
                }
                result[group][valuedate][indexofarray].push(barcodecode);
            }

            else if (barcodecode-1==prev&&barcodecode+1!=next) {
                result[group][valuedate][indexofarray].push(barcodecode);
            }
            else if (barcodecode-1==prev&&barcodecode+1==next) {
                result[group][valuedate][indexofarray].push(barcodecode);
            }
            
        });

        $.each(result, function (i, v) { 
            showmsg('Process group '+i, true);
        });

        loading(20);
        showmsg('Process split done!', true);
        
        console.log(result);
        return result;
    }

    let process_pattern = (result, round) => {
        console.log('process pattern');
        let output = [];
        $.each(result, function (index, date) { // loop group
            $.each(date, function (idate, vgroup) { // loop date in group
                $.each(vgroup, function (i, v) { // loop date in group
                // console.log(v);
                    let start = v[0];
                    let end = v[v.length-1];
                    let qty = end - start + 1;
                    let newdate = idate.substr(0,4)+'-'+idate.substr(4,2)+'-'+idate.substr(6,2);
                    
                    if (typeof start !== 'undefined' && typeof end !== 'undefined') {
                        showmsg('GroupRange ('+(newdate)+') '+start+'-'+end+' = '+qty);      
                        output.push({
                            round: round,
                            group: index,
                            start: start,
                            end: end,
                            qty: qty,
                            date: newdate,
                            status: status
                        });
                    }
                });
            });  
        });

        loading(50);
        showmsg('Process pattern done!', true);
        console.log(output);
        return output;
    }

    let process_db = (output, status) => {
        console.log('Process DB');
        // console.log(output);

        const clearGroup = (output, status) => {
            let groupForClear = [];
            $.each(output, function (i, v) { 
                 if ($.inArray(v.group, groupForClear) === -1) {
                    groupForClear.push(v.group);
                 }
            });
            
            $.each(groupForClear, function (i, v) { 
                $.ajax({
                    type: "POST",
                    url: "index.php?route=barcode/ajaxClearRange",
                    data: {group: v, status:status},
                    dataType: "json",
                    async: false,
                    cache: false,
                    success: function (response) {
                        if (response) {
                            console.log('Success Clear barcode_range '+v);
                        } else {
                            console.log('Fail Clear barcode_range '+v);
                        }
                        
                    }
                });
            });
        }

        const findMaximum = () => {
            let result = null;
            $.ajax({
                type: "POST",
                url: "index.php?route=barcode/ajaxFindConditionMinimumRemove",
                dataType: "json",
                async: false,
                cache: true,
                success: function (response) {
                    result = response;
                }
            });
            return result;
        }

        const loopCondition = (output, maximum, status) => {
            let groupForFlagRemove = [];
            let groupForAdd = [];
            $.each(output, function (i, v) { 
                 if (parseInt(v.qty) >= parseInt(maximum)) {
                    groupForAdd.push(v);
                 } else {
                    for (let index = parseInt(v.start); index <= parseInt(v.end); index++) {
                        if ($.inArray(index, groupForFlagRemove) === -1) {
                            groupForFlagRemove.push(index);
                        }
                    }
                 }
            });

            if (groupForAdd.length>0) {
                $.ajax({
                    type: "POST",
                    url: "index.php?route=barcode/ajaxAddRange",
                    data: {data: groupForAdd},
                    dataType: "json",
                    async: false,
                    cache: false,
                    success: function (response) {
                        console.log(response);
                    }
                });
            }

            if (groupForFlagRemove.length>0) {
                $.ajax({
                    type: "POST",
                    url: "index.php?route=barcode/ajaxFlagRemoveBarcode",
                    data: {data: groupForFlagRemove},
                    dataType: "json",
                    async: false,
                    cache: false,
                    success: function (response) {
                        console.log(response);
                    }
                });
            }


            loading(100);
            process_redirect();            
        }

        clearGroup(output, status);
        let maxRemove = findMaximum();
        loopCondition(output, maxRemove, status);

        
        
    }

    let clearData = (group,status) => {
        var ajaxTime = new Date().getTime();
        $.ajax({
            type: "POST",
            url: "index.php?route=barcode/ajaxClearRange",
            data: {group: group, status: status},
            dataType: "json",
            async: false,
            cache: true,
            success: function (response) {
                var totalTime = new Date().getTime()-ajaxTime;
                // console.log(response);
                loading(80);
                if (response==true) {
                    showmsg('Clear group range in DB ['+totalTime+' sec.]', true);
                } else {
                    showmsg('Fail response Clear group range in DB ['+totalTime+' sec.]', true, false);
                }
                
            },
            error: (jqXHR, textStatus, errorThrown) => {
                showmsg('Fail delete something has wrong.', false, false);
            }
        });
    }

    let process_redirect = () => {
        let time = 1;
        showmsg('Waiting redirect page in '+time+' seconds', true);
        let timeid = setInterval(() => {
            showmsg('Countdown in '+time, false, null);
            if (time==0) {
                clearInterval(timeid);
                console.log('redirect');
                redirectAuto();
                
            }
            time--;
        }, 10);
    }

    let redirectAuto = () => {
        let urlgroup = parseInt("<?php echo isset($_GET['group']) ? $_GET['group'] : '';?>");
        let urlstatus = parseInt("<?php echo isset($_GET['status']) ? $_GET['status'] : '';?>");
        let urlredirect = "<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : '';?>";
        if (urlstatus==1) {
            urlstatus = 0;
        } else if (urlstatus==0) {
            urlgroup = urlgroup+1;
            urlstatus = 1;
        }
        
        let max = parseInt("<?php echo isset($_GET['max']) ? $_GET['max'] : '';?>");
        console.log((urlgroup-1)+' '+max+' '+urlstatus);
        if ((urlgroup-1)==max&&urlstatus==1&&urlredirect.length>0) {
            window.location.href = "index.php?route="+urlredirect;
        }
        
        if (max!=0&&(urlgroup<=max||urlstatus==0)) {
            let url = "index.php?route=loading/rangeall&round=1&status="+urlstatus+"&flag=0&group="+urlgroup+"&max="+max;
            // let url = "index.php?route=loading/rangeall&round=1&status="+urlstatus+"&flag=0&storeage=true";
            if (urlredirect.length>0) {
                url += '&redirect='+urlredirect
            }
            window.location.href=url;
        }
        
    }

	
	
//    let storeage = localStorage.getItem('savegroup');
//    let list = storeage.split(',');

//    console.log(typeof list);
	// console.log(retrievedUsername); 

    const round = <?php echo $round;?>;
    const list = <?php echo $list;?>;
    const status = <?php echo $status;?>;
    const nowgroup = <?php echo $group;?>;
    showmsg('Found data '+list.length+' rows', false);
    showmsg('Calcurate with group '+nowgroup+' status '+status, false);

   
    let result = process_split(list);
    setTimeout(() => {
        let output = process_pattern(result,round);
        setTimeout(() => {
            showmsg('Process someone change in DB', false);
            if (list.length > 0) {
                let query = process_db(output, status);
            } else {
                // showmsg('Not change someone in DB', true);
                if (list.length == 0) {
                    clearData(nowgroup, status);
                }
                loading(100);
                process_redirect();
            }
        }, 10);
    }, 10);

    $('#msg').on('change', function() {
        $('#msg').scrollTop($('#msg')[0].scrollHeight);
    });
    
       


    
});
</script>