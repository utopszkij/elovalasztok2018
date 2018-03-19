<?php
// szavazás leadása képernyő
// be: $item->oevkId, $item->oevkNev,  $item->alternativak [{"id":szám, "nev": string},...], $user

defined('_JEXEC') or die;

$db = JFactory::getDBO();
$cancelUrl = JURI::root().'index.php?option=com_content&view=category&layout=articles&id='.$item->oevkId;
echo '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="elovalasztok/views/vote.js" type="text/javascript"></script>
';
   
echo '<h2>'.$item->oevkNev.'</h2>
<div id="divTurelem" style="display:none; background-color:transparent; cursor:default;"></div>
<form method="post" action="'.JURI::root().'index.php?option=com_jumi" name="szavazatForm" id="szavazatForm">
<input type="hidden" name="view" value="application" />
<input type="hidden" name="fileid" value="4"/>
<input type="hidden" name="task" value="szavazatSave" /> 
<input type="hidden" name="task" value="szavazatSave" />
<input type="hidden" name="nick" value="'.$user->username.'" />
<input type="hidden" name="oevk" value="'.$item->oevkId.'" />
'.JHtml::_('form.token').'
';
if (count($item->alternativak)==0) {
  echo '
  <div class="msg">Nincs egyetlen jelölt sem</div>';
  echo '<center><button type="button" onclick="location='."'$cancelUrl'".'" class="btnCancel">Vissza</button></center>';
  return;
}

echo '<div>
';



if ($item->oevkId == MELNOK()) 
	echo '
	<p id="voksHelp">Rangsorold a jelölteket! A legjobbnak tartott kerüljön felülre. Egérrel huzhatod a sorokat,
	vagy használd a lenyiló menüt. Több jelöltet is azonos pozicióba sorolhatsz. A "-- A többit ellenzem--" felett legyenek akiket
  elfogadhatónak tartasz, alatta a szerinted elfogadhatatlanok.</p>
	';
else if ($item->oevkId == OLISTA()) 
	echo '
	<p id="voksHelp">Rangsorold a listákat! A legjobbnak tartott kerüljön felülre. Egérrel huzhatod a sorokat,
	vagy használd a lenyiló menüt. Több listát is azonos pozicióba sorolhatsz. A "-- A többit ellenzem--" fölött legyenek
	azok a listák amikre hajlandó vagy taktikai okokból szavazni (ha az általad favorizált listának nincs esélye az 5%-ra), alatta
  pedig azok amikre semmiképpen nem vagy hajlandó szavazni.</p>
	';
else
	echo '
	<p id="voksHelp">Rangsorold a jelölteket! A legjobbnak tartott kerüljön felülre. Egérrel huzhatod a sorokat,
	vagy használd a lenyiló menüt. Több jelöltet is azonos pozicióba sorolhatsz. A "-- A többit ellenzem--" fölött legyenek
	azok a jelöltek akikre hajlandó vagy taktikai okokból szavazni (ha ez adja a legjobb esélyt a kormány váltásra), alatta
  pedig azok akikre semmiképpen nem vagy hajlandó szavazni.</p>
	';

echo '
<table id="preftable" width="100%" border="1">
<thead><tr><th></th><th>Név</th><th></th><th>Pozició</th></tr></thead>
<tbody>';
// fontos, hogy ul-ben és a tr elemekben ne legyenek #text elemek!
$i = 0;
foreach ($item->alternativak as $res1) {
  if (substr($res1->nev,0,2) == '--')
	$tdstyle = "background-color:red; color:white";
  else
	$tdstyle="background-color:white; color:black";
  echo '<tr>';
  echo '<td style="width:30px; text-align:center"><button type="button" class="up">&uarr;</button></td>';	
  echo '<td id="jelolt'.$res1->id.'" style="'.$tdstyle.'; cursor:pointer"><var>'.$res1->nev.'</var>';
  // cimkék ABC sorrendben
  $db->setQuery('select t.id,t.title
  from #__contentitem_tag_map as tm, #__tags as t
  where t.id = tm.tag_id and tm.content_item_id='.$db->quote($res1->id).'
  order by title');
  $res2 = $db->loadObjectList();
  if (count($res2) > 0) {	
	  echo '<ul style="display:inline-block; margin:0px 0px 0px 5px; padding:0px;" class="inline logok">';
		foreach ($res2 as $res3) {
			echo '<li class="tag-'.$res3->id.' tag-list0" itemprop="keywords">&nbsp;'.$res3->title.'</li>';	
	  }
	  echo '</ul>';
  }	
  echo '</td>';
  echo '<td style="width:30px; text-align:center"><button type="button" class="down">&darr;</button>';
  echo '</td><td style="width:65px;"><select style="width:60px;" onchange="resort_row('.$i.')">';
  for ($j=1; $j<=count($item->alternativak); $j++) {
	  if ($j == 1)	
		echo '<option value="'.$j.'" selected="selected">'.$j.'</option>';
	  else	
	  	echo '<option value="'.$j.'">'.$j.'</option>';
  }
  echo '</select></td></tr>';
  $i++;		
}
echo '</tbody>
</table>
<input type="hidden" name="szavazat" value"" />
<center><button id="okBtn" type="button" class="btn btn-primary btn-ok">Szavazat beküldése</button>
<button type="button" onclick="location='."'$cancelUrl'".'" class="btn btn-cancel">Mégsem</button></center>
<div class="szavazashelp">
A választást megelőző harmincadik napig új jelöltek jelenhetnek meg. Jelöltek visszaléphetnek, a jelöltekről szóló infok változhatnak. Ezért javasljuk, hogy időnként látogass vissza ide, és ha új jelőlt jelent meg akkor ismételten szavazzál!
</div>
</form>
</div>
';
?>

