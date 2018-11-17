<?php 
// elõválasztási rendszer konfuguráció
defined('_JEXEC') or die;
global $evConfig;

class EvConfig {
  public $szavazas = true; 
  public $eredmeny = true; 
  public $pollId = 10;     // joomla category id
  public $canAssurance = 'budapest'; // csak az szavazhat akinél az assurance listában szerepel ez a string
  public $testUzemmod = true;
  function __construct() {
  }
  
  public function userAdmin($user) {
	  return (($user->groups[8] == 8) | ($user->groups[10] == 10));
  }
}
$evConfig = new EvConfig();

?>
