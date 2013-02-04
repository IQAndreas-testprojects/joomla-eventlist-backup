<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER_ADMIN') or die('Restricted access');

//Predefine commonly used variables
//$banner_id = JRequest::getInt('id');
$returnURL = "index.php?option=com_eventlist&task=banner_admin";

//"import" setting from JRequest values
$banner_name	 = JRequest::getString('banner_name', "");
$banner_owner 	 = JRequest::getInt('banner_owner');
$banner_category = JRequest::getInt('banner_category');

?>

<!-- EDIT BANNER SETTINGS -->
<form action="<?php echo $returnURL; ?>" method="post">

	<span class="fel">
		<?php echo BannerAdmin::getErrors('banner_name'); ?>
		<?php echo BannerAdmin::getErrors('banner_owner'); ?>
		<?php echo BannerAdmin::getErrors('banner_category'); ?>
	</span>
    
    <?php echo LABEL_NAME; ?> <input type="text" name="banner_name" value="<?php echo htmlspecialchars($banner_name); ?>" width="120" maxlength="255" /><br /><br />
    
    <?php echo LABEL_OWNER; BannerAdmin::showUsersDropdown($banner_owner, 'banner_owner'); ?><br /><br />
    <?php echo LABEL_CATEGORY; BannerAdmin::showCategoriesDropdown($banner_category, 'banner_category'); ?><br /><br />

	<?php 
	
		//Display the next lines based on weather you are adding a new item,
		//or editing an existing one
		if (JRequest::getCmd('action') == "add")
		{
			echo '<input type="submit" name="add" value="'.ADD_BANNER.'" />';
		}
		else //if (JRequest::getCmd('edit'))
		{
			echo '<input type="hidden" name="id" value="'.JRequest::getInt('id').'" />';
			echo '<input type="hidden" name="edit" value="1" />';
			echo '<input type="submit" name="save" value="'.SAVE_BANNER.'" />';
		}
	?>

</form>

<br/><br/>VARNING! Annonsen raderas permanent!
<form action="<?php echo $returnURL; ?>" method="post">
<?php 
		if (JRequest::getCmd('edit'))
		{
			echo '<input type="hidden" name="id" value="'.JRequest::getInt('id').'" />';
			echo '<input type="submit" name="remove" value="'.REMOVE_BANNER.'" />';
		}
?></form>


