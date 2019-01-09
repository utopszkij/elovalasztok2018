<?php 
/** 
 * előválasztási rendszer konfuguráció
 * ===================================
 *       "votingEnable": lehet szavazni,
 *       "resultEnable": (rész)eredmény lekérdezhető,
 *       "proposalEnable": lehet javaslatot beküldeni,
 *       "supportEnable": lehet javaslatot támogatni,
 *       "canAssurance": szavazáshoz szükséges assurance,
 *       "supportAssurance": javaslat támogatáshoz szükséges assurance,
 *       "testMode": TESZT mód: bárki szavazhat, támogathat, többször is lehet szavazni,
 *       "proposals": a jelölt javaslatokat tartalmazó kategória,
 *       "requestedSupport": ennyi támogatás elérésekor automatikusan jelölté válik (ha 0 akkor nincs ilyen szabály),
 *       "requestedCandidateCount": a jelölési szakasz lezárulásakor a 
 *                     legtámogatotabb javaslatok közül ennyi válik jelölté
 * 
 * Jelölt javaslati szakasz start
 * ------------------------------
 * polDefs[#]->votingEnable = false
 * polDefs[#]->resultEnable = false
 * polDefs[#]->proposalEnable = true
 * polDefs[#]->supportEnable = true
 * 
 * jelölt javaslati szakasz vége
 * -----------------------------
 * polDefs[#]->votingEnable = false
 * polDefs[#]->resultEnable = false
 * polDefs[#]->proposalEnable = false
 * polDefs[#]->supportEnable = false
 * 
 * FUTTATNI: yourDomain/component/elovalasztok?task=supportEnd
 *    FIGYELEM FONTOS: csak egszer futtatható!!!!!!!!
 *    kodban ellenörizni: votingEnable == false és proposalEnable == false supportEnable == false
 *    valahol (pl kategori metadesc) megjegyezni, hogy futott már!
 *    
 * szavazás start
 * --------------
 * polDefs[#]->votingEnable = true
 * polDefs[#]->resultEnable = true
 * polDefs[#]->proposalEnable = false
 * polDefs[#]->supportEnable = false
 * 
 * szavazás vége
 * -------------
 * polDefs[#]->votingEnable = false
 * polDefs[#]->resultEnable = true
 * polDefs[#]->proposalEnable = true
 * polDefs[#]->supportEnable = true
 */
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
        "supportEnable":true,
        "proposalEnable":true,
        "canAssurance":"budapest",
        "supportAssurance":"budapest",
        "testMode":true,
        "proposals":11,
        "requestedSupport":0,
        "requestedCandidateCount":5
}');



?>
