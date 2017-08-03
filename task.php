<?php

$table = $_POST["table"];
$sqlPkId = $pkId = "";
$service = new Service();
$data = array();

foreach ((array)$_POST['name'] as $key => $name) {
    if (strlen(trim($_POST['name'][$key])) > 1) {
        $temp = array();
        $temp['name'] = $name;
        $temp['pk'] = 0;
        if (isset($_POST['key'][$key])) {
            $sqlPkId = $pkId = $name;
            $temp['pk'] = 1;
        }
        $temp['type'] = $_POST['type'][$key];
        $data[] = $temp;
    }
}


$entry = new Entry();
$dir = dirname(__FILE__);
file_put_contents($dir."/".$entry->getEntryName($table).".java", $entry->getEntryClass($table, $data));
$entryClassName = $entry->getEntryName($table);
$pkId = $entry->nameToFiledName($pkId);

$map = new Mapper($table, $entryClassName, $sqlPkId, $pkId, $data);
file_put_contents($dir."/".$entryClassName.".xml", $map->getMapper());

$dao = new Dao();
file_put_contents($dir."/".$dao->getDaoName($entryClassName).".java", $dao->getDaoClass($entryClassName, $pkId));
$service = new Service();
file_put_contents($dir."/".$service->getServiceName($entryClassName).".java", $service->getServiceClass($entryClassName, $pkId));


class Entry
{

    function getEntryName($table) {
        return ucfirst($this->nameToFiledName($table));
    }

    function getEntryClass($name, $data)
    {
        $className = $this->getEntryName($name);
        $string = " public class {$className} { ";
        foreach ($data as $row) {
            $string .= $this->defineFiled($row['name'], $row['type']);
        }
        foreach ($data as $row) {
            $string .= $this->setterAndGetter($row['name'], $row['type']);
        }
        $string .= "}";
        return $string;
    }

    function defineFiled($name, $type = "int")
    {
        $filedName = $this->nameToFiledName($name);
        return "private $type " . $filedName . "; \r\n";
    }

    function nameToFiledName($name)
    {
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

    function setterAndGetter($name, $type)
    {
        $filedName = $this->nameToFiledName($name);
        $functionName = ucfirst($filedName);
        $setter = "public void set{$functionName} ({$type} {$filedName}) {
			this.{$filedName} = {$filedName};
		}\n";
        $getter = "public {$type} get{$functionName} () {
			return this.{$filedName};
		}\n";
        return $setter . $getter;
    }
}

class Dao
{

    function getDaoName($name)
    {
        $daoClassName = ucfirst($name);
        return $daoClassName . "Dao";
    }

    function getDaoClass($entryClass, $pkId)
    {
        $className = $this->getDaoName($entryClass);
        $string = "@Repository \n public interface {$className} { ";
        $string .= $this->getList($entryClass);
        $string .= $this->getSearchResult($entryClass);
        $string .= $this->findById($entryClass, $pkId);
        $string .= $this->delete($pkId);
        $string .= $this->deleteByPkIds();
        $string .= $this->update($entryClass);
        $string .= $this->insert($entryClass);
        $string .= $this->getCount($entryClass);
        $string .= $this->getTotal();
        $string .= "}";
        return $string;
    }

    function getList($entryClass)
    {
        return "List<{$entryClass}> get{$entryClass}List (HashMap map);";
    }

    function getSearchResult($entryClass)
    {
        return "List<{$entryClass}> getSearchResult (HashMap map);";
    }

    function findById($entryClass, $pkId)
    {
        $ucPkId = ucfirst($pkId);
        return "{$entryClass} get{$entryClass}By{$ucPkId}(int {$pkId});";
    }

    function delete($pkId)
    {
        return "void delete(int {$pkId});";
    }

    function deleteByPkIds() {
        return "void deleteByPkIds(List<Integer> ids); ";
    }

    function update($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "void update({$entryClass} {$lcEntryClass});";
    }

    function insert($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "int insert({$entryClass} {$lcEntryClass});";
    }

    function getCount($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "int getCount({$entryClass} {$lcEntryClass});";
    }

    function getTotal()
    {
        return "int getTotal();";
    }

}

class Service
{

    public $daoName;

    public $lcDaoName;

    function getDaoName($name)
    {
        $daoClassName = ucfirst($name);
        return $daoClassName . "Dao";
    }

    function getServiceName($name)
    {
        $serviceClassName = ucfirst($name);
        return $serviceClassName . "Service";
    }

    function getServiceClass($entryClass, $pkId)
    {
        $className = $this->getServiceName($entryClass);
        $this->daoName = $this->getDaoName($entryClass);
        $this->lcDaoName = lcfirst($this->daoName);

        $string = "@Service \n public class {$className} { ";
        $string .= "@Autowired \n private {$this->daoName} " .lcfirst($this->daoName)."; \n";
        $string .= $this->getList($entryClass);
        $string .= $this->getSearchResult($entryClass);
        $string .= $this->findById($entryClass, $pkId);
        $string .= $this->delete($pkId);
        $string .= $this->deleteByPkIds();
        $string .= $this->update($entryClass);
        $string .= $this->insert($entryClass);
        $string .= $this->getCount($entryClass);
        $string .= $this->getTotal();
        $string .= "}";
        return $string;
    }

    function getList($entryClass)
    {
        return "public List<{$entryClass}> get{$entryClass}List (HashMap map) { return {$this->lcDaoName}.get{$entryClass}List (HashMap map); }";
    }

    function getSearchResult($entryClass)
    {
        return "public List<{$entryClass}> getSearchResult (HashMap map) { return {$this->lcDaoName}.getSearchResult(map); }";
    }

    function findById($entryClass, $pkId)
    {
        $ucPkId = ucfirst($pkId);
        return "public {$entryClass} get{$entryClass}By{$ucPkId}(int {$pkId}) { return {$this->lcDaoName}.get{$entryClass}By{$ucPkId}({$pkId}); }";
    }

    function delete($pkId)
    {
        return "public void delete(int {$pkId}) { {$this->lcDaoName}.delete($pkId); }";
    }

    function deleteByPkIds() {
        return "public void deleteByPkIds(List<Integer> ids) { {$this->lcDaoName}.deleteByPkIds(ids); } ";
    }

    function update($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "public void update({$entryClass} {$lcEntryClass}){ {$this->lcDaoName}.update($lcEntryClass); }";
    }

    function insert($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "public int insert({$entryClass} {$lcEntryClass}) { return {$this->lcDaoName}.insert($lcEntryClass);}";
    }

    function getCount($entryClass)
    {
        $lcEntryClass = lcfirst($entryClass);
        return "public int getCount({$entryClass} {$lcEntryClass}) { return {$this->lcDaoName}.getCount({$lcEntryClass}); }";
    }

    function getTotal()
    {
        return "public int getTotal() { return {$this->lcDaoName}.getTotal(); }";
    }
}

class Mapper {

    public $dao;
    public $entry;
    public $tableName;
    public $entryClassName;
    public $daoClassName;
    public $pkId;
    public $sqlPkId;
    public $data;

    function Mapper($tableName, $entryClassName, $sqlPkId, $pkId, $data) {
        $this->dao = new Dao();
        $this->entry = new Entry();
        $this->tableName = $tableName;
        $this->entryClassName = $entryClassName;
        $this->daoClassName = $this->dao->getDaoName($entryClassName);
        $this->sqlPkId = $sqlPkId;
        $this->pkId = $pkId;
        $this->data = $data;
    }

    function getMapper()
    {
        $string = $this->xmlStart();
        $string .= $this->getMapperDao();
        $string .= $this->getResultMap();
        $string .= $this->getList();
        $string .= $this->findById();
        $string .= $this->delete();
        $string .= $this->update();
        $string .= $this->insert();
        $string .= $this->getCount();
        $string .= $this->getTotal();
        $string .= $this->xmlEnd();
        return $string;
    }

    function getMapperDao() {
        return "<mapper namespace=\"{$this->daoClassName}\">\n";
    }

    function getResultMap() {
        $string = " <resultMap type=\"{$this->entryClassName}\" id=\"{$this->entryClassName}ResultMap\">";
        foreach ((array)$this->data as $row) {
            $filedName = $row['name'];
            $entryFiledName = $this->entry->nameToFiledName($filedName);
            $string .= "<id column=\"{$filedName}\" property=\"{$entryFiledName}\"/>";
        }
        $string .= "</resultMap>\n";
        return $string;
    }

    function getList()
    {
        return " <select id=\"get{$this->entryClassName}List\" resultMap=\"{$this->entryClassName}ResultMap\">
        SELECT * FROM {$this->tableName} ORDER BY {$this->sqlPkId} DESC limit #{limit} offset #{offset} </select>\n";
    }

    function findById()
    {
        $ucPkId = ucfirst($this->pkId);
        return "  <select id=\"get{$this->entryClassName}By{$ucPkId}\" resultMap=\"{$this->entryClassName}ResultMap\">
        SELECT * FROM {$this->tableName} WHERE {$this->sqlPkId} = #{{$this->pkId}}</select>\n";
    }

    function delete()
    {
        return "<delete id=\"delete\">
        DELETE FROM {$this->tableName} WHERE {$this->sqlPkId} = #{{$this->pkId}}
    </delete>\n";
    }

    function update()
    {
        $string = " <update id=\"update\"> UPDATE {$this->tableName}
<set> ";
        foreach ($this->data as $key => $row) {
            if ($row['pk'] != 1) {
                $filedName = $row['name'];
                $entryFiledName = $this->entry->nameToFiledName($filedName);
                if ($row['type'] == "int") {
                    $string .= "<if test=\"{$entryFiledName} > 0\">{$filedName}=#{{$entryFiledName}},</if>";
                } else {
                    $string .= "<if test=\"{$entryFiledName} != null\">{$filedName}=#{{$entryFiledName}},</if>";
                }
            }
         }
        $string .= "</set>";
        $string .= "WHERE {$this->sqlPkId} = #{{$this->pkId}}  </update>\n";
        return $string;
    }

    function insert()
    {
        $string = "<insert id=\"insert\" useGeneratedKeys=\"true\">
        INSERT INTO {$this->tableName} ";
        foreach ($this->data as $key => $row) {
            if ($row['pk'] != 1) {
                $filedName = $row['name'];
                $entryFiledName = $this->entry->nameToFiledName($filedName);
                $filedNameList[] = $filedName;
                $entryFiledNameList[] = $entryFiledName;
            }
        }
        $string .= " ( ". implode(" , " , $filedNameList) . " ) VALUE  ( ";
        $length = count($filedNameList) - 1;
        foreach ($entryFiledNameList as $key => $value) {
            if ($key == $length) {
                $string .= "#{" . $value . "}";
            } else {
                $string .= "#{" . $value . "} , ";
            }
        }
        $string .= " ) ";
        $string .= "</insert>\n";
        return $string;
    }

    function getCount()
    {
        $string = " <select id=\"getCount\" resultType=\"int\"> 
                       SELECT count(*) FROM {$this->tableName} <where> ";
        foreach ($this->data as $key => $row) {
            $filedName = $row['name'];
            $entryFiledName = $this->entry->nameToFiledName($filedName);

            if ($row['pk'] != 1) {
                $string .= " <if test=\"{$entryFiledName} != null\">
                {$filedName} = #{{$entryFiledName}}
                     </if>";
            } else {
                $string .= "<if test=\"{$entryFiledName} > 0\">
                AND {$filedName} <![CDATA[<]]><![CDATA[>]]> #{{$entryFiledName}}
            </if>";
            }
        }

        $string .= " </where>
    </select>";
        return $string;
    }

    function getTotal()
    {
        return "<select id=\"getTotal\" resultType=\"int\">
        SELECT count(*) FROM {$this->tableName}
        </select>\n";
    }


    function xmlStart() {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>

<!DOCTYPE mapper PUBLIC \"-//mybatis.org//DTD Mapper 3.0//EN\"
        \"http://mybatis.org/dtd/mybatis-3-mapper.dtd\">";
    }
    
    function xmlEnd() {
        return "</mapper>";
    }
}


