<?php

/**
 * Primitive entity used for working with the entities tables.
 * @abstract
*/
abstract class Entity extends Model
{
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
     * Saves the entity to the database.
     * @param bool $newRevision If false update the current record for the entity, if true create a new record and deactive the current record. Defaults to true.
    */
    public function save($newRevision = true)
    {
        
    }
    
    /**
     * Loads any entity from the database.
     * @param int $entityGuid GUID of the entity to load.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return Entity
    */
    public static function getByGuid($entityGuid, $type = null, $skipCache = false, $storeInCache = true)
    {
        
    }
    
    /**
     * Loads entities from the database using the given metadata.
     * @param string $metaKey Metadata key used for matching.
     * @param mixed $metaValue Metadata value used for matching.
     * @param int $count Maximum number of entities to return.
     * @param int $offset Offset to start entity listing at.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return array
    */
    public static function getByMeta($metaKey, $metaValue, $count = 20, $offset = 0, $type = null, $skipCache = false, $storeInCache = true)
    {
        $query = DB::select()->from(array("entities","e"))->
            join(array('entities_meta', 'm'))->on('guid','=','entityGuid')->
            join(array('entities_metakeys', 'mk'))->on('m.metaKey','=','mk.metaKey')->
            where('metaKeyName','=',$metaKey)->and_where('metaValue','=',$metaValue)->
            offset($offset)->limit($count);
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
                $query->and_where('e.entityType','=',$typeName);
            }
        }
        $rows = $query->execute();
        $ret = array();
        $keys = array();
        $i = 0;
        $query = DB::select()->from(array("entities_meta","m"))->
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
                $keys[$row['guid']] = $i++;
                if ($i == 1)
                    $query->where('entityGuid','=',$row['guid']);
                else
                    $query->or_where('entityGuid','=',$row['guid']);
            }
        }
        $query->and_where_close();

        if ($i > 0)
        {
            $rows = $query->execute();
            foreach($rows as $row)
            {
                $obj = $ret[$keys[$row['entityGuid']]];
                $obj->{$row['metaKeyName']} = $row['metaValue'];
            }
        }
        return $ret;
    }
}