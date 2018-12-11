<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Ezt az állományt a <joomla_root>/templates/<actualTemplate>/html/com_users/profile
 * könyvtárba kell elhelyezni.             szükséges: jQuery, bootstrap
 * 
 * Megvalósitott funkciók:
 * - adataim törlése
 * - adatkezelési hozzájárulás felfüggesztése
 * - adatkezeléshez hozzájárulás
 * - adat exportálás XML fájlba
 * 
 * Megjegyzések:
 * 1 A "adatkezelési hozzájárulás felfügessztése" funkció a core joomlában
 *   nincs megvalósitva, Az ebben a fileban megvalósított megoldás csak az
 *   általam fejlesztett com_netpolgar komponensben fog müködni 
 *   (users->params->suspended = 1 vagy 0 tárolja az infot).
 * 2. A "adataim törlése" funkció úgy van megvalósitva, hogy a #__users táblában
 *   a username, name és email mezők tartalma átíródik, de a rekord megmarad,
 *   így nem sérül az adatbázis integritása (több más táblában lehet ide mutató
 *   hivatkozás).
 * 3. A kódban szereplő szöveges tájékotatók, magyarázatok természetesen mindig 
 *   aktualizálandóak.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

// Load user_profile plugin language
// GDPR funciók kezelése
$db = JFactory::getDBO();
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
$session = JFactory::getSession();
$user = JFactory::getUser();
if ($user->params == '') 
	$params = new stdClass();
else
	$params = JSON_decode($user->params);	
if (!isset($params->suspended)) $params->suspended = 0;

$link = JURI::current();
$link = str_replace('?gdpr=delete','',$link);
$link = str_replace('?gdpr=suspend','',$link);
$link = str_replace('?gdpr=unsuspend','',$link);
$link = str_replace('?gdpr=export','',$link);
$GDPRaction = JFactory::getApplication()->input->get('gdpr','@');
if ($GDPRaction == 'delete') {
	if (!isset($user->groups[8])) {
		$db->setQuery('update #__users
		set name = "***'.$user->id.'",
		username = "***'.$user->id.'",
		email = "notvalid'.$user->id.'@'.$user->id.'notvalid.xyz"	
		where id ='.$user->id);
		$db->query();
		echo '
		<p>delete your data from database .... please wait</p>
		<script type="text/javascript">
			location = "'.JURI::base().'kijelentkezes";
		</script>
		';
	} else {
		echo '<p>super user login delete not enabled.</p>';
	}	
} else if ($GDPRaction == 'suspend') {
	$params->suspended = 1;
	$db->setQuery('update #__users
	set params = '.$db->quote(JSON_encode($params)).',
	activation = "suspended"
	where id ='.$user->id);
	$db->query();
} else if ($GDPRaction == 'unsuspend') {
	$params->suspended = 0;
	$db->setQuery('update #__users
	set params = '.$db->quote(JSON_encode($params)).',
	activation = ""
	where id ='.$user->id);
	$db->query();
} else if ($GDPRaction == 'export') {
	header('Content-Type: application/xml');
	echo '<?xml version="1.0" encoding="utf-8"?>
	<user>
	<username>'.$user->username.'</username>
	<name>'.$user->name.'</name>
	<email>'.$user->email.'</email>
	</user>
	';
	exit();
}
?>

<div class="GDPR">
	<?php if ($params->suspended) : ?>
		<p>Ön visszavonta adatkezelési hozzájárulását. Ebben a helyzetben adatai a rendszerünkben sehol
		 nem jelennek meg, adatait harmadik félnek nem adjuk ki. Ön be tud jelentkezni a programba, de ezen az
		 "Adatlap" funkción kivül más menüpontot csak a minden látogatok számára megengedett jogosultságok
		  szerint használhat.</p>
		<a class="btn btn-success" 
			href="<?php echo $link.'?gdpr=unsuspend'; ?>">
			Adatkezeléshez hozzájárulok
		</a>
	<?php else : ?>	
		<a class="btn btn-warning" 
			href="<?php echo $link.'?gdpr=suspend'; ?>">
			Adatkezelési hozzájárulás visszavonása
		</a>
	<?php endif; ?>	
	<a class="btn btn-primary" target="_new"
		href="<?php echo $link.'?gdpr=export'; ?>">
		Adataim exportálása XML fájlba
	</a>
	<a class="btn btn-danger" onclick="jQuery('#deleteInfo').toggle()">Adataim törlése</a>
	
	<div id="deleteInfo" style="display:none">
		Ha törli az adatait, akkor:
		<ul>
		  <li>A program adatbázisából töröljük az Ön nevét és e-mail címét</li>	
		  <li>A továbbiakban nem tud bejelentkezni a programba, a programot a továbbiakban csak a 
		 "nem regisztrált látogatók" jogosultságaival használhatja
		  </li>
		  <li>Az eddig végzett tranzakció, adat feltöltései a rendszerben megmaradnak, de a
		   továbbiakban nem lesz azonosítható, hogy ezeket Ön csinálta.
		  </li>
		  <li>Bármikor újra regisztrálhat</li>
		</ul>
		<center>
			<a class="btn btn-danger" href="<?php echo $link.'?gdpr=delete'; ?>">
				Törlés végrehajtása
			</a>
		</center>
	</div>	
	
</div><!-- GDPR -->

<div class="profile-edit<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<script type="text/javascript">
		Joomla.twoFactorMethodChange = function(e)
		{
			var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

			jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el)
			{
				if (el.id != selectedPane)
				{
					jQuery('#' + el.id).hide(0);
				}
				else
				{
					jQuery('#' + el.id).show(0);
				}
			});
		}
	</script>
	<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal well" enctype="multipart/form-data">
		<?php // Iterate through the form fieldsets and display each one. ?>
		<?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
			<?php $fields = $this->form->getFieldset($group); ?>
			<?php if (count($fields)) : ?>
				<fieldset class="<?php echo $group; ?>">
					<?php // If the fieldset has a label set, display it as the legend. ?>
					<?php if (isset($fieldset->label)) : ?>
						<legend>
							<?php echo JText::_($fieldset->label); ?>
						</legend>
					<?php endif; ?>
					<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
						<p>
							<?php echo $this->escape(JText::_($fieldset->description)); ?>
						</p>
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
										<span class="optional">
											<?php echo JText::_('COM_USERS_OPTIONAL'); ?>
										</span>
									<?php endif; ?>
								</div>
								<div class="controls">
									<?php if ($field->fieldname === 'password1') : ?>
										<?php // Disables autocomplete ?>
										<input type="password" style="display:none">
									<?php endif; ?>
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if (count($this->twofactormethods) > 1) : ?>
			<fieldset>
				<legend><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH'); ?></legend>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
							title="<?php echo '<strong>' . JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') . '</strong><br />' . JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC'); ?>">
							<?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
						</label>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
					</div>
				</div>
				<div id="com_users_twofactor_forms_container">
					<?php foreach ($this->twofactorform as $form) : ?>
						<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
						<div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
							<?php echo $form['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<fieldset>
				<legend>
					<?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
				</legend>
				<div class="alert alert-info">
					<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
				</div>
				<?php if (empty($this->otpConfig->otep)) : ?>
					<div class="alert alert-warning">
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
					</div>
				<?php else : ?>
					<?php foreach ($this->otpConfig->otep as $otep) : ?>
						<span class="span3">
							<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
						</span>
					<?php endforeach; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			</fieldset>
		<?php endif; ?>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary validate">
					Tárolás
				</button>
				<a class="btn" href="<?php echo JURI::root(); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
					Mégsem
				</a>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="profile.save" />
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
