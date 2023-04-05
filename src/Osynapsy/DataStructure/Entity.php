<?php

/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\DataStructure;

/**
 * Description of Entity
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
abstract class Entity
{
    protected $entity;
    protected $fields;
    protected $executionTriggerQueue = [];

    public function __construct(array $rawentity = [])
    {
        $this->init();
        $this->initEntity($rawentity);
    }

    protected function initEntity(array $rawentity)
    {
        $this->entity = $rawentity;
    }

    public function addField($name, $dbname, callable $trigger = null)
    {
        $this->fields[strtolower($name)] = [$dbname, $trigger];
    }

    public function __call($name, $arguments)
    {
        $action = strtolower(substr($name, 0, 3));
        $field = substr($name, 3);
        switch($action) {
            case 'get':
                return $this->getValue($field);
            case 'set':
                return $this->setValue($field, $arguments[0] ?? null);
        }
        throw new \Exception(sprintf('No method %s exists', $name));
    }

    public function getEntity()
    {
        $this->execTriggerQueue();
        return $this->entity;
    }

    protected function getValue($fieldName)
    {
        $this->executionTriggerQueue();
        return $this->entity[$this->getKey($fieldName)];
    }

    protected function setValue($fieldName, $fieldValue)
    {
        $this->entity[$this->getKey($fieldName)] = $fieldValue;
        $this->addTriggerExecutionInQueue($fieldName);
        return $fieldValue;
    }

    protected function addTriggerExecutionInQueue($rawFieldName)
    {
        $fieldName = strtolower($rawFieldName);
        if (array_key_exists($fieldName, $this->fields) && !empty($this->fields[$fieldName][1])) {
            $this->executionTriggerQueue[$rawFieldName] = $this->fields[$fieldName][1];
        }
    }

    protected function execTriggerQueue()
    {
        foreach($this->executionTriggerQueue as $trigger) {
            $trigger();
        }
        $this->executionTriggerQueue = [];
    }

    protected function getKey($key)
    {
        if (array_key_exists($key, $this->entity)) {
            return $key;
        }
        if (array_key_exists(strtolower($key), $this->fields)) {
            return $this->fields[strtolower($key)][0];
        }
        throw new \Exception(sprintf('Key %s not found in entity', $key), 404);
    }

    abstract protected function init();
}
