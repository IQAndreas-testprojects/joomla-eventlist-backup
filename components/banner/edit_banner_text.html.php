<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;

$phpDate = mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'));

//"import" setting from JRequest values
$text_id 		= JRequest::getInt('text_id');
$lunch_text		= JRequest::getString('lunch_text', "");
$altlunch_text	= JRequest::getString('altlunch_text', "");

?>

<b><?php echo ENTER_DAILY_TEXT_FOR . " " . strftime(DATEFORMAT_ENTER_DAILY_TEXT, $phpDate); ?></b>

<!-- EDIT BANNER TEXT -->
<form action="<?php echo $bannerEditURL; ?>" method="post">

	<span class="fel">
		<?php echo BannerActions::getErrors('lunch_text'); ?>
		<?php echo BannerActions::getErrors('altlunch_text'); ?>
	</span>
	<br />
	<?php echo LABEL_LUNCH_TEXT; ?><br /><textarea name="lunch_text" cols="66" rows="3" wrap="soft" maxlength="255"><?php echo htmlspecialchars($lunch_text); ?></textarea><br/>
	<?php echo LABEL_ALTLUNCH_TEXT; ?><br /><textarea name="altlunch_text" COLS=66 ROWS=3 wrap="soft" maxlength="1000"><?php echo htmlspecialchars($altlunch_text); ?></textarea>
	
	<input type="hidden" name="text_id" value="<?php echo $text_id; ?>" />
    <input type="hidden" name="id" value="<?php echo $banner_id; ?>" />
    <input type="hidden" name="action" value="text" />

	<input type="hidden" name="d" value="<?php echo JRequest::getInt('d'); ?>" />
	<input type="hidden" name="m" value="<?php echo JRequest::getInt('m'); ?>" />
	<input type="hidden" name="y" value="<?php echo JRequest::getInt('y'); ?>" />
    
    <input type="submit" name="save" value="<?php echo SAVE_BANNER_TEXT; ?>" />
    <input type="submit" name="preview" value="<?php echo PREVIEW_BANNER_TEXT; ?>" />
</form>
<p>&nbsp;</p>
<hr />
<p>&nbsp;</p>
