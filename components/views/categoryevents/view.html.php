<?php
/**
 * @version 1.0 $Id: view.html.php 1006 2009-04-21 20:31:53Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Categoryevents View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewCategoryevents extends JView
{
	/**
	 * Creates the Categoryevents View
	 *
	 * @since 0.9
	 */
	function display( $tpl=null )
	{
		global $mainframe, $option;

		//initialize variables
		$document 	= & JFactory::getDocument();
		$menu		= & JSite::getMenu();
		$elsettings = & ELHelper::config();
		$item    	= $menu->getActive();
		$params 	= & $mainframe->getParams();
		$uri 		= & JFactory::getURI();
		$pathway 	= & $mainframe->getPathWay();

		//add css file
		$document->addStyleSheet($this->baseurl.'/components/com_eventlist/assets/css/eventlist.css');
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #eventlist dd { height: 1%; }</style><![endif]-->');

		// Request variables
		$limitstart		= JRequest::getInt('limitstart');
		$limit       	= $mainframe->getUserStateFromRequest('com_eventlist.categoryevents.limit', 'limit', $params->def('display_num', 0), 'int');
		$task 			= JRequest::getWord('task');
		$pop			= JRequest::getBool('pop');

		//get data from model
		$rows 		= & $this->get('Data');
		$category 	= & $this->get('Category');
		$total 		= & $this->get('Total');

		//are events available?
		if (!$rows) {
			$noevents = 1;
		} else {
			$noevents = 0;
		}

		//does the category exist
		if ($category->id == 0)
		{
			return JError::raiseError( 404, JText::sprintf( 'Category #%d not found', $category->id ) );
		}

		//Set Meta data
		$document->setTitle( $item->name.' - '.$category->catname );
    	$document->setMetadata( 'keywords', $category->meta_keywords );
    	$document->setDescription( strip_tags($category->meta_description) );

    	//Print function
		$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
		$params->def( 'icons', $mainframe->getCfg( 'icons' ) );

		if ( $pop ) {
			$params->set( 'popup', 1 );
		}

		//add alternate feed link
		$link    = 'index.php?option=com_eventlist&view=categoryevents&format=feed&id='.$category->id;
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink(JRoute::_($link.'&type=atom', 'alternate', 'rel'), $attribs);

		//create the pathway
		$pathway->setItemName(1, $item->name);
		
		if ($task == 'archive') {
			$pathway->addItem( JText::_( 'ARCHIVE' ).' - '.$category->catname, JRoute::_('index.php?option='.$option.'&view=categoryevents&task=archive&id='.$category->slug));
			$link = JRoute::_( 'index.php?option=com_eventlist&view=categoryevents&task=archive&id='.$category->slug );
			$print_link = JRoute::_( 'index.php?option=com_eventlist&view=categoryevents&id='. $category->id .'&task=archive&pop=1&tmpl=component');
		} else {
			$pathway->addItem( $category->catname, JRoute::_('index.php?option='.$option.'&view=categoryevents&id='.$category->slug));
			$link = JRoute::_( 'index.php?option=com_eventlist&view=categoryevents&id='.$category->slug );
			$print_link = JRoute::_( 'index.php?option=com_eventlist&view=categoryevents&id='. $category->id .'&pop=1&tmpl=component');
		}

		//Check if the user has access to the form
		$maintainer = ELUser::ismaintainer();
		$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

		if ($maintainer || $genaccess ) $dellink = 1;

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		//Generate Categorydescription
		if (empty ($category->catdescription)) {
			$catdescription = JText::_( 'NO DESCRIPTION' );
		} else {
			//execute plugins
			$category->text	= $category->catdescription;
			$category->title 	= $category->catname;
			JPluginHelper::importPlugin('content');
			$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$category, &$params, 0 ));
			$catdescription = $category->text;
		}

		if ($category->image != '') {

			$imgattribs['width'] = $elsettings->imagewidth;
			$imgattribs['height'] = $elsettings->imagehight;

			$category->image = JHTML::image('images/stories/'.$category->image, $category->catname, $imgattribs);
		} else {
			$category->image = JHTML::image('components/com_eventlist/assets/images/noimage.png', $category->catname);
		}

		//create select lists
		$lists	= $this->_buildSortLists($elsettings);
		$this->assign('lists', 						$lists);
		$this->assign('action', 					$uri->toString());

		$this->assignRef('rows' , 					$rows);
		$this->assignRef('noevents' , 				$noevents);
		$this->assignRef('category' , 				$category);
		$this->assignRef('print_link' , 			$print_link);
		$this->assignRef('params' , 				$params);
		$this->assignRef('dellink' , 				$dellink);
		$this->assignRef('task' , 					$task);
		$this->assignRef('catdescription' , 		$catdescription);
		$this->assignRef('pageNav' , 				$pageNav);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('item' , 					$item);

		parent::display($tpl);
	}

	/**
	 * Manipulate Data
	 *
	 * *************************************************************************************
	 * HEAVY modifications by Andreas Renberg, including adding extra functions at the end
	 * November 2009
	 *
	 * @since 0.9
	 */
	function &getRows()
	{
		$count = count($this->rows);

		if (!$count) {
			return;
		}
		
		$recurrences = array();
		$maxDate = "0000-00-00";
		
		$k = 0;
		foreach($this->rows as $key => $row)
		{
			$row->odd   = $k;
			
			if (($row->recurrence_number != "0") && ($row->recurrence_type != "0"))
			{
				//Push the key into the array, and use it later instead
				$recurrences[] = $key;
			}
			
			$maxDate = $row->dates;
			
			$this->rows[$key] = $row;
			$k = 1 - $k;
		}
		
		//Now, push the new rows into the array!!
		foreach($recurrences as $idnum)
		{
			
			//This variable contains the newest value of the row that is pushed
			//It begins as the original row
			$cloneRow = $this->rows[$idnum]; 
			$cloneRow = $this->clone_array($cloneRow); 
			$cloneRow = $this->next_date($cloneRow); 
			
			$maxCounter = 0;
			
			while ((($cloneRow->dates <= $cloneRow->recurrence_counter) || ($cloneRow->recurrence_counter == "0000-00-00")) 
					&& ($maxCounter <= 10)
					&& ($cloneRow->dates <= $maxDate))
			{
				//Save the clone to the array
				$this->rows[] = $cloneRow;
				
				//Now get the next one
				$cloneRow = $this->next_date($this->clone_array($cloneRow));
				
				$maxCounter++;
			}
			
		}
		
		
		function cmp($a, $b)
		{
			if ($a->dates == $b->dates) {
				return 0;
			}
			return ($a->dates < $b->dates) ? -1 : 1;
		}		
		
		//Now, sort the rows on the date
		usort($this->rows, "cmp");
		
		return $this->rows;
	}

	function _buildSortLists($elsettings)
	{
		// Table ordering values
		$filter_order		= JRequest::getCmd('filter_order', 'a.dates');
		$filter_order_Dir	= JRequest::getCmd('filter_order_Dir', 'ASC');

		$filter				= $this->escape(JRequest::getString('filter'));
		$filter_type		= JRequest::getString('filter_type');

		$sortselects = array();
		$sortselects[]	= JHTML::_('select.option', 'title', $elsettings->titlename );
		$sortselects[] 	= JHTML::_('select.option', 'venue', $elsettings->locationname );
		$sortselects[] 	= JHTML::_('select.option', 'city', $elsettings->cityname );
		$sortselect 	= JHTML::_('select.genericlist', $sortselects, 'filter_type', 'size="1" class="inputbox"', 'value', 'text', $filter_type );

		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		$lists['filter'] 		= $filter;
		$lists['filter_type'] 	= $sortselect;

		return $lists;
	}
	
	
// *********************************************
//    Andreas Renberg - Nov 2009
//  Function taken from helper.php

	/**
	 * this methode calculate the next date
	 */
	function next_date($recurrence_row) {
		// get the recurrence information
		$recurrence_number = $recurrence_row->recurrence_number;
		$recurrence_type = $recurrence_row->recurrence_type;

		$day_time = 86400;	// 60 sec. * 60 min. * 24 h
		$week_time = 604800;// $day_time * 7days
		$date_array = ELHelper::generate_date($recurrence_row->dates, $recurrence_row ->enddates);


		switch($recurrence_type) {
			case "1":
				// +1 hour for the Summer to Winter clock change
				$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);
				$start_day = $start_day + ($recurrence_number * $day_time);
				break;
			case "2":
				// +1 hour for the Summer to Winter clock change
				$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);
				$start_day = $start_day + ($recurrence_number * $week_time);
				break;
			case "3":
				$start_day = mktime(1,0,0,($date_array["month"] + $recurrence_number),$date_array["day"],$date_array["year"]);;
				break;
			default:
				$weekday_must = ($recurrence_row->recurrence_type - 3);	// the 'must' weekday
				if ($recurrence_number < 5) {	// 1. - 4. week in a month
					// the first day in the new month
					$start_day = mktime(1,0,0,($date_array["month"] + 1),1,$date_array["year"]);
					$weekday_is = date("w",$start_day);							// get the weekday of the first day in this month

					// calculate the day difference between these days
					if ($weekday_is <= $weekday_must) {
						$day_diff = $weekday_must - $weekday_is;
					} else {
						$day_diff = ($weekday_must + 7) - $weekday_is;
					}
					$start_day = ($start_day + ($day_diff * $day_time)) + ($week_time * ($recurrence_number - 1));
				} else {	// the last or the before last week in a month
					// the last day in the new month
					$start_day = mktime(1,0,0,($date_array["month"] + 2),1,$date_array["year"]) - $day_time;
					$weekday_is = date("w",$start_day);
					// calculate the day difference between these days
					if ($weekday_is >= $weekday_must) {
						$day_diff = $weekday_is - $weekday_must;
					} else {
						$day_diff = ($weekday_is - $weekday_must) + 7;
					}
					$start_day = ($start_day - ($day_diff * $day_time));
					if ($recurrence_number == 6) {	// before last?
						$start_day = $start_day - $week_time;
					}
				}
				break;
		}
		$recurrence_row->dates = date("Y-m-d", $start_day);
		if ($recurrence_row->enddates) {
			$recurrence_row->enddates = date("Y-m-d", $start_day + $date_array["day_diff"]);
		}
		return $recurrence_row;
	}
	
	
	
	function generate_date($startdate, $enddate) {
		$startdate = explode("-",$startdate);
		$date_array = array("year" => $startdate[0],
							"month" => $startdate[1],
							"day" => $startdate[2],
							"weekday" => date("w",mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0])));
		if ($enddate) {
			$enddate = explode("-", $enddate);
			$day_diff = (mktime(1,0,0,$enddate[1],$enddate[2],$enddate[0]) - mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));
			$date_array["day_diff"] = $day_diff;
		}
		return $date_array;
	}
	
	
	function clone_array($arrIn)
	{
		//Initialize the out variable
		$arrOut;// = new object();
		
		foreach($arrIn as $inKey => $inValue)
		{
			//$arrOut[$inKey] = $inValue;
			//$arrOut->{$inKey} = unserialize(serialize($inValue));
			$arrOut->{$inKey} = $inValue;
		}
		
		return $arrOut;
	}

//    END Andreas Renberg
// *********************************************

	
}
?>