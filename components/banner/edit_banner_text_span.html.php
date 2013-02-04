<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;

//Check if you are ADDING or EDITING a banner
$adding = (JRequest::getCmd('action') == "add_text") ? true : false;

//$phpDate = mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'));
$start_date = 	JRequest::getString('banner_start_date');
$end_date =		JRequest::getString('banner_end_date');

//"import" setting from JRequest values
$text_id 	= JRequest::getInt('text_id'); //Blank if ADDING
$main_text	= JRequest::getString('main_text', "");
$sub_text	= JRequest::getString('sub_text', "");

?>

<b><?php echo EDIT_BANNER_TEXT; ?></b>

<!-- EDIT BANNER TEXT -->
<form action="<?php echo $bannerEditURL; ?>" method="post">

	<span class="fel">
		<?php echo BannerActions::getErrors('banner_start_date'); ?>
		<?php echo BannerActions::getErrors('banner_end_date'); ?>
		<?php echo BannerActions::getErrors('main_text'); ?>
		<?php echo BannerActions::getErrors('sub_text'); ?>
	</span>
	
	<br />
	<?php echo DATE_FORMAT_TEXT;?> <?php //echo strftime(DATEFORMAT_INPUT, mktime(0, 0, 0, 12, 31)); ?> <br />
	<?php echo LABEL_START_DATE;?> <input type="text" name="banner_start_date" maxlength="12" value="<?php echo htmlspecialchars($start_date);?>" /><br/>
	<?php echo LABEL_END_DATE;?>   <input type="text" name="banner_end_date"   maxlength="12" value="<?php echo htmlspecialchars($end_date);?>"   /><br/>
	
	<?php echo LABEL_MAIN_TEXT; ?>  <br /><textarea name="main_text" cols="66" rows="3" wrap="soft" maxlength="255"><?php echo htmlspecialchars($main_text); ?></textarea><br/>
	<?php echo LABEL_SUB_TEXT; ?>	<br /><textarea name="sub_text"  COLS="66" ROWS="3" wrap="soft" maxlength="1000"><?php echo htmlspecialchars($sub_text); ?></textarea><br/>
	
    <input type="hidden" name="id" value="<?php echo $banner_id; ?>" />
    
    <?php 
    	//Check if you are adding new text, or modifying existing text
    	if ($adding)
    	{
    		echo '<input type="hidden" name="action" value="add_text" />';
    	}
    	else
    	{
			echo '<input type="hidden" name="text_id" value="'.$text_id.'" />';
    		echo '<input type="hidden" name="action" value="span_text" />';
    	}
    
    ?>
    
    <br/>
    <input type="submit" name="save" value="<?php echo SAVE_BANNER_TEXT; ?>" />
    <input type="submit" name="preview" value="<?php echo PREVIEW_BANNER_TEXT; ?>" />
</form>
<p>&nbsp;</p>
<hr />
<p>&nbsp;</p>
