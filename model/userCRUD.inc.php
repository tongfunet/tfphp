<?php

namespace tfphp\model;

use tfphp\framework\tfphp;

class userCRUD extends user {
    private tffastCRUDModel $tffastCRUDModel;
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
        $this->tffastCRUDModel = new tffastCRUDModel($this, $this->getDS(), $this->getO2O("fullUser"));
    }
    public function getSearch(): array{
        $data = $this->tffastCRUDModel->getSearch("select u.userId, u.userName, u.state, ud.nickName, ud.gender, ud.birth, u.createDT, u.updateDT, '' as __opers__
            from user u 
            inner join userDetail ud 
            on u.userId = ud.userId", []);
        return $data;
    }
    public function getDetail(string $id): ?array{
        $data = $this->tffastCRUDModel->getDetail($id);
        return $data;
    }
    public function add(array $params){
        $params["createDT"] = date("Y-m-d H:i:s");
        $this->tffastCRUDModel->addUniqueData($params, [
            "userName"
        ]);
    }
    public function enable(string $id, array $params){
        $params["state"] = 1;
        $params["updateDT"] = date("Y-m-d H:i:s");
        $this->tffastCRUDModel->modify($id, $params);
    }
    public function disable(string $id, array $params){
        $params["state"] = 0;
        $params["updateDT"] = date("Y-m-d H:i:s");
        $this->tffastCRUDModel->modify($id, $params);
    }
    public function modify(string $id, array $params){
        $params["updateDT"] = date("Y-m-d H:i:s");
        $this->tffastCRUDModel->modifyUniqueData($id, $params, [
            "userName"
        ]);
    }
    public function remove(string $id, array $params){
        $params["updateDT"] = date("Y-m-d H:i:s");
        $this->tffastCRUDModel->remove($id);
    }
}