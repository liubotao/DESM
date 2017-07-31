<?php

$table = $_POST["table"];
$pkId = "";
$service = new Service();
$data = array();

foreach((array) $_POST['name'] as $key => $name) {
	if (strlen(trim($_POST['name'][$key])) > 1) {
		$temp = array();
		$temp['name'] = $name;
		if (isset($_POST['key'][$key])) {
			$pkId = $name;
		}
		$temp['type'] = $_POST['type'][$key];
		$data[] = $temp;
	}
}
$entry = new Entry();
//$entry->getEntryClass($table, $data);

$dao = new Dao();
//echo $dao->getDaoClass($table, $pkId);

$service = new Service();
echo $service->getServiceClass($table, $pkId);

class Entry {
	
	function getEntryClass($name, $data) {
		$className =  ucfirst($name); 
		$string = "class {$className} { ";
		foreach ($data as $row) {
			$string .= $this->defineFiled($row['name'], $row['type']);
		}
		foreach ($data as $row) {
			$string .= $this->setterAndGetter($row['name'], $row['type']);
		}
		$string .= "}";
		echo $string;
	}
	
	function defineFiled($name, $type = "int") {
		$filedName = $this->nameToFiledName($name);
		return "private $type ".$filedName."; \n";
	}

	function nameToFiledName($name) {
		$filedNameList = explode("_", $name);
		$filedName = "";
		foreach ($filedNameList as $key => $filed) {
			if ($key == 0) {
				$filedName .= $filed;
			} else {
				$filedName .= ucfirst($filed);
			}
		}
		return $filedName;
	}

	function setterAndGetter($name, $type) {
		$filedName =  $this->nameToFiledName($name);
		$functionName =  ucfirst($filedName); 
		$setter = "public void set{$functionName} ({$type} {$filedName}) {
			this.{$filedName} = {$filedName};
		}\n";
		$getter = "public {$type} get{$functionName} () {
			return this.{$filedName};
		}\n";
		return $setter.$getter;
	}
}

class Dao {
	
	function getDaoName($name) {
		$daoClassName =  ucfirst($name); 
		return  $daoClassName."Dao";
	}
	
	function getDaoClass($entryClass, $pkId) {
		$className = $this->getDaoName($entryClass); 
		$string = "class {$className} { ";
		$string .=  $this->getList($entryClass);
		$string .=  $this->getSearchResult($entryClass);
		$string .=  $this->findById($entryClass, $pkId);
		$string .=  $this->delete($pkId);
		$string .=  $this->update($entryClass);
		$string .=  $this->insert($entryClass);
		$string .=  $this->getCount($entryClass);
		$string .=  $this->getTotal();
		$string .= "}";
		return $string;
	}
	
	function getList($entryClass) {
		return "List<{$entryClass}> get{$entryClass}List (HashMap map);";
	}
	
	function getSearchResult($entryClass) {
		return "List<{$entryClass}> getSearchResult (HashMap map);";
	}
	
	function findById($entryClass, $pkId) {
		$ucPkId = ucfirst($pkId);
		return "{$entryClass} find{$entryClass}By{$ucPkId}(int {$pkId});";
	}
	
	function delete($pkId) {
		return "void delete(int {$pkId});";
	}
	
	function update($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "void update({$entryClass} {$lcEntryClass});";
	}
	
	function insert($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "int insert({$entryClass} {$lcEntryClass});";
	}
	
	function getCount($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "int getCount({$entryClass} {$lcEntryClass});";
	}
	
	function getTotal() {
		return "int getTotal();";
	}
}

class Service {

	public $daoName;
	
	function getDaoName($name) {
		$daoClassName =  ucfirst($name); 
		return  $daoClassName."Dao";
	}
	
	function getServiceName($name) {
		$serviceClassName =  ucfirst($name); 
		return  $serviceClassName."Service";
	}
	
	function getServiceClass($entryClass, $pkId) {
		$className = $this->getServiceName($entryClass); 
		$this->daoName = $this->getDaoName($entryClass);
		$string = "class {$className} { ";
		$string .=  $this->getList($entryClass);
		$string .=  $this->getSearchResult($entryClass);
		$string .=  $this->findById($entryClass, $pkId);
		$string .=  $this->delete($pkId);
		$string .=  $this->update($entryClass);
		$string .=  $this->insert($entryClass);
		$string .=  $this->getCount($entryClass);
		$string .=  $this->getTotal();
		$string .= "}";
		return $string;
	}
	
	function getList($entryClass) {
		return "List<{$entryClass}> get{$entryClass}List (HashMap map) { return {$this->daoName}.getList(map); }";
	}
	
	function getSearchResult($entryClass) {
		return "List<{$entryClass}> getSearchResult (HashMap map) { return {$this->daoName}.getSearchResult(map); }";
	}
	
	function findById($entryClass, $pkId) {
		$ucPkId = ucfirst($pkId);
		return "{$entryClass} find{$entryClass}By{$ucPkId}(int {$pkId}) { return {$this->daoName}.find{$entryClass}By{$ucPkId}({$pkId}); }";
	}
	
	function delete($pkId) {
		return "void delete(int {$pkId}) { {$this->daoName}.delete($pkId); }";
	}
	
	function update($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "void update({$entryClass} {$lcEntryClass}){ {$this->daoName}.update($lcEntryClass); }";
	}
	
	function insert($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "int insert({$entryClass} {$lcEntryClass}) { return {$this->daoName}.insert($lcEntryClass);}";
	}
	
	function getCount($entryClass) {
		$lcEntryClass = lcfirst($entryClass);
		return "int getCount({$entryClass} {$lcEntryClass}) { return {$this->daoName}.getCount({$lcEntryClass}); }";
	}
	
	function getTotal() {
		return "int getTotal() { return {$this->daoName}.getTotal(); }";
	}
}


