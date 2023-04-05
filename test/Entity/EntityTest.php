<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Osynapsy\DataStructure\Entity;

/**
 * Description of EntityTest
 *
 * @author Pietro
 */
class EntityTest extends TestCase
{
    protected $testRecord = [
        'id' => '1',
        'id_client' => '1'
    ];

    private function entityFactory()
    {
        $class = (new class extends Entity
        {
            protected function init()
            {
                $this->addField('id', 'id');
                $this->addField('clientId', 'id_client', [$this, 'testEvent']);
                $this->addField('eventId', 'id_event');
            }

            public function testEvent()
            {
                $this->setEventId('1');
            }
        });
        return new $class($this->testRecord);
    }

    public function testEntity()
    {
        $handle = $this->entityFactory();
        $this->assertEquals($this->testRecord, $handle->getEntity());
    }

    public function testEntitySetValue()
    {
        $handle = $this->entityFactory();
        $handle->setId('2');
        $handle->setClientId('2');
        $this->assertEquals(['id' => '2', 'id_client' => '2', 'id_event' => '1'], $handle->getEntity());
    }
}
