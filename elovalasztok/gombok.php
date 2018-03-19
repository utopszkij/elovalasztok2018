<?php
/**
  * előválasztási web oldal gombok modul
  *
  * telepitve kell lennie a com_pvoks -nak
  * a JUMI fielid=1 tartalmazza a config beállítást JSON formában
  *
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  * Jrequest: option, view,  fileid, (id  | oevk)
  */
  defined('_JEXEC') or die;

  global $config; 
  include_once dirname(__FILE__).'/accesscontrol.php';
  include_once dirname(__FILE__).'/funkciok.php';
  include_once dirname(__FILE__).'/config.php';



	function isMobileDev(){
		if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])){
		   $user_ag = $_SERVER['HTTP_USER_AGENT'];
		   if(preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){
		      return true;
		   }else{
		      return false;
		   };
		}else{
		   return false;    
		};
	};


  $user = JFactory::getUser();
  $msg = '';
  $input = JFactory::getApplication()->input;  
  $task = $input->get('task');
  $szavazas_id = $input->get('szavazas_id',0);
  $id = $input->get('id',0);
  $option = $input->get('option');
  $view = $input->get('view');
  $fileid = $input->get('fileid');
  $id = $input->get('id');
  $redi = 'https://elovalasztok.edemokraciagep.org'.$_SERVER['REQUEST_URI'];
  $loginURL = JURI::root().'index.php?option=com_adalogin&redi='.base64_encode($redi);
  if (isMobileDev()) {
  	$loginScript = "location='$loginURL'";
  } else {	 
  	$loginScript = "open('$loginURL','ADA','width=370,height=600,left=100,top=100');";
  }
  if ($option == 'com_content') {
	  if ($view == 'article') {
		  $szavazas_id = oevkFromJelolt($id);
	  }
  }
  
  if ($szavazas_id == 0)  $szavazas_id = $id;
  
$userToken = JSession::getFormToken();
$logoutLink = JURI::root(). 'index.php?option=com_adalogin&task=dologout';

if ($user->id > 0) {
	$marSzavazott = holSzavazott($szavazas_id, $user); 
	if ($marSzavazott != '') {
		echo '<div id="szavazott_info">
		'.$marSzavazott.'
		</div>
		'; 
	}
}

?>

<div id="elovalasztok-gombok" class="elovalasztok-gombok">
  <center>
	<!-- p class="category-desc">Egy ember egy egyéni választókerületben, a miniszterelnök szavazáson és a országos pártlista szavazáson,
 csak egyszer szavazhat. Viszont lehet ismételten szavazni, ezzel korábbi szavazatát felülbírálhatja. 
 Ilyenkor a korábbitól eltérő választókörzetben is szavazhat.</p -->
  <div class="gombok1">
  <div class="gombok2">
  <?php if ($user->id <= 0) : ?>
    <button id="loginBtn" type="button" title="Bejelentkezés" 
      onclick="<?php echo $loginScript; ?>">
      <i class="icon-login"> </i> <label>Bejelentkezés</label>
    </button>
  <br />
  <?php endif; ?>
    
  <?php if ($user->id > 0) : ?>
	<var class="username"><?php echo 'user'.$user->id; ?></var>
    <button id="logoutBtn" onclick="location='<?php echo $logoutLink; ?>';" title="Kijelentkezés">
       <i class="icon-logout"> </i><label>Kijelentkezés</label>
    </button><br />
  <?php endif; ?>
  
  <?php if (
						(isOevkSzavazas($szavazas_id) | isMiniszterElnokSzavazas($szavazas_id) | isOrszagosListaSzavazas($szavazas_id)) & 
					  ($task != 'szavazasedit') & ($task != 'szavazok')
					 ) : ?>
 
  <?php	if ((/*isOevkSzavazas($szavazas_id) |*/
			 isMiniszterElnokSzavazas($szavazas_id) | 
			 isOrszagosListaSzavazas($szavazas_id)) & 
			($szavazas_id != 116) ) : ?>
	<?php if (teheti($szavazas_id, $user, 'szavazas', $msg) & (szavazottMar($szavazas_id, $user, 0) == false)) : ?>
		  <button id="szavazokBtn" title="Szavazok"
			type="button" onclick="location='<?php echo JURI::root(); ?>component/jumi?fileid=4&task=szavazok&id=<?php echo $szavazas_id; ?>';">
			<i class="icon-szavazok"> </i><label>Szavazok</label>
		  </button><br />
	  <?php elseif (szavazottMar($szavazas_id, $user, 0)) : ?>
		  <button id="szavazokBtn" title="Újra szavazok"
			type="button" onclick="location='<?php echo JURI::root(); ?>component/jumi?fileid=4&task=szavazatedit&id=<?php echo $szavazas_id; ?>';">
			<i class="icon-szavazok"> </i><label>Újra szavazok</label>
		  </button><br />
		 <?php else : ?>
		  <div class="nemszavazhat">
			<i class="icon-nemszavazhat"> </i>
			<label><?php echo $msg; ?></label>
		  </div><br />
	  <?php endif; ?>
	<?php else : ?>
		<!--span style="border-style:solid; border-width:1px; border-radius:5px; padding:5px;">Jelenleg nem lehet szavazni</span -->
    <?php endif; ?>
  <?php endif; ?>

  
  <?php	if ((isOevkSzavazas($szavazas_id) | isMiniszterElnokSzavazas($szavazas_id) | isOrszagosListaSzavazas($szavazas_id)) & 
			($szavazas_id != 116)) : ?>
  <?php if (teheti($szavazas_id, $user, 'eredmeny',$msg)) : ?>
  <button id="eredmenyBtn" <?php echo $d; ?> title="Eredmény"
    type="button" onclick="location='<?php echo JURI::root(); ?>component/jumi?fileid=4&task=eredmeny&id=<?php echo $szavazas_id; ?>';">
    <i class="icon-eredmeny"> </i><label>Eredmény</label>
  </button>
  <?php endif; ?>
  <?php endif; ?>
     
  <button id="keruletekBtn" title="szavazások" 
    type="button" onclick="location='<?php echo JURI::root(); ?>component/content/category?id=8';">
	<i class="icon-oevk"> </i><label>Szavazások</label>
  </button>
	
  <a title="Melyik OEVK-ba tartozom" id="oevksearchbtn"
		href="http://www.valasztas.hu/dyn/pv14/map/index_frame_noborder.html" target="new">
		Melyik OEVK-ba tartozom?
  </a><br />

  </div>
  </div>
  </center>
</div>
  
  
