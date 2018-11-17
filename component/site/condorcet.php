<?php

/*
  Schulze method implementation based on http://en.wikipedia.org/wiki/Schulze_method
  The test cases are from http://wiki.electorama.com/wiki/Schulze_method
  GNU GPL v3 or later
  (c) Árpád Magosányi 2013

  tábla és mező nevek módositása az elovalsztok2018 adatbázishoz 2016.09.18. Fogler Tibor


  FT 2015.02.13  az 123 / 321 szavazatokat a condorcet - schulze módszer döntetlenre hozza
  ezért az eljárás ki lett bővitve az "elfogadhatóság" kezelésével.
  "elfogadható" egy alternativa" ha a szavazó a lehetséges poziciók első 2/3 -ba helyezte el.
  Condorcet döntetlen esetén az az alternativa kerül előre emelyiket többen tartottak
  "elfogadható"-nak.
*/
defined('_JEXEC') or die;
/**
* A Schulze method implementation
*/
class Condorcet {
	public $szavazatTable = '#__szavazatok'; // assurance szürésnél nem a táblát hanem ennek egy szükitett view-jét kell használni.
    private $poll = null;  // szavazas_id
    private $candidates = array(); // key: jelolt.id, value:jelölt neve    
    private $condorcetGyoztes = array();  // condorcet gyöztes
    private $dMatrix = null;
    private $pMatrix = null;
    public $vote_count = 0;
    private $accpted = array();  // key: jelolt.id, value:jelölt neve    
	private $inFirst = array();  // key: jelolt.id, value:jelölt neve    
    private $db = null; // database kezelő objektum
    private $shortlist = null;

    /**
	* @param JDatabase
	* @param integer Témakör ID
	* @param integer Szavazás ID
	* @param string SQL nyelven megirt filter
	* @param integer fordulo
	*/
    function __construct($szavazas_id=0) {
          $this->db = JFactory::getDBO();
          $this->poll = $szavazas_id;
    }

      /**
      * Teljes feldolgozás, és eredmény html tábla generálás
      *
      */
      public function report() {
      	$result = '';
        
        // condorcet feldolgozás
        $this->getCandidates();
        $this->loadVoteCount();
        $this->loadInFirst();
        $this->loadDiffMatrix();
        $this->floydWarshall();
        $this->shortlist = $this->findWinner();

        // eredmény html létrehozása
        $result = '<div class="condorcetResult">'."\n";
		$result .= '<div class="condorcetWiner">'.$this->showResult($this->shortlist).'</div>'."\n";
        $result .= '<div class="condorcetDetails" id="eredmenyInfo" style="display:none">'."\n";
        $result .= '<div class="dMatrix"><h3>dMatrix</h3>'.$this->printMatrix($this->dMatrix).'</div>'."\n";
        $result .= '<div class="pMatrix"><h3>pMatrix</h3>'.$this->printMatrix($this->pMatrix).'</div>'."\n";
        $result .= '</div></div>'."\n";
        return $result;
      }

      /**
      * Jelölt lista beolvasása az adatbázisból
      * @output $this->candidates
      */
      private function getCandidates() {
          $candidates_sql = "select title AS megnevezes,id
		      from #__content
		      where  catid=".$this->poll.' and state=1';
          $db = $this->db;
          $db->setQuery($candidates_sql);
          $this->candidates=array();
		      $this->condorcetGyoztes = array();
          foreach($db->loadObjectList() as $row) {
              $this->candidates[$row->id] = $row->megnevezes;
          }
          return $this->candidates;
      }

      /**
      * a paraméterben kapott mátrix kiirása html kodként
      * a kiirás sorrendjét a $this->candidates sorrendje vezérli
      * beállítja a $this->condorcetGyoztes[$i] értékét is
      * @param matrix
      * @return string html kód
      */
      private function printMatrix($matrix) {
          $result= '
          <table border="1" cellpadding="4" class="pollResult" width="100%">
          <tr><th>&nbsp;</th><th>&nbsp;</th>
          ';
          $c=1;
          foreach($this->candidates as $id => $name) {
              $result .= "<th>$c</th>";
              $c++;
          }
          $result .= "</tr>";
          $r = 1;
          foreach($this->candidates as $id1 => $name1) {
              $result .= "<tr><th>$r</th><td>$name1</td>";
              foreach($this->candidates as $id2 => $name2) {
                  if(array_key_exists($id1,$matrix) && array_key_exists($id2,$matrix[$id1])) {
                     if ($id1 == $id2)
                        $result .= '<td align="center"> - </td>';
                     else {
                       if ($matrix[$id1][$id2] > $matrix[$id2][$id1])
                          $class = 'green';
                       else if ($matrix[$id1][$id2] < $matrix[$id2][$id1]) {
                          $class = 'red';
						  $this->condorcetGyoztes[$id1] = false;
                       } else
                          $class = 'white';
                       $result .= '<td align="center" class="'.$class.'">'.$matrix[$id1][$id2].'</td>';
                      }    
                  } else {
                    $result .= '<td align="center"> - </td>';
                  }
              }
              $result .= "</tr>\n";
              $r++;
          }
          $result .= '</table>
          ';
          return $result;
      }

      /**
      * a feldolgozási Shulze method
      * $this->dMatrix -> $this->pMatrix
      * @return $this->pMatrix
      * use $this->candidates, $this->dMatrix
      */
      private function floydWarshall() {
          $this->pMatrix = array();
          foreach($this->candidates as $i => $name1) {
              $this->pMatrix[$i] = array();
              foreach($this->candidates as $j => $name2) {
                  if($i != $j) {
                    if($this->dMatrix[$i][$j] > $this->dMatrix[$j][$i]) {
                      $this->pMatrix[$i][$j] = $this->dMatrix[$i][$j] ;
                    } else {
                      $this->pMatrix[$i][$j] = 0;
                    }
                  }
              }
          }

          /*
            Minden "i","j" párhoz a lehetséges "j" előzi "i"-t, "i" előzi "k"-t
            lehetséges hármas sorrendek közül
            kiválasztja a legnagyobb támogatottságut ezt irja be a [j][k] -ba

            "j" előzi "i"-t, "i" előzi "k" -t lehetséges hármasok közül
            a leginkább támogatott kerül [j][k] -ba
          */
          foreach($this->candidates as $i => $name1) {
              foreach($this->candidates as $j => $name2) {
                  if($i != $j) {
                    foreach($this->candidates as $k => $name3) {
                        if(($i != $k) && ($j != $k)) {
                          $this->pMatrix[$j][$k] = max($this->pMatrix[$j][$k], min ($this->pMatrix[$j][$i],$this->pMatrix[$i][$k]));
                        }
                    }
                  }
              }
          }
      }

    /**
    * A feldolgozási eljárás első lépése
    * $this->dmatrix képzése az adatbázisból
    * @return $dMatrix   dMatrix[i,j]  'i' jelölt ennyiszer elözi "j" jelöltet
    */
    private function loadDiffMatrix() {
          $diff_sql = "select a.alternativa_id as id1, b.alternativa_id as id2, count(a.id) as d
                       from ".$this->szavazatTable." a,
                            ".$this->szavazatTable." b
                       where  a.szavazas_id=".$this->poll." and
                             b.szavazas_id=a.szavazas_id and
                             a.szavazo_id=b.szavazo_id and
                             a.pozicio < b.pozicio 
                       group by a.alternativa_id, b.alternativa_id";
          $this->db->setQuery($diff_sql);
          $this->dMatrix=array();
		  $rows = $this->db->loadObjectList();
          foreach($rows as $row ) {
              $id1 = $row->id1;
              $id2 = $row->id2;
              $d = $row->d;
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              $this->dMatrix[$id1][$id2] = $d;
          }
          foreach($this->candidates as $id1 => $name1) {
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              foreach($this->candidates as $id2 => $name2) {
                  if(!array_key_exists($id2,$this->dMatrix[$id1])) {
                      $this->dMatrix[$id1][$id2] = 0;
                  }
              }
          }
          return $this->dMatrix;
      }

      /**
      * kigyüjti az adatbázisból, hogy az egyes jelölteket hányan sorolták első helyre
      * @return array //key: jelölt ID, value: true|false
      */    
      private function loadInFirst() {
        foreach($this->candidates as $id1 => $name1) {
            $this->inFirst[$id1] = 0;        
        }
        $this->db->setQuery('select a.alternativa_id, count(a.szavazo_id) cc
        from #__szavazatok a
        where a.szavazas_id = '.$this->db->quote($this->poll).' and a.pozicio = 1 
        group by a.alternativa_id
        ');
        $res = $this->db->loadObjectList();
        foreach ($res as $row) {
            $this->inFirst[$row->alternativa_id] = $row->cc;
        }
        return $this->inFirst;
      }  

	  /**
      * szavazat szám lekérdezése
      * @return integer
      */  
      private function loadVoteCount() {  
   	    $this->db->setQuery('select count(DISTINCT a.szavazo_id) cc
 		from #__szavazatok a
		left outer join #__content c2 on c2.id = a.alternativa_id
    	where c2.state = 1 and a.szavazas_id = '.$this->db->quote($this->poll));		
		$res = $this->db->loadObject();
   	    $this->vote_count = $res->cc;
        return $this->vote_count;
      }  

      // rendezéshez compare rutin
      private function beatsP($id1,$id2) {
          $result = $this->pMatrix[$id2][$id1] - $this->pMatrix[$id1][$id2];
          return $result;
      }

	  /**
	  *@param array a candidates.id -ket tartalmazza az eredménynek megfelelő sorrendben
      * @return string HTML string
	  */ 
      private function showResult($shortlist) {
          $db = JFactory::getDBO();
          $szavazas_id = $this->poll;
			 $result = '';

          // eredmény értékek számolása
          $values = array();
          $i = 0;
          $id1 = 0;
          $id2 = 0;
          $i = count($shortlist) - 1;
          $values[$shortlist[$i]] = 0;
          $lastValue = 0;
          for ($i=count($shortlist) - 2; $i >=0; $i--) {
            $id1 = $shortlist[$i];
            $id2 = $shortlist[$i+1];
            $values[$shortlist[$i]] = $lastValue + $this->pMatrix[$id1][$id2] - $this->pMatrix[$id2][$id1];
            $lastValue = $values[$shortlist[$i]];
          }

          // melyik a"többit ellenzem" lehetőség?
          $vonal = 0;  
          foreach ($this->candidates as $i => $name) {
            if (substr($name,0,2) == '--') $vonal = $i;
          }  
          // az egyes jelölteket hányan sorolták a "vonal" fölé?
          foreach ($this->candidates as $i => $name) {
            $vonalFelett[$i] = $this->dMatrix[$i][$vonal];
          }            

		  //+ 2017.02.24, 2017.12.01  most a végeredménynek megfelelően át kell
		  //  rendezni a $this->candidates és a $vonalFelett táblázatokat
		  //  sorrend: shortlist[0], shortlist[1]... ezek az értékek a candidates
		  //  tábla indexei
		  $w = array();
		  $w1 = array();
		  foreach ($shortlist as $i) {
				$w[$i] = $this->candidates[$i];
				$w1[$i] = $vonalFelett[$i];
		  }
		  $this->candidates = $w;
			$vonalFelett = $w1;

		  // az első helyzett condorcet gyöztes?
		  $i = $this->shortlist[0]; // első helyezett canidates->id
		  $this->condorcetGyoztes1 = true;
		  foreach  ($this->candidates as $j => $name) {
				if ($this->dMatrix[$i][$j] < $this->dMatrix[$j][$i]) $this->condorcetGyoztes1 = false;
		  }

		  if ($this->vote_count == 0) {
				// echo '<p>Nincs egyetlen szavazat sem.</p>';
				return;
		  }

          $result .=  '<table class="pollResult" border="1" width="100%">
                     <tr><th>Condorcet<br />helyezés</th><th>Név</th><th>Első helyen szerepel</th><th>Elfogadható</th></tr>'."\n";
		  $helyezes = 0;
		  $trClass = 'eredmenySor';	
          foreach($shortlist as $j => $i) {
					 if ($i < count($this->inFirst)) { 					 
						 if (($this->inFirst[$i] == '')  |
							  ($this->inFirst[$i] == null) |
							  ($this->inFirst[$i] < 0)
							 ) $this->inFirst[$i] = 0;
					 }	 
					 // condorcet gyöztes?
			         if ($j == 0)
				           $helyezes = 1;
			         else if (($values[$i] < $values[$shortlist[$j-1]]) && (substr($this->candidates[$i],0,2) != '--'))
				           $helyezes++;
			         $info = '';
			         if (($this->condorcetGyoztes1) & ($j==0)) {
				          $info .= '&nbsp;-&nbsp;<strong style="color:orange">Condorcet gyöztes</strong>';
			         }
			         if ($j > 0) {
				        if ($values[$i] === $values[$shortlist[$j-1]])
					        $info .= 'döntetlen';
			         }
					 if (substr($this->candidates[$i],0,2) == '--') {
						$trClass = 'eredmenySorEllenzett';
                        $result .= '<tr class="'.$trClass.'"><td colspan="4"><var class="noAccept">'.$this->candidates[$i].'</var></td></tr>';
					 } else {
                        $result .= '<tr class="'.$trClass.'"><td class="pozicio">'.$helyezes.'</td>
			            <td class="nev">
						     '.$this->candidates[$i].' '.$info.'
					    </td>
					    <td width="100">&nbsp;'.$this->inFirst[$i].'&nbsp;&nbsp;&nbsp;'.Round($this->inFirst[$i] * 100 / $this->vote_count).'%</td>
					    <td width="100">&nbsp;'.$vonalFelett[$i].'&nbsp;&nbsp;&nbsp;'.Round($vonalFelett[$i] * 100 / $this->vote_count).'%</td>	
					    </tr>
					    ';
                    }
          }
          $result .= "</table>\n";
    	  $result .= '<p class="szavazatokSzama">Szavazatok száma:<var>'.$this->vote_count.'</var></p>
    		  	';
      return $result;
      }

      /**
      * condorces sorrend képzése
      * @return array // jelölt ID -k
      */    
      private function findWinner() {
          $shortlist = array_keys($this->candidates);
          $newlist = usort($shortlist,array('Condorcet','beatsP'));
          return $shortlist;
      }

}

?>
