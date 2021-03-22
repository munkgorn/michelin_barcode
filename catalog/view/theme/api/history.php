<div class="container">
  <div class="row">
    <div class="col-sm-12">
    <h1><?php echo $text;?></h1>
<div class="progress mb-2" style="height:20px;width:100%;display:block;">
  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="loadall" role="progressbar" aria-valuenow="<?php echo round($percent,2);?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo round($percent,2);?>%;height:20px;"><?php echo round($percent,2);?>%</div>
  </div>
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
  let time = 10;
  let timeid = setInterval(() => {
      if (time==0) {
          clearInterval(timeid);
          console.log('redirect');
          redirectAuto();
      }
      time--;
  }, 10);

  let redirectAuto = () => {
    <?php if ($result==1) : ?>
    window.location.href="index.php?route=api/runDataGroupHistory&dbname=<?php echo $dbname;?>&key=<?php echo $key;?>";
    <?php else: ?>
    alert('cannot insert this group, please refresh again');
    <?php endif;?>
  }
});
</script>