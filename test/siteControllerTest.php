<?php
global $evConfig;
require_once "./testJoomlaFramework.php";
require_once "../component/site/elovalasztok.php";

class elovalasztokControllerTest extends PHPUnit_Framework_TestCase {
    function __construct() {
		parent::__construct();
	}
	protected function setupConfig() {
	}

	public function test_szavazok_notTestUzemmod_notlogedUser()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 0;
        $evConfig->testUzemmod = false;
        $controller = new szavazoController();
        $controller->szavazok(10, $testUser);
		$this->expectOutputRegex('/be kell jelentkezni/');   
    }

	public function test_szavazok_notTestUzemmod_loggedUser_budapest_nincsJelolt()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $evConfig->testUzemmod = false;
        $testData->clear();
        $testData->addDbResult(JSON_decode('[]')); // szavazott már?
        $testData->addDbResult(JSON_decode('{}')); // szavazat beolvasás a teheti -ben
        $testData->addDbResult(JSON_decode('[]')); // szavazott már? a teheti -ben
        $testData->addDbResult(JSON_decode('{"cc":1}')); // assurace control szavazasraJogsult -bann

        $controller = new szavazoController();
        $controller->szavazok(10, $testUser);
		$this->expectOutputRegex('/Nincs/');   
    }

	public function test_szavazok_notTestUzemmod_loggedUser_budapest_marSzavazott()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $evConfig->testUzemmod = false;
        $testData->clear();
        $testData->addDbResult(JSON_decode('[{},{}]')); // szavazott már?
        $testData->addDbResult(JSON_decode('{}')); // szavazat beolvasás a teheti -ben
        $testData->addDbResult(JSON_decode('[{},{}]')); // szavazott már? a teheti -ben
        $testData->addDbResult(JSON_decode('{"cc":1}')); // assurace control szavazasraJogsult -bann

        $controller = new szavazoController();
        $controller->szavazok(10, $testUser);
		$this->expectOutputRegex('/már szavazott/');   
    }

	public function test_szavazok_notTestUzemmod_loggedUser_notBudapest_nincsJelolt()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testUser->id = 1;
        $evConfig->testUzemmod = false;
        $testData->clear();
        $testData->addDbResult(JSON_decode('[]')); // szavazott már?
        $testData->addDbResult(JSON_decode('{}')); // szavazat beolvasás a teheti -ben
        $testData->addDbResult(JSON_decode('[]')); // szavazott már? a teheti -ben
        $testData->addDbResult(JSON_decode('{"cc":0}')); // assurace control szavazasraJogsult -bann

        $controller = new szavazoController();
        $controller->szavazok(10, $testUser);
		$this->expectOutputRegex('/nem szavazhat/');   
    }

	public function test_eredmeny()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $testData->clear();
        $testData->addDbResult(JSON_decode('{}')); // szavazat beolvasás
        $testData->addDbResult(JSON_decode('{}')); // get chached report?
        $testData->addDbResult(JSON_decode('[]')); // get candidates
        $testData->addDbResult(JSON_decode('{"cc":0}')); // loadVoteCount
        $testData->addDbResult(JSON_decode('[]')); // loadInFirst
        $testData->addDbResult(JSON_decode('[]')); // loadDiffMatrix
        $testData->addDbResult(JSON_decode('[]')); // loadDiffMatrix
        $testData->addDbResult(true); // save cached report

        $controller = new szavazoController();
        $controller->eredmeny(10, $testUser);
		$this->expectOutputRegex('/eredmény részletei/');   
    }

	public function test_szavazat_save_notTest_notLoged()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->testUzemmod = false;
        $testUser->id = 0;
  
        $controller = new szavazoController();
        $controller->szavazatSave(10, $testUser);
		$this->expectOutputRegex('/Nincs bejelentkezve/');   
    }

	public function test_szavazat_save_notTest_marszavazott()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->testUzemmod = false;
        $testUser->id = 1;
        $testData->clear();
        $testData->addDbResult(JSON_decode('{}')); // teheti.szavazat beolvasás
        $testData->addDbResult(JSON_decode('[{},{}]')); // teheti.szavazottMar

  
        $controller = new szavazoController();
        $controller->szavazatSave(10, $testUser);
		$this->expectOutputRegex('/már szavazott/');   
    }

	public function test_szavazat_save_notTest_nemBudapesti()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->testUzemmod = false;
        $testUser->id = 1;
        $testData->clear();
        $testData->addDbResult(JSON_decode('{}')); // teheti.szavazat beolvasás
        $testData->addDbResult(JSON_decode('[]')); // teheti.szavazottMar
        $testData->addDbResult(JSON_decode('{"cc":0}')); // assurance checck

  
        $controller = new szavazoController();
        $controller->szavazatSave(10, $testUser);
		$this->expectOutputRegex('/nem szavazhat/');   
    }

	public function test_szavazat_save_notTest_OK()  {
		global $evConfig,$testData,$componentName,$testUser;
		$this->setupConfig();
        $evConfig->testUzemmod = false;
        $testUser->id = 1;
        $testData->clear();

        $testData->addDbResult(JSON_decode('{}')); // teheti.szavazat beolvasás
        $testData->addDbResult(JSON_decode('[]')); // teheti.szavazottMar
        $testData->addDbResult(JSON_decode('{"cc":1}')); // assurance checck

        $testData->addDbResult(JSON_decode('{}')); // teheti.szavazat beolvasás
        $testData->addDbResult(JSON_decode('[]')); // teheti.szavazottMar
        $testData->addDbResult(JSON_decode('{"cc":1}')); // assurance checck

        $testData->addDbResult(true); // begin transaction
        $testData->addDbResult(JSON_decode('{}')); // get category
        $testData->addDbResult(true); // delete cached report
        $testData->addDbResult(true); // end transaction
  
        $controller = new szavazoController();
        $controller->szavazatSave(10, $testUser);
		$this->expectOutputRegex('/Köszönjük/');   
    }

}
?>
