<?php 
// előválasztási rendszer konfuguráció
defined('_JEXEC') or die;
global $evConfig;

// default pollId = 10
$evConfig = JSON_decode('{
    "pollId":10, 
    "pollDefs":[]
}');

//  config for pollId=10
$evConfig->pollDefs[10] = JSON_decode('{
        "votingEnable":true,
        "resultEnable":true,
        "canAssurance":"budapest",
        "supportEnable":true,
        "proposalEnable":true,
        "testMode":true,
        "proposals":11,
        "supportAssurance":"budapest",
        "requestedSupport":500
}');



?>
