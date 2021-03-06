<?php

namespace HisInOneProxy\DataModel;

use Exception;
use HisInOneProxy\Soap\Interactions\DataCache;

/**
 * Class HisSystemResponse
 * @package HisInOneProxy\DataModel
 */
class HisSystemResponse
{
    protected static $event_types = array('create', 'update', 'delete');

    /**
     * @var string
     */
    protected $object_type;

    /**
     * @var string
     */
    protected $object_id;

    /**
     * @var string
     */
    protected $event_type;

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * @param string $object_type
     */
    public function setObjectType($object_type)
    {
        $this->object_type = $object_type;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * @param string $event_type
     * @throws Exception
     */
    public function setEventType($event_type)
    {
        $event_type = strtolower($event_type);
        if (in_array($event_type, self::$event_types)) {
            $this->event_type = $event_type;
        } else {
            if (!PHPUNIT) {
                DataCache::getInstance()->getLog()->warning(sprintf('Got illegal event type (%s) for object id (%s).', $event_type, $this->getObjectId()));
            }
        }
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * @param string $object_id
     */
    public function setObjectId($object_id)
    {
        $this->object_id = $object_id;
    }
}