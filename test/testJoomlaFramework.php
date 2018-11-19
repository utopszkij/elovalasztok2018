<?php
/**
* test framwork for joomla components unit test
*/
error_reporting(E_ALL & ~E_NOTICE);
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_BASE', '');
define('JPATH_ROOT', '');
define('JPATH_ADMINISTRATOR', '');
define('_UNITTEST', '1');

global $_SERVER;
global $testData;
global $componentName;
global $viewName;
global $testApplication;
global $testDocument;
global $testController;
global $testModel;
global $testView;
global $testDatabase;
global $testUser;

class testDataClass {

	/**
	* set input parameters for test
	* $inputs['name1'] = 'value1', $inputs['name2'] = 'value2', .... 
	*/
	protected $inputs;

	/**
	* set Database result, and errorNum, errorMsg for test
	* $dbResults[0] = JSON_encode('{'field1":"value1", "field2":"value2"}'); 
	* $dbResults[1] = JSON_encode([{'field1":"value1", "field2":"value2"}, {'field1":"value11", "field2":"value12"}])             
	* set $dbErrorNum, $dbErrorMsg
	*/
	protected $dbResults;
	protected $dbErrorNum;
	protected $dbErrorMsg;
	protected $dbIndex;
    protected $db; // JDatabase;

    public $gotArgs = array();
	public $mock_data = array();
	/**
	* set remoteCall results for test
	*/
	protected $remoteResults;
	protected $remoteIndex;
		
	function __construct() {
		global $testUser, $testDatabase;
		$testUser = new JUser();
		$this->clear();
	}
	public function clear() {
		$this->inputs = array();
		$this->dbResults = array();
		$this->dbErrorNum = 0;
		$this->dbErrorMsg = '';
		$this->dbIndex = 0;
		$this->remoteResults = array();
		$this->remoteIndex = 0;
	}
	function addDbResult($value) {
		$this->dbResults[] = $value;
	}
	function setInput($name,$value) {
		$this->inputs[$name] = $value;
	}
	function addRemoteResult($value) {
		$this->remoteResults[] = $value;
	}

    function remoteCall($url,$method,$data,$extraHeader) {
        $this->gotArgs["url"] = $url;
        $this->gotArgs["method"] = $method;
        $this->gotArgs["data"] = $data;
        $this->gotArgs["extraHeader"] = $extraHeader;
        return $this->getRemoteResult();
    }

	public function getDbResult() {
		if ($this->dbIndex < count($this->dbResults))
		   $result = $this->dbResults[$this->dbIndex];
	    else
		   $result = '_none_';	
		$this->dbIndex = $this->dbIndex + 1;
		return $result;
	}
	public function getRemoteResult() {
		if ($this->remoteIndex < count($this->remoteResults))
		   $result = $this->remoteResults[$this->remoteIndex];
	    else
		   $result = '';	
		$this->remoteIndex = $this->remoteIndex + 1;
		return $result;
	}
	public function getInput($name,$default='') {
		if (isset($this->inputs[$name]))
		  $result = $this->inputs[$name];
	    else
		  $result = $default;
		return $result;
	}
}


/**
* set component name for test (without 'com_')
*/
$componentName = 'valami';

class JFactory {
	public static function getApplication() {
		global $testApplication;
		if (!isset($testApplication)) $testApplication = new JApplication();
		return $testApplication;
	}
	public static  function getDocument() {
		global $testDocument;
		if (!isset($testDocument)) $testDocument = new JDocument();
		return $testDocument;
	}
	public static  function getUser($id=0) {
		global $testUser;
		if (!isset($testUser)) $testUser = new JUser();
		$testUser->id = $id;
		$testUser->username='guest';
		return $testUser;
	}
	public static  function getLanguage() {
		return new JLanguage();
	}
	public static  function getDBO() {
		global $testDatabase;
		if (!isset($testDatabase)) {
            $testDatabase = new JDatabase();
        }
		return $testDatabase;
	}
	public static function getSession() {
		return new JSession();
	}
	public static function getConfig() {
		return JSON_decode('{"secret":"abcdefg"}');
	}
}

class JApplication {
	public $input;
	    function __construct() {
		$this->input = new JInput();
	}
	public function getUserStateFromRequest($name, $default='',$dataType='') {
		return $default;
	}	
	public function getCfg($name, $default='') {
		return $default;
	}
	public function login($credentials) {
		global $testUser;
		$testUser->id = 1;
		$testUser->username='testElek';
		$testUser->name='Test Elek';
		return true;
	}	
	public function logout() {
		global $testUser;
		$testUser->id=0;
		$testUser->username = 'guest';
		$testUser->name = '';
	}
}

class JDocument {
	public function getType() {
		return 'html';
	}
}

class JInput {
	public function get($name, $default='') {
		global $testData;
		return $testData->getInput($name, $default);
	}
	public function set($name,$value,$dataType='') {
		global $testData;
		$testData->setInput($name,$value);
	}
}

class JRequest {
	public  static function getVar($name, $default='', $dataType='') {
		global $testData;
		return $testData->getInput($name, $default);
	}
	public  static function getWord($name, $default='', $dataType='') {
		return $this->getVar($name, $default, $dataType);
	}
	public  static function getCmd($name, $default='', $dataType='') {
		return $this->getVar($name, $default, $dataType);
	}
	public  static function setVar($name,$value,$dataType='') {
		global $testData;
		$testData->setInput($name,$value);
	}
}

class JURI {
	public  static function base() {
		return 'http://localhost/';
	}
	public  static function root() {
		return 'http://localhost/';
	}
}

class JText {
	public  static function _($token) {
		return $token;
	}
}
class JHTML {
	public  static function _($token) {
		return '<span class="html.token">'.$token.'</span>';
	}
}

class JDatabase {
    protected $mysqli;
    protected $sql;
    protected $errorMsg;
    protected $errorNum;
    function __construct() {
        $this->mysqli = new mysqli("localhost", TESTDBUSER, TESTDBPSW, TESTDB);
    }
	public function setQuery($sql) {
		$this->sql = str_replace('#__',TESTDBPRE,$sql);
	}
	public function getQuery() {
		return $this->sql;
	}
	public function loadObjectList() {
		global $testData;
        $this->errorMsg = '';
        $this->errorNum = 0;
		$result = $testData->getDbResult();	
        if ($result == '_none_') {
            $result = [];
            try {
                $cursor = $this->mysqli->query($this->sql);
            } catch (Exception $e) {
                try {
                    $this->mysqli = new mysqli("localhost", TESTDBUSER, TESTDBPSW, TESTDB);
                    try {
                        $cursor = $this->mysqli->query($this->sql);
                    } catch(Exception $e) {
                        $cursor = false;
                        $this->errorMsg = 'error_in_query '.$e->getMessage().' sql='.$this->sql;
                        $this->errorNum = 1000;
                    }
                } catch(Exception $e) {
                        $cursor = false;
                        $this->errorMsg = 'error_in_reconnect '.$e->getMessage();
                        $this->errorNum = 1000;
                }
            }
            if ($cursor) {
                $w = $cursor->fetch_object();
                while ($w != null) {
                    $i = count($result);
                    $result[$i] = $w;
                    $w = $cursor->fetch_object();
                }
                $cursor->close();
                $this->errorMsg = 'error_in_fetch '.$this->mysqli->error;
                //$this->errorNum = $this->mysqli->errno;
            }
        }
        return $result;
	}
	public function loadObject() {
		global $testData;
		$result = $testData->getDbResult();	
        if ($result == '_none_') {
            $res = $this->loadObjectList();
            if (count($res) > 0) {
                $result = $res[0];
            } else {
                $result = false;
            }
        }
        return $result;
	}
	public function query() {
		global $testData;
		$result = $testData->getDbResult();	
        if ($result == '_none_') {
            if (!isset($this->sql)) $this->sql = '';
            try {
                $result = $this->mysqli->query($this->sql);
                $this->errorMsg = 'error_in_query '.$this->msqli->error.' sql='.$this->sql;
                $this->errorNum = $this->mysqli->errno;
            } catch (Exception $e) {
                try {
                    $this->mysqli = new mysqli("localhost", TESTDBUSER, TESTDBPSW, TESTDB);
                    try {
                        $result = $this->mysqli->query($this->sql);
                        $this->errorMsg = 'error_in_query '.$this->msqli->error.' sql='.$this->sql;
                        $this->errorNum = $this->mysqli->errno;
                    } catch(Exception $e) {
                        $result = false;
                        $this->errorMsg = 'error_in_query '.$e->getMessage().' sql='.$this->sql;
                        $this->errorNum = 1000;
                    }
                } catch(Exception $e) {
                    $return = false;
                    $this->errorMsg = 'error_in_reconnect '.$e->getMessage().' sql='.$this->sql;
                    $this->errorNum = 1000;
                }

            }
        }
        return $result;
	}
	public function getErrorNum() {
		return $this->errorNum;
	}
	public function getErrorMsg() {
		return $this->errorMsg;
	}
	public function quote($str) {
        return '"'.$str.'"';
	}
    public function exec($sqlStr) {
        $this->setQuery($sqlStr);
        $this->query();
    }
}

class JDatabaseQuery {
	public function select($str) {
		
	}
	public function from($str) {
		
	}
	public function where($str) {
		
	}
	public function order($str) {
		
	}
	public function __toString() {
		return '';
	}
	public function join($str)	{
		
	}
}

class JUser {
	public $id = 0;
	public $username = '';
	public $name = '';
        public $groups = array();
	public function save() {
		return true;
	}
	public function getParam($name) {
		return $name;
	}
	public function setParam($name,$value) {
		
	}
	public function bind($data) {
		return true;
	}
	public function getError() {
		return '';
	}
}

class JLanguage {
	
}
class JTable {
	protected $tableName;
	public function bind($data) {
		
	}
	public function getTableName() {
		return $this->tableName;
	}
	public function setError($str) {
		
	}
	public function getError() {
		
	}
}
class JControllerLegacy {
	protected $redirectURI = '';

	function __construct($config='') {}
	public function getView($aviewName = '',$viewType='html') {
		global $componentName, $viewName;
		if ($aviewName != '') 
			$viewName = $aviewName;
		require_once (JPATH_COMPONENT.DS.'views'.DS.$viewName.DS.'view.'.$viewType.'.php');
		$viewClassName = $componentName.'View'.ucfirst($viewName);
		return new $viewClassName ();
	}
	public function getModel($modelName = '') {
		global $componentName,$viewName;
		if (!isset($this->_viewname)) $this->_viewname = '';
		if (($modelName == '') & ($this->_viewname != '')) $modelName = $this->_viewname;
		if (($modelName == '') & ($viewName != '')) $modelName = $viewName;
		$viewName = $modelName;
		if (file_exists(JPATH_COMPONENT.DS.'models'.DS.$modelName.'.php')) {
			require_once (JPATH_COMPONENT.DS.'models'.DS.$modelName.'.php');
			$modelClassName = $componentName.'Model'.ucfirst($modelName);
		} else {
			require_once (JPATH_COMPONENT.DS.'models'.DS.'model.php');
			$modelClassName = $componentName.'Model';
		}	
		return new $modelClassName ();
	}
	public function setRedirect($uri) {
	  $this->redirectURI = $uri;	
	}
	public function redirect($message = '') {
		global $testData;
        $testData->mock_data["redirectURI"] = $this->redirectURI;
		$testData->mock_data["redirectMsg"] = $message;
		echo 'redirect:'.$this->redirectURI.' message='.$message."\n";
	}
	public function edit() {
		echo 'joomla default edit task';
	}
	public function add() {
		echo 'joomla default add task';
	}
	public function save() {
		echo 'joomla default save task';
	}
	public function remove() {
		echo 'joomla default remove task';
	}
	public function browse() {
		echo 'joomla default browse task';
	}
	public function setMessage($msg) {
		
	}
}
class JModelLegacy {
	protected $errorMsg;
	function __construct($config='') {
	}
	public function set($name,$value) {
		$this->$name = $value;
	}
	public function addIncludePath($str='') {
		
	}
	public function addTablePath($str='') {
		
	}
    public function def($property, $default = null) {
	   if (isset($this->property) == false) $this->$property = $default;
	   return $this->$property; 	
	}
	public function get($property, $default = null) {
	   if (isset($this->property) == false) 
		   return $default;
	   else 
	       return $this->$property; 	
		
	}
	public function getDbo() {
		return $this->_db;
	}
	public function setError($str) {
		$this->errorMsg = $str;
	}
	public function getError() {
		return $this->errorMsg;
	}
	public function getErrors() {
		return array();
	}
	public function getInstance() {
		return null;
	}
	public function getName() {
		global $viewName;
		return $viewName;
	}
	public function getProperties() {
		return array();
	}
	public function setState($name,$value) {
		
	}
	public function getState($name, $default='') {
		return $default;
	}
	public function getTable($tableName = '') {
		return new JTable();
	}
	public function loadHistory($version, $table) {
		return true;
	}
	public function setDbo($db) {
		$this->_db = $db;
	}
	public function setProperties($properties) {
		return true;
	}
	protected function getListCount($query) {
		return 0;
	}
}
class JModelList extends JModelLegacy {	
	public function getQuery() {
		return new $JQuery();
	}
	public function getTotal() {
		return 0;	
	}
	public function getItems() {
		return array();
	}
}
class JViewLegacy {
	protected $layout;
	function __construct($config='') {}
	public function set($name,$value) {
		$this->$name = $value;
	}
	public function setLayout($str) {
		$this->layout = $str;
	}
	public function display($tmp) {
		global $viewName;
		$tmp = $this->layout.$tmp;
		if ($tmp == '') $tmp = 'default';
		
		if ($this->layout != '')
		  echo 'testJoomlaFramwork view.display '.$this->layout.'_'.$tmp.'<br>';
		else	
		  echo 'testJoomlaFramwork view.display '.$tmp.'<br>';
		include JPATH_COMPONENT.DS.'views'.DS.$viewName.DS.'tmpl'.DS.$tmp.'.php';
	}
	public function setModel($model) {
		$this->model = $model;
	}
}

class JSession {
	public static function get($name, $default='') {
		return $default;
	}
	public static function set($name,$value) {
		
	}
	public static function checkToken() {
		return true;
	}
	public static function getFormToken() {
		return "testFormToken";
	}
}

class JPagination {
  function __construct($total, $limitstart, $limit) {
	  
  }
  public function getListFooter() {
	  return 'pagination';
  }
}
class UsersModelGroup {
  public function save($data) {
    return true;
  }
  public function getItem($id) {
    return false;
  }
}
// global functions
function jimport($str) {}

// init globals
$_SERVER['HTTP_SITE'] = 'localhost';
$_SERVER['REQUEST_URI'] = 'index.php';
$componentName = 'testComponent';
$viewName = 'testView';
$testData = new testDataClass();
?> 
