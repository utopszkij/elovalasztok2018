<?php 
// elõválasztási rendszer konfuguráció
defined('_JEXEC') or die;
global $evConfig;

class EvConfig {
  public $szavazas = true; 
  public $eredmeny = true; 
  public $pollId = 10;     // joomla category id
  public $canAssurance = 'budapest'; // csak az szavazhat akinál az assurance listában szerepel ez a string
  public $fordulo = 0; // továbbfejlesztésre, esetleges több fordulós választásokhoz
  public $testUzemmod = true;
  function __construct() {
  }
  
  public function userAdmin($user) {
	  return (($user->groups[8] == 8) | ($user->groups[10] == 10));
  }
}
$evConfig = new EvConfig();

?>
