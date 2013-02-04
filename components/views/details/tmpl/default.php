<?php
/**
 * @version 1.0 $Id: default.php 958 2009-02-02 17:23:05Z julienv $
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
?>
<div id="eventlist" class="event_id<?php echo $this->row->did; ?> el_details">
	<p class="buttons">
			<?php echo ELOutput::mailbutton( $this->row->slug, 'details', $this->params ); ?>
			<?php echo ELOutput::printbutton( $this->print_link, $this->params ); ?>
	</p>

<?php if ($this->params->def( 'show_page_title', 1 )) : ?>
	<h1 class="componentheading">
		<?php echo $this->params->get('page_title'); ?>
	</h1>
	
<?php endif; ?>


<!-- Details EVENT -->
	<h2 class="eventlist">
	<p>
		<?php
    	echo JText::_( 'EVENT' );
    	echo '&nbsp;'.ELOutput::editbutton($this->item->id, $this->row->did, $this->params, $this->allowedtoeditevent, 'editevent' );
    	?>
    </p>
	</h2>

	<?php //flyer
	echo ELOutput::flyer( $this->row, $this->dimage, 'event' );
	?>
	
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<dl class="event_info floattext">
	<tr>
	

		<?php if ($this->elsettings->showdetailstitle == 1) : ?>
		<td valign="top">
			<dt class="title"><?php echo JText::_( 'TITLE' ).':'; ?></dt>
		</td>
		<td width="380" valign="top">
    		<dd class="title"><?php echo $this->escape($this->row->title); ?></dd>
    	</td>
		<?php
  		endif;
  		?>
  	</tr>
  	<tr>
  		<td valign="top">
  		<dt class="when"><?php echo JText::_( 'WHEN' ).':'; ?></dt>
  		</td>
  		<td width="380" valign="top">
		<dd class="when"><div align="left">


			<?php
			echo ELOutput::formatdate($this->row->dates, $this->row->times);
    					
    		if ($this->row->enddates) :
    			echo ' - '.ELOutput::formatdate($this->row->enddates, $this->row->endtimes);
    		endif;
    		
    		if ($this->elsettings->showtimedetails == 1) :
    	
				echo '&nbsp;'.ELOutput::formattime($this->row->dates, $this->row->times);
						
				if ($this->row->endtimes) :
					echo ' - '.ELOutput::formattime($this->row->enddates, $this->row->endtimes);
				endif;
			endif;
			?>
		
		</div></dd>
  		<?php
  		if ($this->row->locid != 0) :
  		?>
  		
  		</td>
  	</tr>
  	<tr>
  		<td valign="top">
		    <dt class="where"><?php echo JText::_( 'WHERE' ).':'; ?>
		    <p><?php echo ELOutput::editbutton($this->item->id, $this->row->locid, $this->params, $this->allowedtoeditvenue, 'editvenue' ); ?></p>
		    </dt>
		</td>
		<td width="380" valign="top" align="left">
		    <dd class="where">
		    <div align="left">
		    <a href="<?php echo JRoute::_( 'index.php?view=venueevents&id='.$this->row->venueslug ); ?>"><?php echo $this->row->venue; ?> - information</a><br />
    		<?php if (($this->elsettings->showdetlinkvenue == 1) && (!empty($this->row->url))) : ?>   
			    <a href="<?php echo $this->row->url; ?>"><?php echo $this->escape($this->row->venue); ?> - webbsida</a>
				</div><br />
			<?php elseif ($this->elsettings->showdetlinkvenue == 0) :

				echo $this->escape($this->row->venue).' - ';

			endif;

			 ?>

			</dd>
		</td>
		<?php endif; ?>

		<!--dt class="category"><?php echo JText::_( 'CATEGORY' ).':'; ?></dt>
    		<dd class="category">
				<?php echo "<a href='".JRoute::_( 'index.php?view=categoryevents&id='.$this->row->categoryslug )."'>".$this->escape($this->row->catname)."</a>";?>
			</dd-->
	</dl>
	</tr>
</table>
  	<?php if ($this->elsettings->showevdescription == 1) : ?>

		
  	    	<h2 class="description"><?php echo JText::_( 'DESCRIPTION' ); ?></h2>
  	 
  	
  		<div class="description event_desc">
  			<?php echo $this->row->datdescription; ?>
  		</div>
		
	
  	<?php endif; ?>

<!--  	Venue  -->

	<?php if ($this->row->locid != 0) : ?>

		<!--h2 class="location">
			<?php echo JText::_( 'VENUE' ) ; ?>
  			<?php echo ELOutput::editbutton($this->item->id, $this->row->locid, $this->params, $this->allowedtoeditvenue, 'editvenue' ); ?>
		</h2>

		<?php //flyer
		echo ELOutput::flyer( $this->row, $this->limage );
		echo ELOutput::mapicon( $this->row );
		?>

		<dl class="location floattext">
			 <dt class="venue"><?php echo $this->elsettings->locationname.':'; ?></dt>
				<dd class="venue">
				<?php echo "<a href='".JRoute::_( 'index.php?view=venueevents&id='.$this->row->venueslug )."'>".$this->escape($this->row->venue)."</a>"; ?>

				<?php if (!empty($this->row->url)) : ?>
					&nbsp; - &nbsp;
					<a href="<?php echo $this->row->url; ?>"> <?php echo JText::_( 'WEBSITE' ); ?></a>
				<?php
				endif;
				?>
				</dd>

			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>

  				<?php if ( $this->row->street ) : ?>
  				<dt class="venue_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
				<dd class="venue_street">
    				<?php echo $this->escape($this->row->street); ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->plz ) : ?>
  				<dt class="venue_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
				<dd class="venue_plz">
    				<?php echo $this->escape($this->row->plz); ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->city ) : ?>
    			<dt class="venue_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    			<dd class="venue_city">
    				<?php echo $this->escape($this->row->city); ?>
    			</dd>
    			<?php endif; ?>

    			<?php if ( $this->row->state ) : ?>
    			<dt class="venue_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
    			<dd class="venue_state">
    				<?php echo $this->escape($this->row->state); ?>
    			</dd>
				<?php endif; ?>

				<?php if ( $this->row->country ) : ?>
				<dt class="venue_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    			<dd class="venue_country">
    				<?php echo $this->row->countryimg ? $this->row->countryimg : $this->row->country; ?>
    			</dd>
    			<?php endif; ?>
			<?php
			endif;
			?>
		</dl-->

		<?php if ($this->elsettings->showlocdescription == 1) :	?>

			<h2 class="location_desc"><?php echo JText::_( 'DESCRIPTION' ); ?></h2>
  			<div class="description location_desc">
  				<?php echo $this->row->locdescription;	?>
  			</div>

		<?php endif; ?>

	<?php
	//row->locid !=0 end
	endif;
	?>

	<?php if ($this->row->registra == 1) : ?>

		<!-- Registration -->
		<?php echo $this->loadTemplate('attendees'); ?>

	<?php endif; ?>
	
	<?php if ($this->elsettings->commentsystem != 0) :	?>
	
		<!-- Comments -->
		<?php echo $this->loadTemplate('comments'); ?>
		
  	<?php endif; ?>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
</div>