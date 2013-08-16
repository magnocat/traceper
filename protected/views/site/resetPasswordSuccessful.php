
<script type="text/javascript">
TRACKER.showMessageDialog('<?php echo Yii::t('site', 'Your password has been changed successfully, you can login now...') ?>');
$("#passwordResetBlock").hide();
$('#registerBlock').css('height', '85%');
$('#registerBlock').css('min-height', '420px');
$("#registerBlock").load();
$("#registerBlock").show();
$("#appLinkBlock").load();
$("#appLinkBlock").show();
</script>
