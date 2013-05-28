
<script type="text/javascript">
TRACKER.showMessageDialog('<?php echo Yii::t('site', 'Your password has been changed successfully, you can login now...') ?>');
$("#passwordResetBlock").hide();
$("#registerBlock").load();
$("#registerBlock").show();
</script>
