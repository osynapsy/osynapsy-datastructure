<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Osynapsy\Data\Tree;

/**
 * Description of TreeTest
 *
 * @author pietr
 */
class TreeTest extends TestCase
{
    private $arrayTest = [
        ['id' => 1, 'parent' => null, 'isopen' => 0],
        ['id' => 2, 'parent' => 1, 'isopen' => 0],
        ['id' => 3, 'parent' => 2, 'isopen' => 1],
    ];

    public function getTreeObject() : Tree
    {
        return new Tree('id', 'parent', 'isopen', $this->arrayTest);
    }

    public function testResult() : void
    {
        $treeObject = $this->getTreeObject();
        $tree = $treeObject->get();
        $this->assertEquals($tree, [
            1 => ['id' => 1, 'parent' => null, 'isopen' => 1, '_level' => 0, '_position' => 3, '_childrens' => [
                2 => ['id' => 2, 'parent' => 1, 'isopen' => 1, '_level' => 1, '_position' => 3, '_childrens' => [
                    3 => ['id' => 3, 'parent' => 2, 'isopen' => 1, '_level' => 2, '_position' => 3]
                ]]
            ]]
        ]);
    }
}
