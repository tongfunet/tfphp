<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfcrudBuilder{
    protected tfphp $tfphp;
    private ?int $lastInsertAutoIncrementValue;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->lastInsertAutoIncrementValue = null;
    }
    public function buildDetail(tfdao $dao, array $query, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // database process
        $data = $dao->select($query);
        return [
            "data"=>($data) ? $data : []
        ];
    }
    public function buildList(tfdao $dao, string $sql, array $params, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["pagination"])) $options["pagination"] = [];
        // make sql
        $gets = $this->tfphp->getRequest()->get();
        $sortArr = json_decode($gets->get("sorts"), true);
        if(is_array($sortArr)){
            $ob = "";
            foreach ($sortArr as $sK => $sV) if($sV) $ob .= ", ". $sK. " ". $sV;
            if($ob) $sql .= " order by ". substr($ob, 2);
        }
        // query data
        $tfdo = $dao->getTFDO();
        $totalInfo = $tfdo->fetchOne3(preg_replace("/select[\s\t\r\n]+.*[\s\t\r\n]+from/i", "select count(*) as total from", $sql), $params);
        $total = $totalInfo["total"];
        $percentPage = (isset($options["pagination"]["ps"])) ? intval($options["pagination"]["ps"]) : 10;
        $currentPage = intval($gets->get("pn"));
        $pagination = $tfdo->makePagination($total, $percentPage, $currentPage);
        $data = $tfdo->fetchMany3($sql, $params, $pagination["seekbegin"], $pagination["fetchnums"]);
        return [
            "data"=>$data,
            "pagination"=>$pagination
        ];
    }
    public function buildBreadCrumb(tfdao $dao,array $query, array $parentRelationParams, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // database process
        $data = [];
        while(true){
            $myData = $dao->select($query);
            if(!$myData){
                break;
            }
            $data[] = $myData;
            foreach ($query as $qK => $qV) if(isset($parentRelationParams[$qK])) $query[$qK] = $myData[$parentRelationParams[$qK]];
        }
        krsort($data);
        $sortedData = [];
        foreach ($data as $datum) $sortedData[] = $datum;
        return [
            "data"=>$sortedData
        ];
    }
    public function buildCreate(tfdao $dao, array $data, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // parameters validate
        $posts = $this->tfphp->getRequest()->post();
        // database process
        $ret = $dao->insert($data);
        if(!$ret){
            return (isset($options["errorMapping"][2])) ? $options["errorMapping"][2] : ["errcode"=>3, "errmsg"=>"fail to insert"];
        }
        if(isset($options["withAutoIncrementValue"]) && $options["withAutoIncrementValue"]){
            $newId = $this->lastInsertAutoIncrementValue = $dao->getLastInsertAutoIncrementValue();
            if(!$newId){
                return (isset($options["errorMapping"][3])) ? $options["errorMapping"][3] : ["errcode"=>4, "errmsg"=>"fail to get last insert auto increment value"];
            }
        }
        return (isset($options["errorMapping"][0])) ? $options["errorMapping"][0] : ["errcode"=>0, "errmsg"=>"OK"];
    }
    public function buildCreateUnique(tfdao $dao, array $uniqueQuery, array $data, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // parameters validate
        $posts = $this->tfphp->getRequest()->post();
        // database process
        if($dao->select($uniqueQuery)){
            return (isset($options["errorMapping"][2])) ? $options["errorMapping"][2] : ["errcode"=>2, "errmsg"=>"duplicate"];
        }
        $ret = $dao->insert($data);
        if(!$ret){
            return (isset($options["errorMapping"][3])) ? $options["errorMapping"][3] : ["errcode"=>3, "errmsg"=>"fail to insert"];
        }
        if(isset($options["withAutoIncrementValue"]) && $options["withAutoIncrementValue"]){
            $newId = $this->lastInsertAutoIncrementValue = $dao->getLastInsertAutoIncrementValue();
            if(!$newId){
                return (isset($options["errorMapping"][4])) ? $options["errorMapping"][4] : ["errcode"=>4, "errmsg"=>"fail to get last insert auto increment value"];
            }
        }
        return (isset($options["errorMapping"][0])) ? $options["errorMapping"][0] : ["errcode"=>0, "errmsg"=>"OK"];
    }
    public function buildUpdate(tfdao $dao, array $query, array $data, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // parameters validate
        $posts = $this->tfphp->getRequest()->post();
        // database process
        $curr = $dao->select($query);
        if(!$curr){
            return (isset($options["errorMapping"][2])) ? $options["errorMapping"][2] : ["errcode"=>2, "errmsg"=>"not found"];
        }
        $ret = $dao->update($query, $data);
        if(!$ret){
            return (isset($options["errorMapping"][3])) ? $options["errorMapping"][3] : ["errcode"=>3, "errmsg"=>"fail to update"];
        }
        return (isset($options["errorMapping"][0])) ? $options["errorMapping"][0] : ["errcode"=>0, "errmsg"=>"OK"];
    }
    public function buildUpdateUnique(tfdao $dao, array $query, array $uniqueQuery, array $data, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // parameters validate
        $posts = $this->tfphp->getRequest()->post();
        // database process
        $curr = $dao->select($query);
        if(!$curr){
            return (isset($options["errorMapping"][2])) ? $options["errorMapping"][2] : ["errcode"=>2, "errmsg"=>"not found"];
        }
        if($dao->select($uniqueQuery)){
            return (isset($options["errorMapping"][3])) ? $options["errorMapping"][3] : ["errcode"=>3, "errmsg"=>"duplicate"];
        }
        $ret = $dao->update($query, $data);
        if(!$ret){
            return (isset($options["errorMapping"][4])) ? $options["errorMapping"][4] : ["errcode"=>4, "errmsg"=>"fail to update"];
        }
        return (isset($options["errorMapping"][0])) ? $options["errorMapping"][0] : ["errcode"=>0, "errmsg"=>"OK"];
    }
    public function buildDelete(tfdao $dao, array $query, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($options["errorMapping"])) $options["errorMapping"] = [];
        // parameters validate
        $posts = $this->tfphp->getRequest()->post();
        // database process
        $curr = $dao->select($query);
        if(!$curr){
            return (isset($options["errorMapping"][2])) ? $options["errorMapping"][2] : ["errcode"=>2, "errmsg"=>"not found"];
        }
        $ret = $dao->delete($query);
        if(!$ret){
            return (isset($options["errorMapping"][3])) ? $options["errorMapping"][3] : ["errcode"=>3, "errmsg"=>"fail to delete"];
        }
        return (isset($options["errorMapping"][0])) ? $options["errorMapping"][0] : ["errcode"=>0, "errmsg"=>"OK"];
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        return $this->lastInsertAutoIncrementValue;
    }
}