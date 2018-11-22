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
require_once "./testJoomlaFramework.php.php";
require_once "../component/site/elovalasztok.php";

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
            catid int(11),
            introtext varchar(128),
            state int(1),
            PRIMARY KEY (id)
        )');
        $db->exec('CREATE TABLE #__users(
            id int(11) AUTO_INCREMENT,
            username varchar(128),
            params varchar(128),
            PRIMARY KEY (id)
        )');
        $db->exec('INSERT INTO #__categories VALUES (0,"szavazas",0,"",1)');
        $db->exec('INSERT INTO #__content VALUES (0,"jelolt1",1,"",1)');
        $db->exec('INSERT INTO #__content VALUES (0,"jelolt2",1,"",1)');
        $db->exec('INSERT INTO #__content VALUES (0,"jelolt3",1,"",1)');
        $db->exec('INSERT INTO #__users VALUES (0,"user1","ADA:magyar,budapest")');
        $db->exec('INSERT INTO #__users VALUES (0,"user2","ADA:magyar")');
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1] = new stdClass();	
        $evConfig->pollDefs[1]->testMode = false;
        $evConfig->pollDefs[1]->votingEnable = true;
        $evConfig->pollDefs[1]->resultEnable = true;
        $evConfig->pollDefs[1]->canAssurance = 'budapest';
	}
	protected function setupConfig() {
	}

	public function test_szavazok_notTestUzemmod_notlogedUser()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 0;
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/be kell jelentkezni/');   
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

	public function test_szavazok_notTestUzemmod_loggedUser_budapest_marSzavazott()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $evConfig->pollId = 1;
        $evConfig->pollDefs[1]->testMode = false;
        $db = JFactory::getDBO();
        $db->exec('INSERT INTO #__szavazatok VALUES (0,0,1,1,1,1,1)');
        $db->exec('INSERT INTO #__szavazatok VALUES (0,0,1,1,1,2,2)');
        $db->exec('INSERT INTO #__szavazatok VALUES (0,0,1,1,1,3,3)');
        $controller = new szavazoController();
        $controller->szavazok($evConfig->pollId, $testUser);
		$this->expectOutputRegex('/már szavazott/');   
    }

	public function test_eredmeny()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
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

	public function test_szavazat_save_notTest_marSzavazott()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->pollDefs[1]->testMode = false;
        $testUser->id = 1;
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
  
        $controller = new szavazoController();
        $controller->szavazatSave(1, $testUser);
		$this->expectOutputRegex('/már szavazott/');   
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
        $db->exec('INSERT INTO #__content VALUES (28,"jeloltA",10,"",1)');
        $db->exec('INSERT INTO #__content VALUES (29,"jeloltB",10,"",1)');
        $db->exec('INSERT INTO #__content VALUES (30,"jeloltC",10,"",1)');
        $db->exec('INSERT INTO #__content VALUES (31,"jeloltD",10,"",1)');

        /* 801-808 szavazatok  ACDB */
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

        /* 809-810 BADC*/
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 29,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 28,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 31,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,809,0, 30,4)');

        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 29,1)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 28,2)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 31,3)');
        $db->exec('insert into #__szavazatok values	(0,8,10,810,0, 30,4)');

        /* 811-814 CDBA*/
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

        /* 815-818 DBAC*/
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


        /* 819-821 DCBA*/
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
        /* user_id rendbe tétele */
        $db->exec('UPDATE #__szavazatok SET user_id = szavazo_id WHERE user_id = 0');
	
	    global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $testUser->params = 'ADA:magyar, budapest';
        $controller = new szavazoController();
        $controller->eredmeny(10, $testUser);
        $this->expectOutputRegex('/\<td class="pozicio"\>1\<\/td\>\<td class="nev"\>jeloltD/');
        $this->expectOutputRegex('/\<td class="pozicio"\>2\<\/td\>\<td class="nev"\>jeloltA/');
        $this->expectOutputRegex('/\<td class="pozicio"\>3\<\/td\>\<td class="nev"\>jeloltC/');
        $this->expectOutputRegex('/\<td class="pozicio"\>4\<\/td\>\<td class="nev"\>jeloltB/');

    }  
  
}
?>
