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
    public $belongsToTaxonomies;
	
    public function __construct()
    {
        $this->guid = 0;
		$this->revisionId = 0;
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
        
		if ($this->guid == 0)
        {
			$newGuid = DB::select('MAX(guid) as maxGuid')->from('entities')->execute()->get('maxGuid');
			$newGuid++;
			$data = array(
				'guid'	=> $newGuid
			);
			list($revisionId, $affectedRows) = DB::insert('entities',array_keys($data))->values($data)->execute();
			$this->guid = $newGuid;
			$this->revisionId = $revisionId;
			$newRevision = false;
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
                list($revisionId, $affectedRows) = DB::insert('entities', array_keys($data))->values($data)->execute();
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
            DB::update('entities')->set(array('revisionStatus'=>self::REVISIONSTATUS_OUTDATED))
                ->where('guid','=',$this->guid)->and_where('revisionId','!=',$this->revisionId)
                ->execute();
            
            //  store meta data
            $this->saveMeta(false, $oldRevisionId);
        }
        else
        {
            DB::update('entities')->set($data)->where('guid','=',$this->guid)->and_where('revisionId','=',$this->revisionId)->execute();
			$this->saveMeta(true, $this->revisionId);
        }
    }
	
	/**
	 * Gets meta data for the entity.
	 * @param string $metaKey Optional. The meta key; leave blank to retrieve all meta data.
	 * @return mixed The meta data, null on failure.
	*/
	public function getMeta($metaKey = '')
	{
		$query = DB::select()->from('entities_meta')->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)->and_where('autoload','=',0);
		$query->join('entities_metakeys')->on('entities_meta.metaKey','=','entities_metakeys.metaKey');
		$query->order_by('metaId', 'ASC');
		if ($metaKey != '')
		{
			$query->where('entities_metakeys.metaKeyName','=',$metaKey);
		}
		$rows = $query->execute();

		$ret = array();
		foreach($rows as $row)
		{
			if (array_key_exists($row['metaKeyName'], $ret))
			{
				if (!is_array($ret[$row['metaKeyName']]))
					$ret[$row['metaKeyName']] = array($ret[$row['metaKeyName']]);
				$ret[$row['metaKeyName']][] = $row['metaValue'];
			}
			else
				$ret[$row['metaKeyName']] = $row['metaValue'];
		}

		if ($metaKey == '')
			return $ret;
		return array_shift($ret);
	}
	
	/**
	 * Sets meta data for the entity.
	 * @param string $metaKey Meta key.
	 * @param mixed $metaValue Meta value. Objects and non-numerically indexed arrays will be serialized and will not be searchable.
	*/
	public function setMeta($metaKey, $metaValue)
	{
		$serialize = false;
		if (is_object($metaValue))
			$serialize = true;
		else if (is_array($metaValue))
		{
			$keys = array_keys($metaValue);
			foreach($keys as $key)
			{
				if (!is_numeric($key))
				{
					$serialize = true;
					break;
				}
			}
		}
		else
			$metaValue = array($metaValue);
		
		$metaKeyId = self::getMetaKeyId($metaKey);
		
		DB::delete('entities_meta')->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)
			->and_where('metaKey','=',$metaKeyId)->execute();
		foreach($metaValue as $v)
		{
			$data = array(
				'entityGuid'    => $this->guid,
				'entityRevision'=> $this->revisionId,
				'autoload'      => 0,
				'metaKey'       => $metaKeyId,
				'metaValue'     => $v
			);
			DB::insert('entities_meta', array_keys($data))->values($data)->execute();
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
			DB::delete('entities_meta')->where('entityGuid','=',$this->guid)->and_where('entityRevision','=',$this->revisionId)->execute();
		}
		
		//  save non-autoload meta data
		foreach($allMeta as $key => $val)
		{
			$metaKeyId = self::getMetaKeyId($key);
			if (!is_array($val))
				$val = array($val);
			foreach($val as $v)
			{
				$data = array(
					'entityGuid'    => $this->guid,
					'entityRevision'=> $this->revisionId,
					'autoload'      => 0,
					'metaKey'       => $metaKeyId,
					'metaValue'     => $v
				);
				DB::insert('entities_meta', array_keys($data))->values($data)->execute();
			}
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
					foreach($this->{$metaKey} as $val)
					{
						$data = array(
							'entityGuid'    => $this->guid,
							'entityRevision'=> $this->revisionId,
							'autoload'      => 1,
							'metaKey'       => $metaKeyId,
							'metaValue'     => $val
						);
						DB::insert('entities_meta', array_keys($data))->values($data)->execute();
					}
				}
				else
				{
					$data = array(
						'entityGuid'    => $this->guid,
						'entityRevision'=> $this->revisionId,
						'autoload'      => 1,
						'metaKey'       => $metaKeyId,
						'metaValue'     => $this->{$metaKey}
					);
					DB::insert('entities_meta', array_keys($data))->values($data)->execute();
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
        $rows = DB::select()->from('entities_metakeys')->where('metaKeyName','=',$keyName)->execute();
        foreach($rows as $row)
            return $row['metaKey'];
        list($insertId, $affectedRows) = DB::insert('entities_metakeys', array('metaKeyName'))->values(array('metaKeyName'=>$keyName))->execute();
        return $insertId;
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
        $query = DB::select('e.guid','e.authorGuid','e.authoredDateTime','e.entityType','e.revisionId','e.revisionStatus')
            ->from(array("entities","e"))->where('revisionStatus','=',$revisionStatus);
		
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
                
            $i = 0;
            foreach($metaKey as $key)
            {
                $query->join(array('entities_meta', 'm_'.$i))->on('guid','=','m_'.$i.'.entityGuid')->on('revisionId','=','m_'.$i.'.entityRevision');
                $query->join(array('entities_metakeys', 'mk_'.$i))->on('m_'.$i.'.metaKey','=','mk_'.$i.'.metaKey');
                if ($i == 0)
                    $query->where('mk_'.$i.'.metaKeyName','=',$key);
                else
                    $query->and_where('mk_'.$i.'.metaKeyName','=',$key);
                $i++;
            }
            
            $i = 0;
            foreach($metaValue as $val)
            {
                $query->and_where('m_'.$i.'.metaValue','=',$val);
                $i++;
            }
            
            if ($doneGuidMatch)
                $query->and_where_close();
        }
		
        if ($count > 0)
            $query->limit($count);
        if ($offset > 0)
            $query->offset($offset);
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
        $rows = $query->execute();

        $ret = array();
        $keys = array();
        $i = 0;
        $query = DB::select('mk.metaKeyName','m.metaValue', 'm.entityGuid', 'm.entityRevision')->from(array("entities_meta","m"))->
            join(array('entities_metakeys', 'mk'))->on('m.metaKey','=','mk.metaKey')->
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
                if ($obj->{$row['metaKeyName']} != null)
                {
					if (!is_array($obj->{$row['metaKeyName']}))
						$obj->{$row['metaKeyName']} = array($obj->{$row['metaKeyName']});
                    $obj->{$row['metaKeyName']}[] = $row['metaValue'];
                }
                else
                    $obj->{$row['metaKeyName']} = $row['metaValue'];
            }
        }
        return $ret;
    }
}