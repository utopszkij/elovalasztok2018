<?php

/*
  Schulze method implementation based on http://en.wikipedia.org/wiki/Schulze_method
  The test cases are from http://wiki.electorama.com/wiki/Schulze_method
  GNU GPL v3 or later
  (c) Árpád Magosányi 2013,  Fogler Tibor 2018
*/

defined('_JEXEC') or die;

/**
* A Schulze method implementation
*/
class Condorcet {
    protected $poll = null;  // poll id
    protected $candidates = array(); // key: candidate.id, value:candidate.name    
    protected $condorcetWinner = array();  // key: candidate.id, value: true|false
    protected $dMatrix = null;
    protected $pMatrix = null;
    public  $vote_count = 0;
	protected $inFirst = array();   // key: candidate.id, value:number    
    protected $shortlist = array(); // value: candidate.id

    /**
	* @param integer Szavazás ID
	*/
    function __construct($szavazas_id=0) {
          $this->poll = $szavazas_id;
    }

    // ======================================
    // abstract metods
    // ======================================

     /**
      * database --> $this->candidates
      * @output $this->candidates 
      */
      protected function getCandidates() {
          return $this->candidates;
      }

      /**
      * database --> $this->dmatrix
      * @return $dMatrix   dMatrix[i,j]  The 'i' candidate will prematurely precede the "j" candidate
      * where "i" and "j" candidate.id
      */
      protected function loadDiffMatrix() {
          return $this->dMatrix;
      }

      /**
      * database --> $this->inFirst
      * @return array 
      */    
      protected function loadInFirst() {
        return $this->inFirst;
      }  

	  /**
      * database --> $this->vote_count
      * @return integer
      */  
      protected function loadVoteCount() {  
        return $this->vote_count;
      }  
      
  
      // ==================================
      // standart methods
      // ==================================  

      /**
      * Full processs
      */
      public function report() {
      	$result = '';
        $this->getCandidates();
        $this->loadVoteCount();
        $this->loadInFirst();
        $this->loadDiffMatrix();
        $this->floydWarshall();
        $this->shortlist = $this->findWinner();

        $result .= '<div class="condorcetResult">'."\n";
		$result .= '<div class="condorcetWiner">'.$this->showResult($this->shortlist).'</div>'."\n";
        $result .= '<div class="condorcetDetails" id="eredmenyInfo" style="display:none">'."\n";
        $result .= '<div class="dMatrix"><h3>dMatrix</h3>'.$this->printMatrix($this->dMatrix).'</div>'."\n";
        $result .= '<div class="pMatrix"><h3>pMatrix</h3>'.$this->printMatrix($this->pMatrix).'</div>'."\n";
        $result .= '</div></div>'."\n";
        return $result;
      }
 
      /**
      * matrix --> html short by $this->candidates 
      * and set $this->condorcetWinner[$i]
      * @param matrix
      * @return string 
      */
      protected function printMatrix($matrix) {
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
						  $this->condorcetWinner[$id1] = false;
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
      * Shulze method
      * $this->dMatrix -> $this->pMatrix
      * @return $this->pMatrix
      * use $this->candidates, $this->dMatrix
      */
      protected function floydWarshall() {
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

      // support function for sort
      protected function beatsP($id1,$id2) {
          $result = $this->pMatrix[$id2][$id1] - $this->pMatrix[$id1][$id2];
          return $result;
      }

	  /**
      * create condorcet result html  
	  *@param array value: candidate.id
      *@return string HTML string
	  */ 
      protected function showResult($shortlist) {
		  if ($this->vote_count == 0) {
				$result = '<p class="nincsSzavazat">Nincs egyetlen szavazat sem.</p>';
				return $result;
		  }
		  $result = '';
          if (count($shortlist) == 0) {
                return '';
          }  
          // compute result values
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

          // find "notAccepted" candidate
          $notAccept = 0;  
          foreach ($this->candidates as $i => $name) {
            if (substr($name,0,2) == '--') $notAccept = $i;
          }  
          // compute accepted numbers
          $accepted = array();  
          foreach ($this->candidates as $i => $name) {
            $accepted[$i] = $this->dMatrix[$i][$notAccept];
          }            

		  //  resort $this->candidates and $this->vonalFelett by $shortlist
		  $w = array();
		  $w1 = array();
		  foreach ($shortlist as $i) {
				$w[$i] = $this->candidates[$i];
				$w1[$i] = $accepted[$i];
		  }
		  $this->candidates = $w;
		  $accepted = $w1;

		  // check first is condorcet winner?
		  $i = $this->shortlist[0]; 
		  $this->condorcetWinner1 = true;
		  foreach  ($this->candidates as $j => $name) {
				if ($this->dMatrix[$i][$j] < $this->dMatrix[$j][$i]) $this->condorcetWinner1 = false;
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
			         if ($j == 0)
				           $pozition = 1;
			         else if (($values[$i] < $values[$shortlist[$j-1]]) && (substr($this->candidates[$i],0,2) != '--'))
				           $pozition++;
			         $info = '';
			         if (($this->condorcetWinner1) & ($j==0)) {
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
                        $result .= '<tr class="'.$trClass.'"><td class="pozicio">'.$pozition.'</td>
			            <td class="nev">
						     '.$this->candidates[$i].' '.$info.'
					    </td>
					    <td width="100">&nbsp;'.$this->inFirst[$i].'&nbsp;&nbsp;&nbsp;'.Round($this->inFirst[$i] * 100 / $this->vote_count).'%</td>
					    <td width="100">&nbsp;'.$accepted[$i].'&nbsp;&nbsp;&nbsp;'.Round($accepted[$i] * 100 / $this->vote_count).'%</td>	
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
      * calculate condorcet sort
      * @return array // condorcet ID -k
      */    
      protected function findWinner() {
          $shortlist = array_keys($this->candidates);
          $newlist = usort($shortlist,array('Condorcet','beatsP'));
          return $shortlist;
      }

}

?>
