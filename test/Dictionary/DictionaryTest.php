<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Osynapsy\DataStructure\Dictionary;

class DictionaryTest extends TestCase
{
    private function getDictionary(array $initDictionary = []) : Dictionary
    {
        $Dictionary = new Dictionary($initDictionary);
        return $Dictionary;
    }

    public function testDictionaryGetValue(): void
    {
        $dictionary = $this->getDictionary(['a' => ['b' => 'c']]);
        $this->assertEquals($dictionary->get('a.b'), 'c');
    }

    public function testDictionaryToXml()
    {
        $dictionary = $this->getDictionary([
            'a' => [
                'b' => '1',
                'c' => '2'
            ]
        ]);
        $this->assertXmlStringEqualsXmlString(
            $dictionary->xmlSerialize('root',false),
            '<?xml version="1.0"?><root><a><b>1</b><c>2</c></a></root>'
        );
    }

    public function testDictionaryWithSpecialCharsToXml()
    {
        $dictionary = $this->getDictionary([
            'a' => [
                'b' => '1',
                'c' => '<<2'
            ]
        ]);
        $this->assertXmlStringEqualsXmlString(
            $dictionary->xmlSerialize('root',false),
            '<?xml version="1.0"?><root><a><b>1</b><c><![CDATA[<<2]]></c></a></root>'
        );
    }
}
