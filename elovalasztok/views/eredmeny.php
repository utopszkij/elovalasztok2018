<?php
// input adat: $oevk, $report, $filter, $szavazatTable

// 2017.12.06. úgy tünik lesznek/lehetnek OEVK tanusitások is
// ezért a selectnek lényegesen hosszabbnak kell lennie
// dinamikus db view ellenörzést és generálást is be kell épiteni. Az is lehet, hogy OR kapcsolatos feltételek is lehetnek
// a view -ekben  #__szavazatok_magyar (magyar OR emagyar), 
//                #__szavazatok_oevk_magyar (adott_OEVK vagy magyar magyar vagy emagyar)
//                #__szavazatok_oevk (adott OEVK)  
// 2017.12.06 egyenlőre elég a mindenki és a magyar+emagyar

defined('_JEXEC') or die;
$db = JFactory::getDBO();
$db->setQuery('select count(user_id) as cc from '.$szavazatTable.' where szavazas_id = '.$db->quote($oevk));
$res = $db->loadObject();
$voksDarab = $res->cc;

?>
  <form action="index.php?option_com_jumi&view=application&fileid=4" method="get">
	<input type="hidden" name="option" value="com_jumi" />
	<input type="hidden" name="view" value="application" />
	<input type="hidden" name="fileid" value="4" />
	<input type="hidden" name="oevk" value="<?php echo $oevk; ?>" />
	<input type="hidden" name="task" value="eredmeny" />
	   <h3>Eredmény</h3>
	   <p>	
	   <select name="szavazattable" style="width:auto">
	   <option value="#__szavazatok"<?php if ($szavazatTable == '#__szavazatok') echo ' selected="selected"'; ?>>
	      <span id="eredmenyidopont"><?php echo date('Y.m.d H:i'); ?> -ig leadott </span>
	      minden szavazatot
	   </option> 
	   <!-- option value="#__szavazatok_email"<?php if ($szavazatTable == '#__szavazatok_email') echo ' selected="selected"'; ?>>
	      ellenörzött e-mail -es szavazók szavazatait
	   </option> 
	   <option value="#__szavazatok_hashgiven"<?php if ($szavazatTable == '#__szavazatok_hashgiven') echo ' selected="selected"'; ?>>
	      titkós kóddal rendelkező szavazók szavazatait
	   </option --> 
	   <option value="#__szavazatok_magyar"<?php if ($szavazatTable == '#__szavazatok_magyar') echo ' selected="selected"'; ?>>
	      <span id="eredmenyidopont"><?php echo date('Y.m.d H:i'); ?> -ig leadott </span>
	      hitelesített magyar állampolgárok szavazatait
	   </option> 
	   </select>
		figyelembe véve
	   <button type="submit" class="btn btn-primary">Módosít</button>
		</p>
	<!--
	Szűrés <select name="filter" style="width:390px">
	   <option value=""<?php if ($filter=='') echo ' selected="selected"'; ?>>
	      Minden szavazó
	   </option> 
	   <option value="a.ada0=1"<?php if ($filter=="a.ada0=1") echo ' selected="selected"'; ?>>
	      ADA regisztrált szavazók
	   </option> 
	   <option value="a.ada1=1"<?php if ($filter=="a.ada1=1") echo ' selected="selected"'; ?>>
	      ADA megadták személyi adataikatat
	   </option> 
	   <option value="a.ada2=1"<?php if ($filter=="a.ada2=1") echo ' selected="selected"'; ?>>
	      ADA e-mail aktiváltak
	   </option> 
	   <option value="a.ada3=1"<?php if ($filter=="a.ada3=1") echo ' selected="selected"'; ?>>
	      ADA ellenörzöttek
	   </option> 
	</select>
	-->   
	</form>


	<?php if ($voksDarab > 0) : ?>
		<?php echo $report ?>
		<?php $url = JURI::root().'component/jumi?fileid=4&task=szavazatok&id='.$oevk.'&szavazattable='.urlencode($szavazatTable); ?>
		<p><button type="button" onclick="infoClick()" id="infoBtn">+</button>
			Az eredmény részletei&nbsp;
			<a href="<?php echo $url; ?>">szavazatok</a>
		</p>
	<?php else : ?>
		<div class="noVoksInfo">Nincsenek szavazatok ebben a szavazásban.</div>	
	<?php endif; ?>
	
	<center><br />
	<button type="button" onclick="location='<?php echo $backUrl; ?>';" style="height:34px" class="btn btn-primary btn-back">Vissza</button>
	</center>
	<script type="text/javascript">
	  function infoClick() {
		  var d = document.getElementById("eredmenyInfo");
		  if (d.style.display=="block") {
			  d.style.display="none";
			  document.getElementById("infoBtn").innerHTML="+";
		  } else {
			  d.style.display="block";
			  document.getElementById("infoBtn").innerHTML="-";
		  }
	  }
	</script>
