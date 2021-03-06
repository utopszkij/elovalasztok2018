<?php
// szavazás leadása képernyő
// be: $item {pollId, pollName, pollOptions:[{id,name},....], $user

defined('_JEXEC') or die;

$db = JFactory::getDBO();
$cancelUrl = JURI::root().'leiras';
echo '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="'.JURI::root().'components/com_elovalasztok/views/vote.js" type="text/javascript"></script>
';
   
echo '<h2>'.$item->pollName.'</h2>
<div id="divTurelem" style="display:none; background-color:transparent; cursor:default;"></div>
<form method="post" action="'.JURI::root().'index.php?option=com_elovalasztok" name="szavazatForm" id="szavazatForm">
<input type="hidden" name="view" value="application" />
<input type="hidden" name="fileid" value="4"/>
<input type="hidden" name="task" value="szavazatSave" /> 
<input type="hidden" name="nick" value="'.$user->username.'" />
<input type="hidden" name="pollId" value="'.$item->pollId.'" />
'.JHtml::_('form.token').'
';
if (count($item->pollOptions)==0) {
  echo '
  <div class="msg">Nincs egyetlen jelölt sem</div>';
  echo '<center><button type="button" onclick="location='."'$cancelUrl'".'" class="btnCancel">Vissza</button></center>';
  return;
}

echo '<div>
';


echo '
	<p id="voksHelp">Rangsorold a jelölteket! A legjobbnak tartott kerüljön felülre. Egérrel huzhatod a sorokat,
	vagy használd a lenyiló menüt. Több jelöltet is azonos pozicióba sorolhatsz. A "-- A többit ellenzem--" fölött legyenek
	azok a jelöltek akikre hajlandó vagy taktikai okokból szavazni, alatta
  pedig azok akikre semmiképpen nem vagy hajlandó szavazni.Egy ember többször is szavazhat, 
  ilyenkor az utolsó leadott szavazatát vesszük figyelembe.</p>
	';

echo '
<table id="preftable" width="100%" border="1">
<thead><tr><th></th><th>Név</th><th></th><th>Pozició</th></tr></thead>
<tbody>';
// fontos, hogy ul-ben és a tr elemekben ne legyenek #text elemek!
$i = 0;
foreach ($item->pollOptions as $res1) {
  if (substr($res1->name,0,2) == '--') {
	$tdstyle = "background-color:red; color:white";
  } else {
	$tdstyle="background-color:white; color:black";
  }	
  echo '<tr>';
  echo '<td style="width:30px; text-align:center"><button type="button" class="up">&uarr;</button></td>';	
  echo '<td id="jelolt'.$res1->id.'" style="'.$tdstyle.'; cursor:pointer"><var>'.$res1->name.'</var></td>';
  echo '<td style="width:30px; text-align:center"><button type="button" class="down">&darr;</button>';
  echo '</td><td style="width:65px;"><select style="width:60px;" onchange="resort_row('.$i.')">';
  for ($j=1; $j<=count($item->pollOptions); $j++) {
	  if ($j == 1)	{
		echo '<option value="'.$j.'" selected="selected">'.$j.'</option>';
	  } else {	
	  	echo '<option value="'.$j.'">'.$j.'</option>';
	  }	
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
</div>
</form>
</div>
';
?>

