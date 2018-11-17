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
echo '<h2>'.$pollRecord->title.'</h2>
<div class="pollLeiras">'.$pollRecord->description.'</div>
';
?>
  <form action="index.php?option_com_jumi&view=application&fileid=4" method="get">
	<input type="hidden" name="option" value="com_jumi" />
	<input type="hidden" name="view" value="application" />
	<input type="hidden" name="fileid" value="4" />
	<input type="hidden" name="oevk" value="<?php echo $oevk; ?>" />
	<input type="hidden" name="task" value="eredmeny" />
	   <h3><?php if ($evConfig->szavazas) echo 'Pillanatnyi Részeredmény'; else echo 'Eredmény' ?></h3>
	   <p>	
	   <span id="eredmenyidopont"><?php echo date('Y.m.d H:i'); ?> -ig leadott szavazatokat 
		figyelembe véve </span>
		</p>
	</form>

	<?php echo $report ?>
    <div id="condorcetMagyarazat" style="display:none">
            A <strong>dMatrix</strong> és a <strong>pMatrix</strong> sorai és oszlopai egyaránt egy-egy jelöltnek felelnek meg.<br />
            A <strong>dMatrix</strong> cellái azt mutatják, hogy a sorban szereplő jelölt hányszor elözi meg az oszlopban lévőt.<br />
            A <strong>pMatrix</strong> a Shulze methód második lépésének munka táblázata.
    </div>
	<?php $url = JURI::root().'component/elovalasztok?task=szavazatok&id='.$oevk; ?>
	<?php $urlcsv = JURI::root().'component/elovalasztok?task=szavazatokcsv&id='.$oevk; ?>
	<p><button type="button" onclick="infoClick()" id="infoBtn">+</button>
			Az eredmény részletei&nbsp;&nbsp;
			<a href="<?php echo $url; ?>">szavazatok</a>&nbsp;&nbsp;
			<a href="<?php echo $urlcsv; ?>">szavazatok CSV formában</a>&nbsp;&nbsp;
	</p>
	
	<script type="text/javascript">
	  function infoClick() {
		  var d = document.getElementById("eredmenyInfo");
		  if (d.style.display=="block") {
			  d.style.display="none";
              document.getElementById('condorcetMagyarazat').style.display="none";
			  document.getElementById("infoBtn").innerHTML="+";
		  } else {
			  d.style.display="block";
              document.getElementById('condorcetMagyarazat').style.display="block";
			  document.getElementById("infoBtn").innerHTML="-";
		  }
	  }
	</script>
