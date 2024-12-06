<?php

namespace tfphp\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfmodel;

class tffastCRUDModel{
    private tfmodel $model;
    private tfdo $tfdo;
    private tfdao $tfdao;
    public function __construct(tfmodel $model, tfdo $tfdo, tfdao $tfdao){
        $this->model = $model;
        $this->tfdo = $tfdo;
        $this->tfdao = $tfdao;
    }
    public function getLocate(int $id, string $idFieldName, string $pidFieldName): array{
        $items = [];
        while($item = $this->tfdao->select([$idFieldName=>$id])){
            $items[] = $item;
            if(($id = $item[$pidFieldName]) == 0) break;
        }
        $items = array_reverse($items);
        return $items;
    }
    public function getSearch(string $sql, array $params): array{
        $ps = 10;
        $pn = ($_GET["pn"]) ? $_GET["pn"] : 1;
        // query
        $dataTotal = $this->tfdo->fetchOne3("select count(*) as cc from (". $sql. ") as tt", $params);
        $total = $dataTotal["cc"];
        // page
        $pagination = $this->tfdo->makePagination($total, $ps, $pn);
        $pagination["numFrom"] = $pagination["currentpage"]-3;
        if($pagination["numFrom"] < 1) $pagination["numFrom"] = 1;
        $pagination["numTo"] = $pagination["numFrom"]+6;
        if($pagination["numTo"] > $pagination["totalpage"]) $pagination["numTo"] = $pagination["totalpage"];
        // data
        $data = $this->tfdo->fetchMany3($sql, $params, ($pagination["currentpage"]-1)*$pagination["percentpage"], $pagination["percentpage"]);
        if($data === null) $data = [];
        return [
            "data"=>$data,
            "pagination"=>$pagination,
        ];
    }
    public function getDetail(int $id): ?array{
        // data
        $curr = $this->tfdao->keySelect([$id]);
        return $curr;
    }
    public function add(array $data){
        if(!$this->tfdao->insert($data)){
            throw new \Exception("fail to add", 1);
        }
    }
    public function addUniqueData(array $data, array $uniqueKeyNames){
        $uniqueData = [];
        foreach ($uniqueKeyNames as $uniqueKeyName) $uniqueData[$uniqueKeyName] = $data[$uniqueKeyName];
        if(count($uniqueData) == 0 || $this->tfdao->select($uniqueData)){
            throw new \Exception("unique data is empty or duplicate for unique", 1);
        }
        if(!$this->tfdao->insert($data)){
            throw new \Exception("fail to add", 2);
        }
    }
    public function modify(int $id, array $data){
        $curr = $this->tfdao->keySelect([$id]);
        if(!$curr){
            throw new \Exception("item is not exist", 1);
        }
        if(!$this->tfdao->keyUpdate([$id], $data)){
            throw new \Exception("fail to modify", 2);
        }
    }
    public function modifyUniqueData(int $id, array $data, array $uniqueKeyNames){
        $uniqueData = [];
        foreach ($uniqueKeyNames as $uniqueKeyName) $uniqueData[$uniqueKeyName] = $data[$uniqueKeyName];
        $curr = $this->tfdao->keySelect([$id]);
        if(!$curr){
            throw new \Exception("item is not exist", 1);
        }
        if(count($uniqueData) == 0 || (($dup = $this->tfdao->select($uniqueData)) && serialize($dup) != serialize($curr))){
            throw new \Exception("unique data is empty or duplicate for unique", 2);
        }
        if(!$this->tfdao->keyUpdate([$id], $data)){
            throw new \Exception("fail to modify", 3);
        }
    }
    public function remove(int $id){
        $curr = $this->tfdao->keySelect([$id]);
        if(!$curr){
            throw new \Exception("item is not exist", 1);
        }
        if(!$this->tfdao->keyDelete([$id])){
            throw new \Exception("fail to remove", 2);
        }
    }
}