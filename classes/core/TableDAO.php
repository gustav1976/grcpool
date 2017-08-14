<?php
abstract class TableDAO extends DAO {
	
	protected $_database;
    protected $_table;
   	protected $_model;
   	protected $_primaryKey;
   	protected $_fields;
   	
   	public function getFullTableName() {return $this->_database.'.'.$this->_table;}
   	public function getDatabase() {return $this->_database;}
	public function getTableName() {return $this->_table;}
	public function getPrimaryKey() {return $this->_primaryKey;}
	public function getFields() {return $this->_fields;}
	public function getModel() {return $this->_model;}
	
	public function toXml($model) {
		$result = '';
		foreach ($this->_fields as $field => $info) {
			$result .= '<'.$field.'><![CDATA['.$model->{'get'.$field}().']]></'.$field.'>';	
		}
		return $result;
	}
    public function toArray($model) {
    	$result = array();
		foreach ($this->_fields as $field => $info) {
			$result[$field] = $model->{'get'.$field}();
		}
		return $result;
    }
    public function getCount($wheres = array()) {
    	$sql = 'select count(*) as howMany from '.$this->getFullTableName().' where 0 = 0 '.$this->getWhereSql($wheres);
    	$statement = $this->getDb()->prepare($sql);
       	//if (Utils_Server::isDevelopment()) {echo '<div style="width:400px;font-size:11px;background-color:lightyellow;padding:5px;border-bottom:1px solid black;">'.$sql.'</div>';}    	
    	$this->getWhereBind($statement,$wheres);
    	$this->execute($statement);
    	$result = $statement->fetch(PDO::FETCH_ASSOC);
    	return $result['howMany'];
    }
    public function fetch($wheres=array(),$orderBys = array(),$limit = array()) {
		$items = $this->fetchAll($wheres,$orderBys,$limit);
		if ($items) {
			return array_pop($items);
		} else {
			return null;
		}
    }
    public function fetchAll($wheres=array(),$orderBys = array(),$limit = array()) {
    	$sql = 'select '.$this->getSelectFields($this).' from '.$this->getFullTableName().' where 0 = 0 '.$this->getWhereSql($wheres);
        if ($orderBys) {
			$orderBy = '';
			foreach ($orderBys as $idx => $order) {
				if ($orderBy != '') $orderBy .= ',';
				$orderBy .= $idx.' '.$order;
			}
			$sql .= ' order by '.$orderBy.' ';
    	}
    	if ($limit) {
    		if (is_array($limit)) {
	    		$sql .= ' limit '.implode(',',$limit).' ';	
    		} else {
    			$sql .= ' limit '.$limit.' ';	
    		}
    	}    	 	
       	//if (Utils_Server::isDevelopment()) {echo '<div style="width:400px;font-size:11px;background-color:lightyellow;padding:5px;border-bottom:1px solid black;">'.$sql.'</div>';}       	
    	$statement = $this->getDb()->prepare($sql);  
    	$this->getWhereBind($statement,$wheres);
   		$this->execute($statement);
    	return $this->initWithRows($statement->fetchAll(PDO::FETCH_ASSOC));    		    	
    }
    public function fetchAllWithPkInnerJoin($joins,$wheres = array(),$orderBys = array(),$limit = array()) {
    	$daos = array();
    	$daos[get_class($this)] = $this;
       	foreach ($joins as $join) {
			$daos[get_class($join)] = $join;
    	}   
    	$sql = 'select '; 	    	
    	$first = true;
		foreach ($daos as $dao) {
			if (!$first) $sql .= ',';
			$sql .= $this->getSelectFields($dao);	
			$first = false;
		}
		$sql .= ' from ';
		$first = true;
		foreach ($daos as $dao) {
			if (!$first) $sql .= ', ';
			$sql .= $dao->getFullTableName();
			$first = false;
		}
		$sql .= ' where 0 = 0 ';
		foreach ($joins as $field => $join) {
			$sql .= ' and '.$join->getFullTableName().'.'.$field.' = '.$this->getFullTableName().'.'.$join->getPrimaryKey().' ';
		}
		$sql .= $this->getWhereSql($wheres);
    	$statement = $this->getDb()->prepare($sql);
    	$this->getWhereBind($statement,$wheres);    	
		return $this->initWithStatementAndDaos($daos,$statement);
    }    
	private function initWithRows($rows) {
		$objects = array();
		foreach ($rows as $id => $row) {
			$object = $this->initWithRow($row);
			if ($this->_primaryKey == "") {
				$objects[$id] = $object;
			} else {
				$objects[$row[$this->_primaryKey]] = $object;
			}
		}
		return $objects;
	}
	private function initWithRow($row) {
		$object = new $this->_model;
		if ($row) {
			foreach ($row as $field => $value) {
				if (method_exists($object,'set'.$field)) {
					$object->{'set'.$field}($value);
				}
			}
		}
		return $object;		
	}    
	private function initWithStatementAndDaos($daos,$statement) {
		$this->execute($statement);
		$tableNames = array();
		foreach ($daos as $idx => $dao) {
			$tableNames[$dao->getTableName()] = $idx;
		}
		foreach(range(0, $statement->columnCount() - 1) as $column_index) {
	 		$metas[] = $statement->getColumnMeta($column_index);
		}
		$result = array();
		while($row = $statement->fetch(PDO::FETCH_NUM)) {
			$objects = array();					
			foreach ($daos as $idx => $dao) {
				$objects[$dao->getModel()] = new $dao->_model;
			}
			$pkValue = '';
 			foreach($row as $column_index => $column_value) {
 				$meta = $metas[$column_index];
 				$daoName = $tableNames[$meta['table']];
 				$dao = $daos[$daoName];
 				if ($meta['name'] == $this->getPrimaryKey()) {
 					$pkValue = $column_value;	
 				}
				if (method_exists($objects[$dao->getModel()],'set'.$meta['name'])) {
					$objects[$dao->getModel()]->{'set'.$meta['name']}($column_value);
				}
			}
			$result[$pkValue] = $objects; 
			//array_push($result,$objects);
		}
		return $result;
	}
	public function where($field,$value,$operator = '=',$dao = null) {
		if ($dao == null) $dao = $this;
		return array('field'=>$field,'value'=>$value,'operator'=>$operator,'dao'=>$dao);
	}
	public function getWhereBind($statement,$wheres) {
    	foreach ($wheres as $idx => $where) {
    		if (is_array($where['value'])) {
    			continue;
    		}
	    	$statement->bindValue(':'.str_replace(".","_",$where['dao']->getFullTableName().'.'.$where['field']).'_'.$idx,$where['value']);	
    	}
	}
    public function getWhereSql($wheres) {
    	$sql = '';
    	foreach ($wheres as $idx => $where) {
    		if (is_array($where['value'])) {
    			foreach ($where['value'] as $idx => $val) {
    				$where['value'][$idx] = addslashes($val);	
    			}
				$sql .= ' and '.$where['dao']->getFullTableName().'.'.$where['field'] .' in (\''.implode("','",$where['value']).'\')';
    		} else {
				$sql .= ' and '.$where['dao']->getFullTableName().'.'.$where['field'] .' '.$where['operator'].' :'.str_replace(".","_",$where['dao']->getFullTableName().'.'.$where['field']).'_'.$idx;
    		}
    	}
		return $sql;    	
    }
    public function getSelectFields($dao) {
    	$sql = '';
    	$first = true;
    	foreach ($dao->getFields() as $field => $info) {
			if (!$first) $sql .= ', ';
			$sql .= $dao->getTableName().'.'.$field;
			$first = false;
    	}
    	return $sql;
    }
	public function save(&$object) {
		if ($object->{'get'.$this->_primaryKey}() == 0) {
			return $this->insert($object);
		} else {
			return $this->update($object);
		}
	}
	public function delete($object) {
		$keyValue = $object->{'get'.$this->_primaryKey}();
		$sql = 'delete from '.$this->_database.'.'.$this->_table.' where '.$this->_primaryKey.' = :keyValue';
		$statement = $this->getDb()->prepare($sql);
		$statement->bindValue(':keyValue',$keyValue);
		$this->execute($statement);
		return !$this->isError();
	}
	public function insert(&$object) {
		$sql = 'insert into '.$this->_database.'.'.$this->_table.' (';
		$first = true;
		foreach ($this->_fields as $field => $arr) {
			if ($field == $this->_primaryKey) continue;
			if ($first) {$first = false;} else {$sql .= ', ';}
			$sql .= $field;
		}
		$sql .= ') values (';
		$first = true;
		foreach ($this->_fields as $field => $arr) {
			if ($field == $this->_primaryKey) continue;
			if ($first) {$first = false;} else {$sql .= ', ';}
			$sql .= ':'.$field;
		}
		$sql .= ')';
		$statement = $this->getDb()->prepare($sql);
		foreach ($this->_fields as $field => $arr) {
			if ($field == $this->_primaryKey) continue;
			switch ($arr['type']) {
				case 'INT' : $type = PDO::PARAM_INT;break;
				case 'STRING' : $type = PDO::PARAM_STR;break;
			}
			$statement->bindValue(':'.$field,$object->{'get'.$field}());
		}
		$this->execute($statement);
		if ($this->isError()) {
			return false;
		} else {
			$object->{'set'.$this->_primaryKey}($this->getDb()->lastInsertId()); 
			return true;
		}
	}
	public function update($object) {
		$sql = 'update '.$this->_database.'.'.$this->_table.' set ';
		$first = true;
		foreach ($this->_fields as $field => $arr) {
			if ($field == $this->_primaryKey) continue;
			if ($first) {$first = false;} else {$sql .= ', ';}
			$sql .= $field .' = :'.$field.' ';
		}
		$sql .= 'where '.$this->_primaryKey.' = :keyValue';
		//echo $sql;
		$statement = $this->getDb()->prepare($sql);
		foreach ($this->_fields as $field => $arr) {
			if ($field == $this->_primaryKey) continue;
			switch ($arr['type']) {
				case 'INT' : $type = PDO::PARAM_INT;break;
				case 'STRING' : $type = PDO::PARAM_STR;break;
			}
			$statement->bindValue(':'.$field,$object->{'get'.$field}());
		}
		$statement->bindValue(':keyValue',$object->{'get'.$this->_primaryKey}());
		$this->execute($statement);
		return !$this->isError();
	} 
	public function initWithKeys($keys,$orderBys = array()) {
		if ($keys) {
			$sql = 'select * from '.$this->_database.'.'.$this->_table.' where '.$this->_primaryKey.' in ('.implode(",",$keys).')';
			if ($orderBys) {
				$orderBy = '';
				foreach ($orderBys as $idx => $order) {
					if ($orderBy != '') $orderBy .= ',';
					$orderBy .= $idx.' '.$order;
				}
				$sql .= ' order by '.$orderBy.' ';
			}
			$statement = $this->getDb()->prepare($sql);
			$this->execute($statement);
			return $this->initWithRows($statement->fetchAll(PDO::FETCH_ASSOC));
		} else {
			return array();
		}
	}
	public function initWithKey($keyValue) {
		$sql = 'select * from '.$this->_database.'.'.$this->_table.' where '.$this->_primaryKey.' = :keyValue';
		$statement = $this->getDb()->prepare($sql);
		$statement->bindValue(':keyValue',$keyValue);
		$this->execute($statement);
		return $this->initWithRow($statement->fetch(PDO::FETCH_ASSOC));
	}
    public function queryObjects($sql) {
    	$statement = $this->getDb()->prepare($sql);
    	$this->execute($statement);
		return $this->initWithRows($statement->fetchAll(PDO::FETCH_ASSOC));    	
    }
    
    public function fetchObj($objs,$where) {
    	foreach ($objs as $obj) {
    		$match = true;
    		foreach ($where as $w) {
    			if (call_user_func_array(array($obj,'get'.$w['field']), array()) !== $w['value']) {
    				$match = false;
    				break;
    			}
    		}
    		if ($match) {
    			return $obj;
    		}
    	}
    	return null;
    }
    
    public function truncate() {
    	$this->executeQuery("truncate table ".$this->getFullTableName());
    }
	
}