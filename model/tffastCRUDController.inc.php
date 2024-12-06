<?php

namespace tfphp\model;

use tfphp\framework\model\tfmodel;
use tfphp\framework\system\tfrestfulAPI;

class tffastCRUDController extends tfrestfulAPI {
    public function doGetSearch(tfmodel $model){
        $data = call_user_func_array([$model, "getSearch"], []);
        $this->responseJsonData($data);
    }
    public function doGetDetail(tfmodel $model, int $id){
        $data = call_user_func_array([$model, "getDetail"], [$id]);
        $this->responseJsonData($data);
    }
    public function doGetListDetail(tfmodel $model, string $idList){
        $data = call_user_func_array([$model, "getListDetail"], [$idList]);
        $this->responseJsonData($data);
    }
    public function doAction(string $action, tfmodel $model, array $params, array $options){
        try{
            if(!method_exists($model, $action)){
                $this->responseJsonData(["errcode"=>1, "errmsg"=>"method is not defined"]);
            }
            else{
                call_user_func_array([$model, $action], [$params]);
                $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
            }
        }
        catch (\Exception $e){
            $code = $e->getCode();
            $message = $e->getMessage();
            if(!is_array($options["exceptionMapping"]) || !isset($options["exceptionMapping"][$code])){
                $this->responseJsonData(["errcode"=>$code, "errmsg"=>$message]);
            }
            else{
                $this->responseJsonData(["errcode"=>$options["exceptionMapping"][$code][0], "errmsg"=>$options["exceptionMapping"][$code][1]]);
            }
        }
    }
    public function doActionWithID(string $action, tfmodel $model, string $id, array $params, array $options){
        try{
            if(!method_exists($model, $action)){
                $this->responseJsonData(["errcode"=>1, "errmsg"=>"method is not defined"]);
            }
            else{
                call_user_func_array([$model, $action], [$id, $params]);
                $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
            }
        }
        catch (\Exception $e){
            $code = $e->getCode();
            $message = $e->getMessage();
            if(!is_array($options["exceptionMapping"]) || !isset($options["exceptionMapping"][$code])){
                $this->responseJsonData(["errcode"=>$code, "errmsg"=>$message]);
            }
            else{
                $this->responseJsonData(["errcode"=>$options["exceptionMapping"][$code][0], "errmsg"=>$options["exceptionMapping"][$code][1]]);
            }
        }
    }
    public function doOther(string $action, tfmodel $model){
        if(!method_exists($model, $action)){
            $this->responseJsonData(["errcode"=>1, "errmsg"=>"method is not defined"]);
        }
        else{
            $data = call_user_func_array([$model, $action], []);
            $this->responseJsonData($data);
        }
    }
    public function select(tfmodel $model, array $options){
        $method = $_SERVER["REQUEST_METHOD"];
        $resFunction = $_SERVER["RESTFUL_RESOURCE_FUNCTION"];
        $resValue = $_SERVER["RESTFUL_RESOURCE_VALUE"];
        switch ($resFunction){
            case "search":
                $this->doGetSearch($model);
                break;
            case "detail":
                $this->doGetDetail($model, $resValue);
                break;
            case "list_detail":
                $this->doGetListDetail($model, $resValue);
                break;
            default:
                if($method != "GET"){
                    if(!$resValue){
                        $this->doAction($resFunction, $model, $_POST, [
                            "exceptionMapping"=>$options["exceptionMappings"][$resFunction]
                        ]);
                    }
                    else{
                        $this->doActionWithID($resFunction, $model, $resValue, $_POST, [
                            "exceptionMapping"=>$options["exceptionMappings"][$resFunction]
                        ]);
                    }
                }
                else{
                    $this->doOther($resFunction, $model);
                }
        }
    }
}