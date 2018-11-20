<?php 
// elõválasztási rendszer konfuguráció
defined('_JEXEC') or die;
global $evConfig;

// default pollId and configs by polls
$evConfig = JSON_decode('{
    "pollId":10,
    "pollDefs":[]
}');

//  config for pollId=10
$evConfig->pollDefs[10] = JSON_decode('{
        "szavazas":true,
        "eredmeny":true,
        "canAssurance":"budapest",
        "testUzemmod":true,

        "votingEnable":true,
        "resultEnable":true,
        "requiredUserParams":"budapest",
        "testMode":true
}');

?>
