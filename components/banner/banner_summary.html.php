<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$banner = BannerDatabase::getBanner($banner_id);
$base_url = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;

$categoryType = BannerActions::getBannerCategoryType($banner_id);

$returnURL = "index.php?option=com_eventlist&task=banner_admin";
?>

<br/><br/>
<b>Banner sammanfattning för <?php echo htmlspecialchars($banner->name); ?></b><br />
<p>&nbsp;&nbsp;<a href="<?php echo $returnURL."&edit=1&id=".$banner_id; ?>">Ändra banner inställningar</a></p>
<p>&nbsp;&nbsp;<a href="<?php echo $base_url; ?>">Ändra banner text och utseende</a></p>

<?php 
	//Show a different banner list depending on category!
	
	if ($categoryType == CTYPE_SINGLE)
	{
		BannerActions::showBannerSummaryTable_single($banner_id, $base_url);
	}
	elseif ($categoryType == CTYPE_SPAN)
	{
		BannerActions::showBannerSummaryTable_span($banner_id, $base_url);
	}
	else
	{
		//By default, show span
		BannerActions::showBannerSummaryTable_span($banner_id, $base_url);
	}
?>

<br/>
<p><a href="<?php echo $returnURL; ?>">Tillbaka till banner listan</a></p><br />



