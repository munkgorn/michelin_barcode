<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-spinner fa-pulse"></i> Loading range barcode... </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" id="barload" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
            <textarea class="form-control mt-2" id="msg" cols="6" rows="20"></textarea>
        </div>
    </div>
</div>
<style>
#barload {
    -webkit-transition: width 300ms;
    -moz-transition: width 300ms;
    -o-transition: width 300ms;
    transition: width 300ms;
}
</style>
<script type="text/javascript">
$(document).ready(function () {
   

    let showmsg = (message='', tab=false, status=true) => {
        const elemsg = $('#msg')
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
        let result = new Object();
        $.each(list, (index,val) => {
            let group = parseInt(val.barcode_prefix);
            let barcode = parseInt(val.barcode_code);
            let prev = (typeof list[index-1] !== 'undefined') ? parseInt(list[index-1].barcode_code) : '';
            let next = (typeof list[index+1] !== 'undefined') ? parseInt(list[index+1].barcode_code) : '';

            if (typeof result[group] === 'undefined') {
                result[group] = [];
                showmsg('Calcurate with group '+group, true);
                loading(10);
            }
            if (typeof result[group][indexofarray] === 'undefined') {
                result[group][indexofarray] = [];
                let temp = indexofarray+1;
                showmsg('Found new range '+temp, false);
            }

            if ( barcode+1 == next) {
                result[group][indexofarray].push(barcode);
            } else {
                result[group][indexofarray].push(barcode);
                indexofarray++;
            }
            
        });

        loading(20);
        showmsg('Process split done!', true);
        return result;
    }

    let process_pattern = (result, round) => {
        console.log('process pattern');
        let output = [];
        $.each(result, (index,val) => {
            $.each(val, (i,v) => {
                let start = v[0];
                let end = v[v.length-1];
                let qty = end - start + 1;
                showmsg('GroupRange ('+(parseInt(i)+1)+') '+start+'-'+end+' = '+qty);      
                output.push({
                    round: round,
                    group: index,
                    start: start,
                    end: end,
                    qty: qty
                });
            });  
        });

        loading(50);
        showmsg('Process pattern done!', true);
        return output;
    }

    let process_db = (output, status) => {

        
        let ajaxDel = (arrayID) => {
            console.log('process del');
            var ajaxTime = new Date().getTime();
            $.ajax({
                type: "POST",
                url: "index.php?route=barcode/ajaxDelRange",
                data: {data: arrayID, status: status},
                dataType: "json",
                async: false,
                cache: true,
                success: function (response) {
                    var totalTime = new Date().getTime()-ajaxTime;
                    // console.log(response);
                    loading(80);
                    if (response==true) {
                        showmsg('Delete group range in DB ['+totalTime+' sec.]', false);
                    } else {
                        showmsg('Fail response Delete group range in DB ['+totalTime+' sec.]', false, false);
                    }
                    
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    showmsg('Fail delete something has wrong.', false, false);
                }
            });
        }

        let findGroup = (output) => {
            console.log('process find');
            showmsg('Start find group range in DB', false);
            $.each(output, (i,v) => {
                let item = parseInt(v.group);
                if ($.inArray(item, clean_repeat_group) === -1) {
                    clean_repeat_group.push(item);
                }
            });
            $.each(clean_repeat_group, (i,v) => {

                let ajaxFind = (v) => {
                    var ajaxTime = new Date().getTime();
                    $.ajax({
                        type: "POST",
                        url: "index.php?route=barcode/ajaxFindRange",
                        data: {group: v, status: status},
                        dataType: 'JSON',
                        async: false,
                        cache: true,
                        success: (response) => {
                            var totalTime = new Date().getTime()-ajaxTime;
                            if (response.length == 0) {
                                showmsg('Not found group range in DB ['+totalTime+' sec.]', false);
                                loading(80);
                            } else if (response.length > 0) {
                                showmsg('Found group range in DB ['+totalTime+' sec.]', false);
                                loading(70);
                                ajaxDel(response);
                            } else {
                                showmsg('Fail response find group range in DB ['+totalTime+' sec.]', false, false);
                            }
                        },
                        error: (jqXHR, textStatus, errorThrown) => {
                            showmsg('Fail find something has wrong.', false, false);
                        }
                    });
                };
                ajaxFind(v);
                
            });
        }

        let ajaxAdd = (output, status) => {
            console.log('process add');
            let datainput = [];
            $.each(output, (i,v) => {
                v.status = status;
                datainput.push(v);
            });
            // console.log(datainput);
            var ajaxTime = new Date().getTime();
            $.ajax({
                type: "POST",
                url: "index.php?route=barcode/ajaxAddRange",
                data: {data: datainput},
                dataType: "json",
                async: false,
                cache: true,
                success: function (response) {
                    var totalTime = new Date().getTime()-ajaxTime;
                    if (response==true) {
                        showmsg('Add group range in DB ['+totalTime+' sec.]', true);
                    } else {
                        showmsg('Fail response Add group range in DB ['+totalTime+' sec.]', true, false);
                    }
                    loading(100);
                    process_redirect();
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    showmsg('Fail add something has wrong.', true, false);
                }
            });
        }

        let clean_repeat_group = [];
        findGroup(output);
        ajaxAdd(output, status);
        
    }

    let process_redirect = () => {
        let time = 5;
        showmsg('Waiting redirect page in '+time+' seconds', true);
        let timeid = setInterval(() => {
            showmsg('Countdown in '+time, false, null);
            if (time==0) {
                clearInterval(timeid);
                console.log('redirect');
            }
            time--;
        }, 1000);
    }

   

    const round = <?php echo $round;?>;
    const list = <?php echo $list;?>;
    const status = <?php echo $status;?>;
    showmsg('Found data '+list.length+' rows', false);
    showmsg('Calcurate with status '+status, false);
   
    let result = process_split(list);
    setTimeout(() => {
        let output = process_pattern(result,round);
        setTimeout(() => {
            showmsg('Process someone change in DB', false);
            if (list.length > 0) {
                let query = process_db(output, status);
            } else {
                showmsg('Not change someone in DB', true);
                process_redirect();
            }
        }, 1000);
    }, 1000);

    $('#msg').on('change', function() {
        $('#msg').scrollTop($('#msg')[0].scrollHeight);
    });
    
       


    
});
</script>