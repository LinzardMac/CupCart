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
     * @var array Array of taxonomy instances the entity belongs to.
    */
    public $belongsToTaxonomies;
    
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
     * Loads entities from the database using the given types.
     * @param int $count Maximum number of entities to return.
     * @param int $offset Offset to start entity listing at.
     * @param mixed $type Entity type restrictions. String restricts to a single type an array will restrict to multiple types.
     * @param bool $skipCache When set to true any cached instances of the entity requested will be ignore. Defaults to false.
     * @param bool $storeInCache When set to true a copy of the entity will be saved for later retreival in the cache. Defaults to true.
     * @return array
    */
    public static function getByType($count = 20, $offset = 0, $type = null, $skipCache = false, $storeInCache = true)
    {
        $query = DB::select('e.guid','e.authorGuid','e.authoredDateTime','e.entityType')->from(array("entities","e"));
        if ($count > 0)
            $query->limit($count);
        if ($offset > 0)
            $query->offset($offset);
        if ($type != null)
        {
            if (is_array($type) && sizeof($type) > 0)
            {
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
            }
            else
            {
                $query->where('e.entityType','=',$type);
            }
        }
        $rows = $query->execute();
        $ret = array();
        $keys = array();
        $i = 0;
        $query = DB::select('mk.metaKeyName','m.metaValue', 'm.entityGuid')->from(array("entities_meta","m"))->
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
                if ($obj->{$row['metaKeyName']} != null)
                {
                    $obj->{$row['metaKeyName']} = array($obj->{$row['metaKeyName']});
                    $obj->{$row['metaKeyName']}[] = $row['metaValue'];
                }
                else
                    $obj->{$row['metaKeyName']} = $row['metaValue'];
            }
        }
        return $ret;
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
    public static function getByMeta($metaKey, $metaValue, $count = 20, $offset = 0, $type = null, $skipCache = false, $storeInCache = true)
    {
        $query = DB::select('e.guid','e.authorGuid','e.authoredDateTime','e.entityType')->from(array("entities","e"));
        if (!is_array($metaKey) && !is_array($metaValue))
        {
            $query->join(array('entities_meta', 'm'))->on('guid','=','entityGuid');
            $query->join(array('entities_metakeys', 'mk'))->on('m.metaKey','=','mk.metaKey');
            $query->where('metaKeyName','=',$metaKey)->and_where('metaValue','=',$metaValue);
        }
        else if (is_array($metaKey) && is_array($metaValue))
        {
            $i = 0;
            foreach($metaKey as $key)
            {
                $query->join(array('entities_meta', 'm_'.$i))->on('guid','=','m_'.$i.'.entityGuid');
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
        $query = DB::select('mk.metaKeyName','m.metaValue', 'm.entityGuid')->from(array("entities_meta","m"))->
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
                if ($obj->{$row['metaKeyName']} != null)
                {
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