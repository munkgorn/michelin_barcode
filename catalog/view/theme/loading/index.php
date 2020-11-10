<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-spinner fa-pulse"></i> Loading file... </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>List file</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loading as $key => $load) : ?>
                    <tr>
                        <td>
                        <?php echo $load['name'];?>
                        <div class="progress load<?php echo $key;?>">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {

    <?php foreach ($loading as $key => $load) : ?>
    $.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 100;
                    // Do something with download progress
                    console.log(percentComplete);
                    $('.progress.load<?php echo $key;?> > .progress-bar').attr('aria-valuenow', percentComplete).css('width', percentComplete+'%');
                }
            }, false);
            return xhr;
        },
        type: 'POST',
        url: "<?php echo $load['url'];?>",
    });
    <?php endforeach; ?>

    var tid = setTimeout(checkCompleted, 1000);
    function checkCompleted() {
        let arr = new Array();
        $('.progress').each(function(){
            let bar = $(this).children('.progress-bar').attr('aria-valuenow');
            console.log(bar);
            if (bar==100) {
                arr.push(true);
            }
        });
        if ($.inArray(false, arr)===-1) {
            console.log("redirect");
            clearTimeout(tid);
            setTimeout(function () {
                window.location.href = "index.php?route=<?php echo $redirect;?>";
            }, 2000); //
            
        } else {
            tid = setTimeout(checkCompleted, 1000);
        }
    }
});
</script>