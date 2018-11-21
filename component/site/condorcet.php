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
    function __construct($pollId=0) {
          $this->poll = $pollId;
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
        $this->getCandidates();
        $this->loadVoteCount();
        $this->loadInFirst();
        $this->loadDiffMatrix();
        $this->floydWarshall();
        $this->shortlist = $this->findWinner();

        return '<div class="condorcetResult">'."\n".
		  '<div class="condorcetWiner">'.$this->showResult($this->shortlist).'</div>'."\n".
        '<div class="condorcetDetails" id="eredmenyInfo" style="display:none">'."\n".
        '<div class="dMatrix"><h3>dMatrix</h3>'.$this->printMatrix($this->dMatrix).'</div>'."\n".
        '<div class="pMatrix"><h3>pMatrix</h3>'.$this->printMatrix($this->pMatrix).'</div>'."\n".
        '</div></div>'."\n";
      }

		/**
		* print cell of matrix
		* @param array of array matrix
		* @param int $id1
		* @param  int $id2
		* @return string  '<td.......</td>'
		*/ 
		protected function printMatrixCell($matrix, $id1, $id2) {
         if ($id1 == $id2) {
            $result = '<td align="center"> - </td>';
         } else {
           if ($matrix[$id1][$id2] > $matrix[$id2][$id1]) {
              $class = 'green';
           } else if ($matrix[$id1][$id2] < $matrix[$id2][$id1]) {
              $class = 'red';
		  		  $this->condorcetWinner[$id1] = false;
           } else {
              $class = 'white';
           }   
           $result = '<td align="center" class="'.$class.'">'.$matrix[$id1][$id2].'</td>';
         } 
         return $result;   
		}            	

		/**
		* echo row of matrix
		* @param int number of row
		* @param arrray of array matrix
		* @param int $id1
		* @param string $name1
		* @return string <tr.......</tr>'
		*/
      protected function printMatrixRow($r, $matrix, $id1, $name1) {
        $result = "<tr><th>$r</th><td>$name1</td>";
        foreach($this->candidates as $id2 => $name2) {
            if(array_key_exists($id1,$matrix) && array_key_exists($id2,$matrix[$id1])) {
					$result .= $this->printMatrixCell($matrix, $id1, $id2);            	
            } else {
               $result .= '<td align="center"> - </td>';
            }
        }
        return $result."</tr>\n";
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
          for ($c=0; $c < count($this->candidates); $c++) {
				$result .= '<th>'.$c.'</th>';          
          }	
          $result .= "</tr>";
          $r = 1;
          foreach($this->candidates as $id1 => $name1) {
          	  $result .= $this->printMatrixRow($r, $matrix, $id1, $name1);
              $r++;
          }
          return $result.'</table>'."\n";
      }

		/**
		* Shulze method step 2.2
		*/
      protected function flowdWarshall2() {
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
      * Shulze method step 2.
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
          $this->flowdWarshall2();
      }

      // support function for sort
      protected function beatsP($id1,$id2) {
          return  ($this->pMatrix[$id2][$id1] - $this->pMatrix[$id1][$id2]);
      }

		/**
		* cretae resultTable HTML
		* @param array of candidatesId
		* @param array
		* @param array
		* @return string <table....../table>'
		*/
		protected function showResultTable($shortlist, $values, $accepted) {
          $result =  '<table class="pollResult" border="1" width="100%">
                     <tr><th>Condorcet<br />helyezés</th><th>Név</th><th>Első helyen szerepel</th><th>Elfogadható</th></tr>'."\n";
		    $pozition = 0;
		    $trClass = 'eredmenySor';	
          foreach($shortlist as $j => $i) {
					 if (($i < count($this->inFirst)) &&  					 
						  (($this->inFirst[$i] == '')  |
							($this->inFirst[$i] == null) |
							($this->inFirst[$i] < 0)
						  )) { 
					 	$this->inFirst[$i] = 0;
					 }	
			       if ($j == 0) {
				           $pozition = 1;
			       } else if (($values[$i] < $values[$shortlist[$j-1]]) && (substr($this->candidates[$i],0,2) != '--')) {
				           $pozition++;
				    }       
			       $info = '';
			       if (($this->condorcetWinner1) & ($j==0)) {
				          $info .= '&nbsp;-&nbsp;<strong style="color:orange">Condorcet gyöztes</strong>';
			       }
			       if (($j > 0) && ($values[$i] === $values[$shortlist[$j-1]])) {
					        $info .= 'döntetlen';
			       }   
					 if (substr($this->candidates[$i],0,2) == '--') {
						$trClass = 'eredmenySorEllenzett';
                        $result .= '<tr class="'.$trClass.'"><td colspan="4"><var class="noAccept">'.$this->candidates[$i].'</var></td></tr>';
					 } else {
                        $result .= '<tr class="'.$trClass.'">'.
                        '<td class="pozicio">'.$pozition.'</td>'.
			            '<td class="nev">'.$this->candidates[$i].' '.$info.'</td>'.
					    '<td width="100">&nbsp;'.$this->inFirst[$i].'&nbsp;&nbsp;&nbsp;'.Round($this->inFirst[$i] * 100 / $this->vote_count).'%</td>'.
					    '<td width="100">&nbsp;'.$accepted[$i].'&nbsp;&nbsp;&nbsp;'.Round($accepted[$i] * 100 / $this->vote_count).'%</td></tr>	
					    ';
                }
          }
          return $result."</table>\n";
	  }          
		
	  /**
	  * find notAcceped line in $this->candidates
	  * @return int
	  */	
     protected function findNotAccepted() {    
          $notAccept = 0;  
          foreach ($this->candidates as $i => $name) {
            if (substr($name,0,2) == '--') {
            	$notAccept = $i;
            }	
          }
          return $notAccept;  
     }   
     
		          
      /**
      * compute accepted numbers for candidates
      * @param int notAccepted candidates.ID
      * @return array
      */
      protected function computeAccepteds($notAccept) {    
          $accepted = array();  
          foreach ($this->candidates as $i => $name) {
            $accepted[$i] = $this->dMatrix[$i][$notAccept];
          }
          return $accepted;            
      }    

     /**
     * compute Condorcet result values for candidates
     * @param array  shortlist
     * @return array values
     */
     protected function computeValues($shortlist) {    
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
         return $values;
     }     

		/**
		*  resort $this->candidates and $accepted by $shortlist
		* @param array $shortlist
		* @param array $accepted
		* @return void
		*/
		protected function showResultResort($shortlist, &$accepted) {
		    $w = array();
		    $w1 = array();
		    foreach ($shortlist as $i) {
				$w[$i] = $this->candidates[$i];
				$w1[$i] = $accepted[$i];
		    }
		    $this->candidates = $w;
		    $accepted = $w1;
		    return;
		}

		/**
		*  check first is condorcet winner?
		* @param array $shortlist
		* @return void   set $this->condorcetWinner1
		*/
		protected function showResultCheckCondorcetWinner($shortlist) {	    
		    $i = $shortlist[0]; 
		    $this->condorcetWinner1 = true;
		    foreach  ($this->candidates as $j => $name) {
				if ($this->dMatrix[$i][$j] < $this->dMatrix[$j][$i]) {
					$this->condorcetWinner1 = false;
				}	
		    }
		    return;
		}	    

	  /**
     * create condorcet result html  
	  * @param array value: candidate.id
     * @return string HTML string
	  */ 
      protected function showResult($shortlist) {
		  if ($this->vote_count == 0) {
				$result = '<p class="nincsSzavazat">Nincs egyetlen szavazat sem.</p>';
		  } else  if (count($shortlist) == 0) {
            $result = '';
        } else { 
		  		$values = $this->computeValues($shortlist);	        
		  		$notAccepted = $this->findNotAccepted();	
		  		$accepted = $this->computeAccepteds($notAccepted);
		  		$this->showResultResort($shortlist,$accepted);	
		  		$this->showResultCheckCondorcetWinner($shortlist);
				$result = $this->showResultTable($shortlist, $values, $accepted);
		  }	
		  return $result.'<p class="szavazatokSzama">Szavazatok száma:<var>'.$this->vote_count.'</var></p>'."\n";
      }

      /**
      * calculate condorcet sort
      * @return array // condorcet ID -k
      */    
      protected function findWinner() {
          $short_list = array_keys($this->candidates);
          usort($short_list,array('Condorcet','beatsP'));
          return $short_list;
      }

}

?>
