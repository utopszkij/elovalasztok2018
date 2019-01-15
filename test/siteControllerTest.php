<?php
global $evConfig;
if (file_exists('../../configuration.php')) {
    include_once '../../configuration.php';
    $jconfig = new JConfig();
    define('TESTDB',$jconfig->db);
    define('TESTDBUSER',$jconfig->user);
    define('TESTDBPSW',$jconfig->password);
} else {
    define('TESTDB','test');
    define('TESTDBUSER','root');
    define('TESTDBPSW','');
}
define('TESTDBPRE','tst_');
define('UNITTEST',true);
include_once "./testJoomlaFramework.php";
$session = JFactory::getSession();
$session->set('cookie_enable',1);
include_once "../component/site/config.php";
include_once "../component/site/elovalasztok.php";

class elovalasztokControllerTest extends PHPUnit_Framework_TestCase {
    function __construct() {
		parent::__construct();
		  global $evConfig;	
        // init database
        $db = JFactory::getDBO();
        $db->exec('DROP TABLE IF EXISTS #__categories');
        $db->exec('DROP TABLE IF EXISTS #__content');
        $db->exec('DROP TABLE IF EXISTS #__users');
        $db->exec('CREATE TABLE #__categories(
            id int(11) AUTO_INCREMENT,
            title varchar(128),
            parent_id int(11),
            description varchar(128),
            state int(1),
            PRIMARY KEY (id)
        )');
        $db->exec('CREATE TABLE #__content(
            id int(11) AUTO_INCREMENT,
            title varchar(128),
            alias varchar(128),
            catid int(11),
            introtext text,
            `fulltext` text,
            state int(1),
            `language` varchar(12),
            access varchar(12),
            created_by int(11),
            created_by_alias varchar(32),
            PRIMARY KEY (id)
        )');
        $db->exec('CREATE TABLE #__supports(
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `proposal_id` int(11) NOT NULL ,
            `user_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `proposali` (`proposal_id`),
            KEY `useri` (`user_id`)
        )');
        $db->exec('CREATE TABLE #__users(
            id int(11) AUTO_INCREMENT,
            username varchar(128),
            params varchar(128),
            PRIMARY KEY (id)
        )');
        $db->exec('INSERT INTO #__categories VALUES (0,"szavazas",0,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (0,"jelolt1",1,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (0,"jelolt2",1,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (0,"jelolt3",1,"",1)');
        $db->exec('INSERT INTO #__users VALUES (0,"user1","ADA:magyar,budapest")');
        $db->exec('INSERT INTO #__users VALUES (0,"user2","ADA:magyar")');
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1] = new stdClass();	
        $evConfig->pollDefs[1]->testMode = false;
        $evConfig->pollDefs[1]->votingEnable = true;
        $evConfig->pollDefs[1]->resultEnable = true;
        $evConfig->pollDefs[1]->supportEnable = true;
        $evConfig->pollDefs[1]->canAssurance = 'budapest';
        $evConfig->pollDefs[1]->proposalEnable = true;
        $evConfig->pollDefs[1]->proposals = 2;
        $evConfig->pollDefs[1]->supportAssurance = "budapest";
        $evConfig->pollDefs[1]->requestedSupport = 500;
	}
	protected function setupConfig() {
	}
	public function test_szavazok_notTestUzemmod_notlogedUser()  {
		global $evConfig,$testData,$componentName,$testUser,$sessionVars;
		$this->setupConfig();
        $testUser->id = 0;
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/be kell jelentkezni/');   
    }

	public function test_szavazok_notTestUzemmod_loggedUser_nemBudapesti_nemSzavazottmeg()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/nem szavazhat/');   
    }

	 public function test_szavazok_TestUzemmod_loggedUser_nemBudapesti_nemSzavazottmeg()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = true;
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/jelolt1/');   
    }

	 public function test_szavazok_notTestUzemmod_loggedUser_budapest_nemSzavazottmeg()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/jelolt1/');   
    }

	public function test_szavazok_notTestUzemmod_loggedUser_budapest_lezartSzavazas()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest /'.$evConfig->canAssurance;
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $evConfig->pollDefs[1]->votingEnable = false;
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/nem lehet/');   
    }

    public function test_eredmeny()  {
        global $evConfig,$testData,$componentName,$testUser;
        $evConfig->pollDefs[1]->votingEnable = false;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $controller = new szavazoController();
        $controller->eredmeny(1, $testUser);
        $this->expectOutputRegex('/eredmény részletei/');
    }
    
    public function test_reszeredmeny_magyar()  {
        global $evConfig,$testData,$componentName,$testUser;
        $evConfig->pollDefs[1]->votingEnable = true;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $testData->setInput('table','magyar');
        $controller = new szavazoController();
        $controller->eredmeny(1, $testUser);
        $this->expectOutputRegex('/eredmény részletei/');
    }
    
    public function test_reszeredmeny_hiteles()  {
        global $evConfig,$testData,$componentName,$testUser;
        $evConfig->pollDefs[1]->votingEnable = true;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $testData->setInput('table','hiteles');
        $controller = new szavazoController();
        $controller->eredmeny(1, $testUser);
        $this->expectOutputRegex('/eredmény részletei/');
    }
    
    public function test_reszeredmeny_offline()  {
        global $evConfig,$testData,$componentName,$testUser;
        $evConfig->pollDefs[1]->votingEnable = true;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $testData->setInput('table','offline');
        $controller = new szavazoController();
        $controller->eredmeny(1, $testUser);
        $this->expectOutputRegex('/eredmény részletei/');
    }
    
    public function test_reszeredmeny_appmagyar()  {
        global $evConfig,$testData,$componentName,$testUser;
        $evConfig->pollDefs[1]->votingEnable = true;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $testData->setInput('table','appmagyar');
        $controller = new szavazoController();
        $controller->eredmeny(1, $testUser);
        $this->expectOutputRegex('/eredmény részletei/');
    }
    
    public function test_reszeredmeny_osszes()  {
            global $evConfig,$testData,$componentName,$testUser;
            $evConfig->pollDefs[1]->votingEnable = true;
            $this->setupConfig();
            $testUser->id = 1;
            $testUser->params = 'ADA:magyar, budapest';
            $testData->setInput('table','szavazatok');
            $controller = new szavazoController();
            $controller->eredmeny(1, $testUser);
            $this->expectOutputRegex('/eredmény részletei/');
    }
        

	public function test_szavazat_save_notTest_notLoged()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->pollDefs[1]->testMode = false;
        $testUser->id = 0;
  
        $controller = new szavazoController();
        $controller->szavazatSave(1, $testUser);
		$this->expectOutputRegex('/Nincs bejelentkezve/');   
    }

	public function test_szavazat_save_notTest_nemBudapesti_nemSzavazottMeg()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->pollDefs[1]->testMode = false;
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar';
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        // input: CSR Token, szavazat
        $testData->setInput('123456','1');
        $testData->setInput('szavazat','1=1,2=2,3=3');

        $controller = new szavazoController();
        $controller->szavazatSave(1, $testUser);
		$this->expectOutputRegex('/nem szavazhat/');   
    }

	public function test_szavazat_save_notTest_budapesti_nemSzavazottMeg()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->pollDefs[1]->testMode = false;
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        // input: CSR Token, szavazat
        $testData->setInput('123456','1');
        $testData->setInput('szavazat','1=1,2=2,3=3');
        $controller = new szavazoController();
        $controller->szavazatSave(1, $testUser);
		$this->expectOutputRegex('/Köszönjük/');   
    }

	public function test_szavazat_save_notTest_budapesti_lezartSzavazas()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->pollDefs[1]->testMode = false;
        $evConfig->pollDefs[1]->votingEnable = false;
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');

        // input: CSR Token, szavazat
        $testData->setInput('123456','1');
        $testData->setInput('szavazat','1=1,2=2,3=3');
        $controller = new szavazoController();
        $controller->szavazatSave(1, $testUser);
		$this->expectOutputRegex('/nem lehet/');   
    }

    public function test_wiki_Shulze_pelda() {
        /* https://hu.wikipedia.org/wiki/Schulze-m%C3%B3dszer#Els%C5%91_p%C3%A9lda
        21 szavazó, 4 jelölt:
        8 ACDB
        2 BADC
        4 CDBA
        4 DBAC
        3 DCBA
        Eredmény: Schulze-rangsor D > A > C > B.
        */
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__eredmeny');
        // szavazás
        $db->exec('INSERT INTO #__categories VALUES (10,"wiki_pelda",0,"",1)');
        // jelöltek
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (28,"jeloltA",10,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (29,"jeloltB",10,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (30,"jeloltC",10,"",1)');
        $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (31,"jeloltD",10,"",1)');

        // 801-808 szavazatok  ACDB 
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,801,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,801,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,801,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,801,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,802,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,802,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,802,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,802,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,803,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,803,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,803,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,803,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,804,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,804,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,804,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,804,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,805,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,805,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,805,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,805,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,806,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,806,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,806,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,806,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,807,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,807,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,807,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,807,0, 29,4)');

        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,808,0, 28,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,808,0, 30,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,808,0, 31,3)');
        $db->exec('INSERT INTO #__szavazatok VALUES	(0,8,10,808,0, 29,4)');

        // 809-810 BADC
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 29,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 28,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 31,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 30,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 29,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 28,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 31,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 30,4)');

        // 811-814 CDBA
        $db->exec('insert into #__szavazatok values	(0,8,10,811,0, 30,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,811,0, 31,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,811,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,811,0, 28,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,812,0, 30,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,812,0, 31,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,812,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,812,0, 28,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,813,0, 30,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,813,0, 31,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,813,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,813,0, 28,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,814,0, 30,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,814,0, 31,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,814,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,814,0, 28,4)');

        // 815-818 DBAC
        $db->exec('insert into #__szavazatok values	(0,8,10,815,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,815,0, 29,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,815,0, 28,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,815,0, 30,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,816,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,816,0, 29,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,816,0, 28,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,816,0, 30,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,817,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,817,0, 29,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,817,0, 28,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,817,0, 30,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,818,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,818,0, 29,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,818,0, 28,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,818,0, 30,4)');


        // 819-821 DCBA
        $db->exec('insert into #__szavazatok values	(0,8,10,819,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,819,0, 30,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,819,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,819,0, 28,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,820,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,820,0, 30,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,820,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,820,0, 28,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,821,0, 31,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,821,0, 30,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,821,0, 29,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,821,0, 28,4)');
        // user_id rendbe tétele 
        $db->exec('UPDATE #__szavazatok SET user_id = szavazo_id WHERE user_id = 0');
	
	     global $evConfig,$testData,$componentName,$testUser;
		  $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $controller = new szavazoController();
        $controller->eredmeny(10, $testUser);
        $this->expectOutputRegex('/class="pozicio"\>1\<\/td\>\<td class="nev"\>jeloltD/');
        $this->expectOutputRegex('/class="pozicio"\>2\<\/td\>\<td class="nev"\>jeloltA/');
        $this->expectOutputRegex('/class="pozicio"\>3\<\/td\>\<td class="nev"\>jeloltC/');
        $this->expectOutputRegex('/class="pozicio"\>4\<\/td\>\<td class="nev"\>jeloltB/');

    }  
    
    public function test_szavazatom()  {
        global $evConfig,$testData,$componentName,$testUser;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $controller = new szavazoController();
        $controller->szavazatom(10, $testUser);
        $this->expectOutputRegex('/Leadott szavazatom/');
    }
    
    public function test_szavazatom_nincs()  {
        global $evConfig,$testData,$componentName,$testUser;
        $this->setupConfig();
        $testUser->id = 0;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $controller = new szavazoController();
        $controller->szavazatom(10, $testUser);
        $this->expectOutputRegex('/Nincs/');
    }
    
    public function test_szavazatokcsv()  {
        global $evConfig,$testData,$componentName,$testUser;
        $this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $controller = new szavazoController();
        $controller->szavazatokcsv(10, $testUser);
        // check syntax only
    }
    
 	 public function test_szavazok_notTestUzemmod_loggedUser_budapest_nincs_jelolt()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $db = JFactory::getDBO();
        $db->exec('DELETE FROM #__szavazatok');
        $db->exec('DELETE FROM #__content');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		  $this->expectOutputRegex('/Nincs jelölt/');   
    }
  
  	 public function test_javaslatForm() {
		global $evConfig,$testData,$componentName,$testUser;
        $session = JFactory::getSession();
        $session->set('myCsrToken','tokenAbc');
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = true;
        
        $testData->setInput('tokenAbc','1');
        $controller = new szavazoController();
        $controller->javaslatForm();
		  $this->expectOutputRegex('/program/');   
  	 }
  	 
  	 public function test_javaslat_save() {
		global $evConfig,$testData,$componentName,$testUser;
        $session = JFactory::getSession();
        $session->set('myCsrToken','tokenAbc');
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = true;
        
        $testData->setInput('nev','Javaslat 1');
        $testData->setInput('program','blabla');
        $testData->setInput('eletrajz','blabla');
        $testData->setInput('kepUrl','xyz');
        $testData->setInput('kontakt','xyz');
        $testData->setInput('tokenAbc','1');
        $controller = new szavazoController();
        $controller->javaslatSave();
		$this->expectOutputRegex('/tárolva/');   
  	 }
  	 
  	 public function test_javaslatok() {
  	     global $evConfig,$testData,$componentName,$testUser;
  	     $session = JFactory::getSession();
  	     $session->set('myCsrToken','tokenAbc');
  	     $testUser->id = 1;
  	     $testUser->params = 'ADA:magyar,budapest';
  	     $evConfig->pollId = 1;
  	     $evConfig->pollDefs[1]->testMode = true;
  	    
  	     // get proposalId
  	     $db = JFactory::getDBO();
  	     $db->setQuery('select max(id) cc from #__content');
  	     $res = $db->loadObject();
  	     $proposalId = $res->cc;
  	     
  	     // set publis proposal
  	     $db->setQuery('update #__content set state=1 where id='.$db->quote($proposalId));
  	     $db->query();
  	     
  	     $testData->setInput('tokenAbc','1');
  	     $controller = new szavazoController();
  	     $controller->javaslatok();
  	     $this->expectOutputRegex('/javaslatok/');
  	 }
  	 
  	 public function test_nemtamogatom_pollIdInput() {
		global $evConfig,$testData,$componentName,$testUser;
        $session = JFactory::getSession();
        $session->set('myCsrToken','tokenAbc');
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar,budapest';
        $evConfig->pollDefs[1]->testMode = true;

        // get proposalId
        $db = JFactory::getDBO();
        $db->setQuery('select max(id) cc from #__content');
        $res = $db->loadObject();
        $proposalId = $res->cc;
        
        $evConfig->pollDefs[1]->requestedSupport = 500;
        $testData->setInput('id',$proposalId);
        $testData->setInput('tokenAbc','1');
        $testData->setInput('pollId','1');
        $controller = new szavazoController();
        $controller->nemtamogatom();
        $this->expectOutputRegex('/task=javaslatok/');   
      
  	 }
  	 
  	 
  	 public function test_tamogatom_1() {
  	     global $evConfig,$testData,$componentName,$testUser;
  	     $session = JFactory::getSession();
  	     $session->set('myCsrToken','tokenAbc');
  	     $testUser->id = 1;
  	     $testUser->params = 'ADA:magyar,budapest';
  	     $evConfig->pollDefs[1]->testMode = true;
  	     
  	     // get proposalId
  	     $db = JFactory::getDBO();
  	     $db->setQuery('select max(id) cc from #__content');
  	     $res = $db->loadObject();
  	     $proposalId = $res->cc;
  	     
  	     $evConfig->pollDefs[1]->requestedSupport = 500;
  	     $testData->setInput('id',$proposalId);
  	     $testData->setInput('tokenAbc','1');
  	     $controller = new szavazoController();
  	     $controller->tamogatom();
  	     $this->expectOutputRegex('/task=javaslatok/');
  	     
  	 }
  	 
  	 public function test_getJavaslat() {
  	     global $evConfig,$testData,$componentName,$testUser;
  	     $session = JFactory::getSession();
  	     $session->set('myCsrToken','tokenAbc');
  	     $testUser->id = 1;
  	     $testUser->params = 'ADA:magyar,budapest';
  	     $evConfig->pollDefs[1]->testMode = true;
  	     
  	     // get proposalId
  	     $db = JFactory::getDBO();
  	     $db->setQuery('select max(id) cc from #__content');
  	     $res = $db->loadObject();
  	     $proposalId = $res->cc;
  	     
  	     $evConfig->pollDefs[1]->requestedSupport = 500;
  	     $testData->setInput('id',$proposalId);
  	     $testData->setInput('tokenAbc','1');
  	     $testData->setInput('pollId','1');
  	     $controller = new szavazoController();
  	     $controller->javaslat();
  	     // syntax check only
  	 }

  	 public function test_tamogatom_requestedSupport() {
  	     global $evConfig,$testData,$componentName,$testUser,$message;
  	     $session = JFactory::getSession();
      $session->set('myCsrToken','tokenAbc');
      $testUser->id = 2;
      $testUser->params = 'ADA:magyar,budapest';
      $evConfig->pollDefs[1]->testMode = true;

  	 	// get proposalId
  	 	$db = JFactory::getDBO();
  	 	$db->setQuery('select max(id) cc from #__content');
  	 	$res = $db->loadObject();
  	 	$proposalId = $res->cc;	

  	 	$evConfig->pollDefs[1]->requestedSupport = 2;
  	 	$testData->setInput('id',$proposalId);
  	 	$testData->setInput('tokenAbc','1');
  	 	$controller = new szavazoController();
  	 	$controller->tamogatom();
  	 	$this->assertEquals('A jelölt elérte a megkivánt támogatottságot. Át lett helyezve az elfogadott jelöltek közé.',$message);
      
  	 }
  	 
  	 public function test_proposal2Candidate1() {
  	     /**  bemenet: requestedSupport = 3
  	      *   még nincs supportWork1
  	      *   még nincsenek jelöltek
  	      *   javaslatok support
  	      *   j1         6
  	      *   j2         5
  	      *   j3         3
  	      *   j4         3
  	      *   j5         2
  	      *   elvárt eredmény: j1,j2,j3,j4 átmegy jelöltbe
  	      */
  	      global $evConfig,$testData,$componentName,$testUser,$message;
  	      $db = JFactory::getDBO();
  	      $db->exec('DELETE FROM #__content WHERE catid=1');
  	      $db->exec('DELETE FROM #__content WHERE catid=2');
  	      $db->exec('DELETE FROM #__supports');
  	      $db->exec('DROP TABLE IF EXISTS  #__supportWork1');
  	      
  	      $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (1,"j1",2,"",1)');
  	      $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (2,"j2",2,"",1)');
  	      $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (3,"j3",2,"",1)');
  	      $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (4,"j4",2,"",1)');
  	      $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (5,"j5",2,"",1)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,111)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,112)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,113)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,114)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,115)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,116)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,111)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,112)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,113)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,114)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,115)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,111)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,112)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,113)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,111)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,112)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,113)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,5,111)');
  	      $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,5,112)');
  	      
  	      $evConfig->pollDefs[1]->requestedCandidateCount = 3;
  	      $evConfig->pollDefs[1]->requestedSupport = 0;
  	      $evConfig->pollDefs[1]->supportEnable = false;
  	      $evConfig->pollDefs[1]->proposalEnable = false;
  	      $evConfig->pollDefs[1]->votingEnable = false;
  	      
  	      $controller = new szavazoController();
  	      $controller->supportEnd();
  	      
          $db->setQuery('SELECT COUNT(*) cc FROM #__content WHERE catid=1');
          $res = $db->loadObject();
          $this->assertEquals(4, $res->cc);
          
  	 }
  	 
  	 public function test_proposal2Candidate2() {
  	     /**  bemenet: requestedSupport = 3
  	      *   már van supporWork1
  	      *   még nincsenek jelöltek
  	      *   javaslatok support
  	      *   j1         6
  	      *   j2         5
  	      *   j3         3
  	      *   j4         3
  	      *   j5         2
  	      *   elvárt eredmény: nincsenek jelöltek
  	      */
  	     global $evConfig,$testData,$componentName,$testUser,$message;
  	     $db = JFactory::getDBO();
  	     $db->exec('DELETE FROM #__content WHERE catid=1');
  	     $db->exec('DELETE FROM #__content WHERE catid=2');
  	     $db->exec('DELETE FROM #__supports');
  	     
  	     $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (1,"j1",2,"",1)');
  	     $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (2,"j2",2,"",1)');
  	     $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (3,"j3",2,"",1)');
  	     $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (4,"j4",2,"",1)');
  	     $db->exec('INSERT INTO #__content (id, title, catid, introtext, state) VALUES (5,"j5",2,"",1)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,111)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,112)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,113)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,114)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,115)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,1,116)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,111)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,112)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,113)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,114)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,2,115)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,111)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,112)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,3,113)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,111)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,112)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,4,113)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,5,111)');
  	     $db->exec('INSERT INTO #__supports (id, proposal_id, user_id) VALUES (0,5,112)');
  	      	     
  	     $evConfig->pollDefs[1]->requestedCandidateCount = 3;
  	     $evConfig->pollDefs[1]->supportEnable = false;
  	     $evConfig->pollDefs[1]->proposalEnable = false;
  	     $evConfig->pollDefs[1]->votingEnable = false;
  	     $controller = new szavazoController();
  	     $controller->supportEnd();
  	     
  	     $db->setQuery('SELECT COUNT(*) cc FROM #__content WHERE catid=1');
  	     $res = $db->loadObject();
  	     $this->assertEquals(0, $res->cc);
  	 }
  	 
  	 
}
?>
