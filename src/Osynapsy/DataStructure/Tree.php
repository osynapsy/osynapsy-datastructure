<?php

/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\DataStructure;

/**
 * Description of Tree
 *
 * @author Pietro
 */
class Tree
{
    const POSITION_BEGIN = 1;
    const POSITION_BETWEEN = 2;
    const POSITION_END = 3;

    private $keyId;
    private $keyParent;
    private $keyIsOpen;
    private $openNodes = [];
    private $dataSet;
    private $rawTree;
    private $tree;

    public function __construct($idKey, $parentKey, $isOpenKey = null, array $dataSet = [])
    {
        $this->keyId = $idKey;
        $this->keyParent = $parentKey;
        $this->keyIsOpen = $isOpenKey;
        $this->setDataset($dataSet);
    }

    protected function branchFactory(&$rawDataSet, $parentId = 0, $level = 0)
    {
        $branch = [];
        $lastIdx = count($rawDataSet[$parentId]) - 1;
        foreach ($rawDataSet[$parentId] as $idx => $child){
            $childId = $child[$this->keyId];
            $child['_level'] = $level;
            $child['_position'] = $this->setPosition($idx, $lastIdx);
            if(!empty($rawDataSet[$childId])){
               $child['_childrens'] = $this->branchFactory($rawDataSet, $childId, $level + 1);
               if (in_array($childId, $this->openNodes)) {
                   $child[$this->keyIsOpen] = 1;
               }
            }
            if (!empty($child[$this->keyIsOpen])) {
                $this->openNodes[] = $child[$this->keyParent];
            }
            $branch[$childId] = $child;
        }
        return $branch;
    }

    public function get()
    {
        if (!is_null($this->tree)) {
            return $this->tree;
        }
        $this->rawTreeFactory();
        $this->tree = $this->branchFactory($this->rawTree);
        return $this->tree;
    }

    public function getOpenNodes()
    {
        return $this->openNodes;
    }

    protected function rawTreeFactory()
    {
        $this->rawTree = [];
        foreach ($this->dataSet as $rec){
            $this->rawTree[$rec[$this->keyParent] ?? 0][] = $rec;
        }
    }

    public function setDataset(array $dataset)
    {
        $this->dataSet = $dataset;
    }

    /**
     * Calcolo in che posizione si trova l'elemento (In testa = 1, nel mezzo = 2, alla fine = 99);
     *
     * @param int $idx posizione dell'elemento
     * @param int $last posizione dell'ultimo elemento
     * @return int
     */
    private function setPosition($idx, $last)
    {
        if ($idx === $last) {
            return self::POSITION_END;
        }
        if (empty($idx)) {
            return self::POSITION_BEGIN;
        }
        return self::POSITION_BETWEEN;
    }
}
