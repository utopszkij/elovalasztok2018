<?php 
// elõválasztási rendszer konfuguráció
defined('_JEXEC') or die;

class EvConfig {
  public $belsoSzavazasok = array(123,124,125,126,127,128);	 // categories
  public $oevkSzavazasok = array();  // categories
  public $orszagosListaSzavazasok = array(155);  // categories
  public $miniszterElnokSzavazasok = array(156); // categories
  public $probaSzavazas = 9;
  public $jeloltAdd = true; // enabled
  public $jeloltEdit = true; // enabled
  public $jeloltDelete = true; // enabled
  public $szavazas = true; // enabled
  public $szavazatDelete = true; // enabled
  public $szavazatEdit = true; // enabled
  public $eredmeny = true; // enabled
  public $fordulo = 0;  // esetleges több fordulós választásokhoz 
  public $canAssurance = false; // csak az szavazhat akinál az assurance listában szerepel a szavazás->title
  
  function __construct() {
	  for ($i=9; $i<=154; $i++) {
		  if (in_array($i, $this->belsoSzavazasok) == false) $this->oevkSzavazasok[] = $i;
	  }
  }
  
  public function userAdmin($user) {
	  return (($user->groups[8] == 8) | ($user->groups[10] == 10));
  }
}
global $evConfig;
$evConfig = new EvConfig();

?>
