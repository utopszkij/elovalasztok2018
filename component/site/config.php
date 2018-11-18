<?php 
// elõválasztási rendszer konfuguráció
defined('_JEXEC') or die;
global $evConfig;
$evConfig = new stdClass();
$evConfig->szavazas = true; // jelenleg lehet szavazni
$evConfig->eredmeny = true; // jelenleg lehet eredménytlekérdezni
$evConfig->pollId = 10;     // joomla category id
$evConfig->canAssurance = 'budapest'; // csak az szavazhat akinél az assurance listában szerepel ez a string (lehet üres is)
$evConfig->testUzemmod = true; // teszt üzemmodban, bárki szavazhat, akár többször is

?>
