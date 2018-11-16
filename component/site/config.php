<?php 
// el�v�laszt�si rendszer konfugur�ci�
defined('_JEXEC') or die;
global $evConfig;

class EvConfig {
  public $szavazas = true; 
  public $eredmeny = true; 
  public $pollId = 10;     // joomla category id
  public $canAssurance = 'budapest'; // csak az szavazhat akin�l az assurance list�ban szerepel ez a string
  public $fordulo = 0; // tov�bbfejleszt�sre, esetleges t�bb fordul�s v�laszt�sokhoz
  public $testUzemmod = true;
  function __construct() {
  }
  
  public function userAdmin($user) {
	  return (($user->groups[8] == 8) | ($user->groups[10] == 10));
  }
}
$evConfig = new EvConfig();

?>
