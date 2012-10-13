<?php

/**
 * Primitive entity used for working with the entities tables.
 * @abstract
*/
abstract class Entity extends Model
{
    /**
    */
    const REVISIONSTATUS_ACTIVE     = 1;
    const REVISIONSTATUS_OUTDATED   = 2;
    
    /**
     * @var int Revision ID for the entity. Higher is newer.
    */
    public $revisionId;
    /**
     * @var int Revision status.
    */
    public $revisionStatus;
    /**
     * @var int Globally unique identifier.
    */
    public $guid;
    /**
     * @var int GUID of the user who authored the entity.
    */
    public $authorGuid;
    /**
     * @var int Unix timestamp of when the entity was authored.
    */
    public $authoredDateTime;
    /**
     * @var string Entity datatype.
    */
    public $entityType;
    
    /**
     * @var array Array of taxonomy instances the entity belongs to.
    */
    public $belongsToTaxonomies = array();
	
    public function __construct()
    {
        $this->guid = 0;
	$this->revisionId = 0;
    }
    
    /**
     * Gets a single property. In the case of an array the first element is returned.
     * @param string $propertyName
     * @return mixed Property value on success, null on failure.
    */
    public function getProperty($propertyName)
    {
        if (isset($this->{$propertyName}))
        {
            if (is_array($this->{$propertyName}))
            {
                return $this->{$propertyName}[0];
            }
            return $this->{$propertyName};
        }
        return null;
    }
	
    /**
     * Gets all revisions of the entity.
     * @return array
    */
    public function getRevisions($metaKey = null, $metaValue = null, $count = 0, $offset = 0)
    {
	$key = array('guid');
	$value = array($this->guid);
	if ($metaKey != null && $metaValue != null)
	{
	    if (is_array($metaKey) && is_array($metaValue))
	    {
		foreach($metaKey as $k)
		    $key[] = $k;
		foreach($metaValue as $v)
		    $value[] = $v;
    	    }
	    else
	    {
		$key[] = $metaKey;
		$value[] = $metaValue;
	    }
	}
	return Entity::getByMeta($key, $value, $count, $offset, get_class($this), self::REVISIONSTATUS_OUTDATED);
    }
    
    /**
     * Saves the entity to the database.
     * @param bool $newRevision If false update the current record for the entity, if true create a new record and deactive the current record. Defaults to true.
    */
    public function save($newRevision = true)
    {
        if ($this->entityType == null || $this->entityType == '')
            $this->entityType = get_class($this);
        if ($this->revisionStatus == null || $this->revisionStatus < 1)
            $this->revisionStatus = self::REVISIONSTATUS_ACTIVE;
        
        $newEntity = false;
	if ($this->guid == 0)
        {
	    $newGuid = DB::select('MAX(guid) as maxGuid')->from(Core::$activeStore->tables->entity)->execute()->get('maxGuid');
	    $newGuid++;
	    $data = array(
		'guid'	=> $newGuid
	    );
	    list($revisionId, $affectedRows) = DB::insert(Core::$activeStore->tables->entity,array_keys($data))->values($data)->execute();
	    $this->guid = $newGuid;
	    $this->revisionId = $revisionId;
	    $newRevision = false;
            $newEntity = true;
        }
        
	$data = array(
	    'guid'              => $this->guid,
	    'authorGuid'        => $this->authorGuid,
	    'authoredDateTime'  => $this->authoredDateTime,
	    'entityType'        => $this->entityType,
	    'revisionStatus'    => $this->revisionStatus
	);
        if ($newRevision)
        {
            $revisionId = -1;
            try
            {
                list($revisionId, $affectedRows) = DB::insert(Core::$activeStore->tables->entity, array_keys($data))->values($data)->execute();
            }
            catch(Exception $ex)
            {
            }
            //  failed to insert
            if ($revisionId < 0)
                return;
            
	    $oldRevisionId = $this->revisionId;
            $this->revisionId = $revisionId;
            
            //  update old records to be inactive
            DB::update(Core::$activeStore->tables->entity)->set(array('revisionStatus'=>self::REVISIONSTATUS_OUTDATED))
                ->where('guid','=',$this->guid)->and_where('revisionId','!=',$this->revisionId)
                ->execute();
            
            //  store meta data
            $this->saveMeta(false, $oldRevisionId);
            
            Hooks::doAction("update_entity_".$this->entityType, $this);
        }
        else
        {
            DB::update(Core::$activeStore->tables->entity)->set($data)->where('guid','=',$this->guid)->and_where('revisionId','=',$this->revisionId)->execute();
	    $this->saveMeta(true, $this->revisionId);
            
            if ($newEntity)
            {
                Hooks::doAction("new_entity_".$this->entityType, $this);
            }
            else
            {
                Hooks::doAction("review_entity_".$this->entityType, $this);
            }
        }
    }
	
    /**
     * Gets meta data for the entity.
     * @param string $metaKey Optional. The meta key; leave blank to retrieve all meta data.
     * @return mixed The meta data, null on failure.
    */
    public function getMeta($metaKey = '')
    {
	$query = DB::select()->from(Core::$activeStore->tables->entityMeta)->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)->and_where('autoload','=',0);
	$query->join(Core::$activeStore->tables->entityMetaKeys)->on(Core::$activeStore->tables->entityMeta.'.metaKey','=',Core::$activeStore->tables->entityMetaKeys.'.metaKey');
	$query->order_by('metaId', 'ASC');
	if ($metaKey != '')
	{
	    $query->where(Core::$activeStore->tables->entityMetaKeys.'.metaKeyName','=',$metaKey);
	}
	$rows = $query->execute();

	$ret = '';
	foreach($rows as $row)
	{
	    $val = $row['metaStrValue'];
	    if ($val == null || $val == '')
		$val = $row['metaIntValue'];
	    if ($row['metaArrayKey'] != null && $row['metaArrayKey'] != '')
	    {
		if (!is_array($ret))
		{
		    $ret = array();
		}
		$ret[$row['metaArrayKey']] = $val;
	    }
	    else
		$ret = $val;
	}

	return $ret;
    }
	
    /**
     * Sets meta data for the entity.
     * @param string $metaKey Meta key.
     * @param mixed $metaValue Meta value. Objects will be serialized and will not be searchable.
    */
    public function setMeta($metaKey, $metaValue)
    {
	$serialize = false;
	if (is_object($metaValue))
	    $serialize = true;
	
	$metaKeyId = self::getMetaKeyId($metaKey);
		
	DB::delete(Core::$activeStore->tables->entityMeta)->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)
	    ->and_where('metaKey','=',$metaKeyId)->execute();
	    
	if (is_array($metaValue))
	{
	    foreach($metaValue as $index => $v)
	    {
		$field = 'metaStrValue';
		if (self::isInteger($v))
		    $field = 'metaIntValue';
		$data = array(
		    'entityGuid'    => $this->guid,
		    'entityRevision'=> $this->revisionId,
		    'autoload'      => 0,
		    'metaKey'       => $metaKeyId,
		    $field     	    => $v,
		    'metaArrayKey'  => $index
		);
		DB::insert(Core::$activeStore->tables->entityMeta, array_keys($data))->values($data)->execute();
	    }
	}
	else
	{
	    $field = 'metaStrValue';
	    if (self::isInteger($metaValue))
		$field = 'metaIntValue';
	    $data = array(
		'entityGuid'    => $this->guid,
		'entityRevision'=> $this->revisionId,
		'autoload'      => 0,
		'metaKey'       => $metaKeyId,
		$field     	=> $metaValue
	    );
	    DB::insert(Core::$activeStore->tables->entityMeta, array_keys($data))->values($data)->execute();
	}
    }
    
    private function saveMeta($overwrite, $oldRevisionId)
    {
	//  load any non-autoload meta data
	$revisionIdBackup = $this->revisionId;
	$this->revisionId = $oldRevisionId;
	$allMeta = $this->getMeta();
	$this->revisionId = $revisionIdBackup;
		
	if ($overwrite)
	{
	    //  delete the existing metadata
	    DB::delete(Core::$activeStore->tables->entityMeta)->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)->execute();
	}
		
	//  save non-autoload meta data
	foreach($allMeta as $key => $val)
	{
	    $this->setMeta($key, $val);
	    /*
	    $metaKeyId = self::getMetaKeyId($key);
	    if (!is_array($val))
		$val = array($val);
	    foreach($val as $v)
	    {
		$field = 'metaStrValue';
		if (self::isInteger($v))
		    $field = 'metaIntValue';
		$data = array(
		    'entityGuid'    => $this->guid,
		    'entityRevision'=> $this->revisionId,
		    'autoload'      => 0,
		    'metaKey'       => $metaKeyId,
		    $field     	    => $v
		);
		DB::insert(Core::$activeStore->tables->entityMeta, array_keys($data))->values($data)->execute();
	    }
	    */
	}
		
	//  save entity attributes as autoload meta
	$vars = get_class_vars($this->entityType);
	$defaultVars = get_class_vars("Entity");
	foreach($defaultVars as $metaKey => $metaValue)
	{
	    //  special var that has to be allowed to save
	    if ($metaKey == 'belongsToTaxonomies')
		continue;
	    if (array_key_exists($metaKey, $vars))
		unset($vars[$metaKey]);
	}
	foreach($vars as $metaKey => $metaValue)
	{
	    $metaKeyId = self::getMetaKeyId($metaKey);
	    if ($this->{$metaKey} != null)
	    {
		if (is_array($this->{$metaKey}))
		{
		    foreach($this->{$metaKey} as $k => $val)
		    {
			$field = 'metaStrValue';
			if (self::isInteger($val))
			{
			    $field = 'metaIntValue';
			}
			$data = array(
			    'entityGuid'    => $this->guid,
			    'entityRevision'=> $this->revisionId,
			    'autoload'      => 1,
			    'metaKey'       => $metaKeyId,
			    $field     	    => $val,
			    'metaArrayKey'  => $k
			);
			DB::insert(Core::$activeStore->tables->entityMeta, array_keys($data))->values($data)->execute();
		    }
		}
		else
		{
		    $val = $this->{$metaKey};
		    $field = 'metaStrValue';
		    if (self::isInteger($val))
		    {
			$field = 'metaIntValue';
		    }
		    $data = array(
			'entityGuid'    => $this->guid,
			'entityRevision'=> $this->revisionId,
			'autoload'      => 1,
			'metaKey'       => $metaKeyId,
			$field          => $val
		    );
		    DB::insert(Core::$activeStore->tables->entityMeta, array_keys($data))->values($data)->execute();
		}
	    }
	}
    }
    
    /**
     * Gets the ID of the given meta key. If it doesn't exist the key is created.
     * @var string $keyName
     * @return int
    */
    private static function getMetaKeyId($keyName)
    {
        $rows = DB::select()->from(Core::$activeStore->tables->entityMetaKeys)->where('metaKeyName','=',$keyName)->execute();
        foreach($rows as $row)
            return $row['metaKey'];
        list($insertId, $affectedRows) = DB::insert(Core::$activeStore->tables->entityMetaKeys, array('metaKeyName'))->values(array('metaKeyName'=>$keyName))->execute();
        return $insertId;
    }
    
    private static function isInteger($val)
    {
	if (!is_numeric($val))
	    return false;
	$check = $val + 0;
	return is_integer($check);
    }
    
    /**
     * Loads any entity from the database.
     * @param int $entityGuid GUID of the entity to load.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return Entity
    */
    public static function getByGuid($entityGuid, $type = null, $revisionStatus = self::REVISIONSTATUS_ACTIVE, $skipCache = false, $storeInCache = true)
    {
        $ret = self::getByMeta("guid", $entityGuid, 1, 0, $type, $revisionStatus, $skipCache, $storeInCache);
        if (sizeof($ret) > 0)
            return array_shift($ret);
        return null;
    }
    
    /**
     * Loads entities from the database using the given types.
     * @param int $count Maximum number of entities to return.
     * @param int $offset Offset to start entity listing at.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return array
    */
    public static function getByType($count = 20, $offset = 0, $type = null, $revisionStatus = self::REVISIONSTATUS_ACTIVE, $skipCache = false, $storeInCache = true)
    {
	return self::getByMeta(null, null, $count, $offset, $type, $revisionStatus, $skipCache, $storeInCache);
    }
    
    private static function _buildMetaQuery($query, $metaKey = null, $metaValue = null, $type = null, $revisionStatus = self::REVISIONSTATUS_ACTIVE)
    {
        $query->from(array(Core::$activeStore->tables->entity,"e"))->where('revisionStatus','=',$revisionStatus);
		
        if (!is_array($metaKey) && $metaKey != null)
	    $metaKey = array($metaKey);
        if (!is_array($metaValue) && $metaValue != null)
	    $metaValue = array($metaValue);
        
        $doneGuidMatch = false;
        if (sizeof($metaKey) > 0 && sizeof($metaValue) > 0)
        {
            $guidMatches = array();
            foreach($metaKey as $index => $key)
            {
                if ($key == "guid")
                {
                    $guidMatches[] = $metaValue[$index];
                    unset($metaKey[$index]);
                    unset($metaValue[$index]);
                }
            }
            
            if (sizeof($guidMatches) > 0)
            {
                $doneGuidMatch = true;
                $query->and_where_open();
                $i = 0;
                foreach($guidMatches as $match)
                {
                    if ($i == 0)
                        $query->where('e.guid','=',$match);
                    else
                        $query->or_where('e.guid','=',$match);
                    $i++;
                }
                $query->and_where_close();
            }
        }
		
        if (sizeof($metaKey) > 0 && sizeof($metaValue) > 0)
        {
            if ($doneGuidMatch)
                $query->and_where_open();
                
            //  metaKeys that are identical should be an OR not AND
            $keys = array();
            foreach($metaKey as $i => $key)
            {
                if (!array_key_exists($key, $keys))
                    $keys[$key] = array();
                $keys[$key][] = $metaValue[$i];
            }
            
            $i = 0;
            foreach($keys as $key => $values)
            {
                $query->join(array(Core::$activeStore->tables->entityMeta, 'm_'.$i))->on('guid','=','m_'.$i.'.entityGuid')->on('revisionId','=','m_'.$i.'.entityRevision');
                $query->join(array(Core::$activeStore->tables->entityMetaKeys, 'mk_'.$i))->on('m_'.$i.'.metaKey','=','mk_'.$i.'.metaKey');
                $query->where('mk_'.$i.'.metaKeyName','=',$key);
                $query->and_where_open();
                $j = 0;
                foreach($values as $value)
                {
                    if ($j == 0)
		    {
			if (self::isInteger($value))
			{
			    $query->where('m_'.$i.'.metaIntValue','=',$value);
			}
			else
			{
			    $query->where('m_'.$i.'.metaStrValue','=',$value);
			}
		    }
                    else
		    {
			if (self::isInteger($value))
			{
			    $query->or_where('m_'.$i.'.metaIntValue','=',$value);
			}
			else
			{
			    $query->or_where('m_'.$i.'.metaStrValue','=',$value);
			}
		    }
                    $j++;
                }
                $query->and_where_close();
                $i++;
            }

            if ($doneGuidMatch)
                $query->and_where_close();
        }
	
        if ($type != null)
        {
            if (is_array($type) && sizeof($type) > 0)
            {
                $query->and_where_open();
                $i = 0;
                foreach($type as $typeName)
                {
                    if ($i++ == 0)
                    {
                        $query->where('e.entityType','=',$typeName);
                    }
                    else
                    {
                        $query->or_where('e.entityType','=',$typeName);
                    }
                }
                $query->and_where_close();
            }
            else
            {
                $query->and_where('e.entityType','=',$type);
            }
        }
        
        return $query;
    }
    
    /**
     * Counts entities from the database using the given metadata.
     * @param mixed $metaKey Metadata key used for matching. Can also be supplied as an array.
     * @param mixed $metaValue Metadata value used for matching. Can also be supplied as an array.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @return int
    */
    public static function getCountByMeta($metaKey = null, $metaValue = null, $type = null, $revisionStatus = self::REVISIONSTATUS_ACTIVE)
    {
        $query = DB::select('COUNT(DISTINCT revisionId) AS mycount');
        $query = self::_buildMetaQuery($query, $metaKey, $metaValue, $type, $revisionStatus);
        $result = $query->execute();
        return $result->get('mycount');
    }
    
    /**
     * Loads entities from the database using the given metadata.
     * @param mixed $metaKey Metadata key used for matching. Can also be supplied as an array.
     * @param mixed $metaValue Metadata value used for matching. Can also be supplied as an array.
     * @param int $count Maximum number of entities to return.
     * @param int $offset Offset to start entity listing at.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return array
    */
    public static function getByMeta($metaKey = null, $metaValue = null, $count = 20, $offset = 0, $type = null, $revisionStatus = self::REVISIONSTATUS_ACTIVE, $skipCache = false, $storeInCache = true)
    {
        $query = DB::select('e.guid','e.authorGuid','e.authoredDateTime','e.entityType','e.revisionId','e.revisionStatus');
        $query = self::_buildMetaQuery($query, $metaKey, $metaValue, $type, $revisionStatus);
        if ($count > 0)
            $query->limit($count);
        if ($offset > 0)
            $query->offset($offset);
        $query->group_by('e.revisionId');
        
        $rows = $query->execute();

        $ret = array();
        $keys = array();
        $i = 0;
        $query = DB::select('mk.metaKeyName','m.metaStrValue', 'm.metaIntValue', 'm.metaArrayKey', 'm.entityGuid', 'm.entityRevision')->from(array(Core::$activeStore->tables->entityMeta,"m"))->
            join(array(Core::$activeStore->tables->entityMetaKeys, 'mk'))->on('m.metaKey','=','mk.metaKey')->
            where('autoload','=',1)->and_where_open();
        foreach($rows as $row)
        {
            $className = $row['entityType'];
            if (class_exists($className))
            {
                $obj = new $className();
                foreach($row as $key => $val)
                    $obj->{$key} = $val;
                $ret[] = $obj;
                $keys[$row['guid'].':'.$row['revisionId']] = $i++;
                if ($i == 1)
                    $query->where_open()->where('entityGuid','=',$row['guid'])
                        ->and_where('entityRevision','=',$row['revisionId'])->where_close();
                else
                    $query->or_where_open()->where('entityGuid','=',$row['guid'])
                        ->and_where('entityRevision','=',$row['revisionId'])->or_where_close();
            }
        }
        $query->and_where_close();
	$query->order_by('metaId', 'ASC');

        if ($i > 0)
        {
            $rows = $query->execute();
            foreach($rows as $row)
            {
                $obj = $ret[$keys[$row['entityGuid'].':'.$row['entityRevision']]];
		$val = $row['metaStrValue'];
		if ($val == null || $val == '')
		    $val = $row['metaIntValue'];
		if ($row['metaArrayKey'] != null && $row['metaArrayKey'] != '')
		{
		    if ($obj->{$row['metaKeyName']} == null)
		    {
			$obj->{$row['metaKeyName']} = array();
		    }
		    $obj->{$row['metaKeyName']}[$row['metaArrayKey']] = $val;
		}
		else
		    $obj->{$row['metaKeyName']} = $val;
            }
        }
        
        foreach($ret as $obj)
        {
            Hooks::doAction("load_entity_".$obj->entityType, $obj);
        }
        
        return $ret;
    }
}