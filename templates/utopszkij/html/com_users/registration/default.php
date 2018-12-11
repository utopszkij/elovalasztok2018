<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Ezt a filet a <joomlaRoot>/templates/<actualTemplate>/html/com_users/registration
 * könyvtárba kell elhelyezni.             szükséges: jQuery, bootstrap
 * 
 * Megvalósított funkció:
 * - usernek el kell fogadnia az adatkezelési tájékoztatót.
 * 
 * Megjeygzések:
 * - Léteznie kell egy joomla cikknek ami az adatkezelési tájékoztatót tartalmazza.
 *   ennek címét a 34. sorban kell beállítani.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

?>
<div class="registration<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<p>A program müködéséhez elengedhetetlenül szükséges, hogy néhány Önnel kapcsolatos adatot kezeljünk. 
	A program használatához el kell fogadnia az adatkezelési tájékoztatóban leírtakat.<br />
		<a target="_new" href="<?php echo JURI::base(); ?>/setups/adatkezelesi-tajekoztato">
	      Lásd itt: Adatkezelési tájékoztató
	   </a>
	</p>
	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal well" enctype="multipart/form-data">
		<?php // Iterate through the form fieldsets and display each one. ?>
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<?php $fields = $this->form->getFieldset($fieldset->name); ?>
			<?php if (count($fields)) : ?>
				<fieldset>
					<?php // If the fieldset has a label set, display it as the legend. ?>
					<?php if (isset($fieldset->label)) : ?>
						<legend><?php echo JText::_($fieldset->label); ?></legend>
					<?php endif; ?>
					<?php // Iterate through the fields in the set and display them. ?>
					<?php foreach ($fields as $field) : ?>
						<?php // If the field is hidden, just display the input. ?>
						<?php if ($field->hidden) : ?>
							<?php echo $field->input; ?>
						<?php else : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
									<?php if (!$field->required && $field->type !== 'Spacer') : ?>
										<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
									<?php endif; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
		<p>Amenniben a megadott e-mail címhez társítva avatar képet tölt fel a gravatar.com oldalra; akkor a netpolgar rendszer egyes helyeken az Ön nick nevénél ezt az avatar képet meg fogka jeleníteni.</p>
		<p>Valós e-mail címet adjon meg! A regisztráció aktiválásához kapni fog egy levelet, és az abban lévő linkre kell majd kattintania.</p>
		<p><input type="checkbox" value="1" id="accept" />
		  Az adatkezelési leírást elolvastam, megértettem. Elfogadom az adatkezelési leírásban foglaltakat, hozzájárulok az abban leírt adatkezeléshez.</p>

		<div id="modal" style="display:none; border-style: solid; marig:5px; padding:5px; background-color:#ffd0d0">
			<center>
				<p></p>A regisztráciohoz hozzá kell járulnia az adatkezeléshez!</p>
				<button type="button" onclick="jQuery('#modal').toggle();">Bezárás</button>
			</center>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="button" class="btn btn-primary validate" onclick="okClick()">
					<?php echo JText::_('JREGISTER'); ?>
				</button>
				<a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
					<?php echo JText::_('JCANCEL'); ?>
				</a>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="registration.register" />
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<script type="text/javascript">
	function okClick() {
		if (jQuery("#accept").is(':checked')) {
			jQuery('#member-registration').submit();
		} else {
			jQuery('#modal').toggle();
		}
	}
</script>
