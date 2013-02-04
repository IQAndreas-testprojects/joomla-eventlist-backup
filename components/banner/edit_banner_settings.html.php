<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;

//"import" setting from JRequest values
$site_url 			= JRequest::getVar('site_url', "http://");
$background_image 	= JRequest::getVar('background_image', "");
$price_text			= JRequest::getVar('price_text', "");
$time_text			= JRequest::getVar('time_text', "");
$date_text 			= JRequest::getVar('date_text', "") . " " . strftime(JRequest::getVar('date_format', SAMPLE_DATE_TEXT_DATEFORMAT));

$lhead_text = "";
if (BannerActions::getBannerCategoryType($banner_id) == CAT_LUNCHGUIDEN)
	{ $lhead_text = SAMPLE_LHEAD_TEXT; }

$main_text 	= JRequest::getVar('main_text', SAMPLE_MAIN_TEXT);
$sub_text 	= JRequest::getVar('sub_text', 	SAMPLE_SUB_TEXT);

?>

<?php 
    //Show the banner - with applied settings
    require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.php');
    Banner::displayCustomBanner($main_text, $sub_text, $price_text, $time_text, $date_text, $lhead_text, $banner_url, $background_image); //Show with sample text
?>


<!-- EDIT BANNER SETTINGS -->
<form action="<?php echo $bannerEditURL; ?>" method="post">

	<span class="fel">
		<?php echo BannerActions::getErrors('site_url'); ?>
		<?php echo BannerActions::getErrors('background_image'); ?>
		<?php echo BannerActions::getErrors('date_text'); ?>
		<?php echo BannerActions::getErrors('price_text'); ?>
		<?php echo BannerActions::getErrors('time_text'); ?>
	</span>
    <br />
    <?php echo LABEL_SITE_URL; ?> <input type="text" name="site_url" value="<?php echo htmlspecialchars($site_url); ?>" style="width:220px" maxlength="255" /><br /><br />
    <?php echo LABEL_BACKGROUND_IMAGE; ?><input type="text" name="background_image" value="<?php echo htmlspecialchars($background_image); ?>" style="width:220px" maxlength="255" /><br /><br />
    
    <?php echo LABEL_DATE_TEXT; ?><input type="text" name="date_text" value="<?php echo htmlspecialchars(JRequest::getVar('date_text', "")); ?>" style="width:220px" maxlength="255" /><br /><br />
    <?php echo LABEL_PRICE_TEXT; ?><input type="text" name="price_text" value="<?php echo htmlspecialchars($price_text); ?>" style="width:220px" maxlength="255" /><br /><br />
    <?php echo LABEL_TIME_TEXT; ?><input type="text" name="time_text" value="<?php echo htmlspecialchars($time_text); ?>" style="width:220px" maxlength="255" /><br /><br />
    <?php /*echo LABEL_TEXT_CSS; ?> <input type="text" name="css_style" value="<?php echo $css_style; ?>" width="120" maxlength="500" /><br/> */?>

    <input type="hidden" name="date_format" value="<?php echo htmlspecialchars($date_format); ?>" />

    <input type="hidden" name="id" value="<?php echo $banner_id; ?>" />
    <input type="hidden" name="action" value="settings" />
    
    <input type="submit" name="save" value="<?php echo SAVE_BANNER_DESIGN; ?>" />
    <input type="submit" name="preview" value="<?php echo PREVIEW_BANNER_DESIGN; ?>" />
</form>

<br/><br/>

<a href="<?php echo $bannerEditURL; ?>"><i>Tillbaka</i></a>
