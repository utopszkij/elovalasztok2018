<?php 
// el�v�laszt�si rendszer konfugur�ci�
defined('_JEXEC') or die;
global $evConfig;
$evConfig = new stdClass();
$evConfig->szavazas = true; // jelenleg lehet szavazni
$evConfig->eredmeny = true; // jelenleg lehet eredm�nytlek�rdezni
$evConfig->pollId = 10;     // joomla category id
$evConfig->canAssurance = 'budapest'; // csak az szavazhat akin�l az assurance list�ban szerepel ez a string (lehet �res is)
$evConfig->testUzemmod = true; // teszt �zemmodban, b�rki szavazhat, ak�r t�bbsz�r is

?>
