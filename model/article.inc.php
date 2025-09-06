<?php 

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;

class article extends tfmodel{
    public function __construct(tfphp $A){
        parent::__construct($A);
        $this->setDAOOneToMany("article_class", ["articleClass", "article"], [
            ["mapping"=>["classId"=>"classId"]]
        ]);
        $this->setDAOManyToMany("article_tags", ["articleTag", "article_tag", "article"], [
            ["mapping"=>["tagId"=>"tagId"]],
            ["mapping"=>["articleId"=>"articleId"]]
        ]);
    }
    public function tfdaoOneToManyCRUD(): array{
        $A6 = $this->tfphp->getDataSource();
        $A9 = $this->getSG("article");
        $AB = $this->getSG("articleClass");
        $AC = $this->getO2M("article_class");
        $A6->execute3("truncate table article", []);
        $A6->execute3("truncate table articleClass", []);
        $B1 = [];
        $B1[] = ["tfdaoOneToMany", "CRUD"];
        $B6 = [];
        for($B9=0;$B9<3;$B9++){
            $B1[] = ["insert article for class", $A9->insert([
                "title"=>"文章". date("YmdHis"),
                "content"=>"正文". date("YmdHis"),
                "createDT"=>date("Y-m-d H:i:s")
            ])];
            $B6[] = $BD = $A9->getLastInsertData()["articleId"];
        }
        $B1[] = ["select article 1", $A9->keySelect([$B6[0]])];
        $B1[] = ["select article 2", $A9->keySelect([$B6[1]])];
        $B1[] = ["select article 3", $A9->keySelect([$B6[2]])];
        $B1[] = ["insert article class technology", $AB->insert([
            "className"=>"technology"
        ])];
        $BF = $AB->getLastInsertData()["classId"];
        $B1[] = ["insert article class travel", $AB->insert([
            "className"=>"travel"
        ])];
        $C0 = $AB->getLastInsertData()["classId"];
        $B1[] = ["bind technology to article 1", $A9->update(
            ["articleId"=>$B6[0]],
            ["classId"=>$BF]
        )];
        $B1[] = ["bind travel to article 3", $A9->update(
            ["articleId"=>$B6[2]],
            ["classId"=>$C0]
        )];
        $B1[] = ["select article 1", $A9->keySelect([$B6[0]])];
        $B1[] = ["select article 2", $A9->keySelect([$B6[1]])];
        $B1[] = ["select article 3", $A9->keySelect([$B6[2]])];
        $B1[] = ["unbind article 1", $A9->update(
            ["articleId"=>$B6[0]],
            ["classId"=>0]
        )];
        $B1[] = ["unbind article 3", $A9->update(
            ["articleId"=>$B6[2]],
            ["classId"=>0]
        )];
        $B1[] = ["select article 1", $A9->keySelect([$B6[0]])];
        $B1[] = ["select article 2", $A9->keySelect([$B6[1]])];
        $B1[] = ["select article 3", $A9->keySelect([$B6[2]])];
        return $B1;
    }
    public function tfdaoManyToManyCRUD(): array{
        $A6 = $this->tfphp->getDataSource();
        $A9 = $this->getSG("article");
        $C4 = $this->getSG("articleTag");
        $CA = $this->getM2M("article_tags");
        $A6->execute3("truncate table article", []);
        $A6->execute3("truncate table articleTag", []);
        $A6->execute3("truncate table article_tag", []);
        $B1 = [];
        $B1[] = ["tfdaoManyToMany", "CRUD"];
        $B6 = [];
        for($B9=0;$B9<3;$B9++){
            $B1[] = ["insert article for tags", $A9->insert([
                "title"=>"文章". date("YmdHis"),
                "content"=>"正文". date("YmdHis"),
                "createDT"=>date("Y-m-d H:i:s")
            ])];
            $B6[] = $BD = $A9->getLastInsertData()["articleId"];
        }
        $B1[] = ["select article 1", $CB = $A9->keySelect([$B6[0]])];
        $B1[] = ["select article 2", $CF = $A9->keySelect([$B6[1]])];
        $B1[] = ["select article 3", $D4 = $A9->keySelect([$B6[2]])];
        $B1[] = ["insert article tag php", $C4->insert([
            "tagName"=>"php"
        ])];
        $DA = $C4->getLastInsertAutoIncrementValue();
        $B1[] = ["insert article tag java", $C4->insert([
            "tagName"=>"java"
        ])];
        $DC = $C4->getLastInsertAutoIncrementValue();
        $B1[] = ["insert article tag python", $C4->insert([
            "tagName"=>"python"
        ])];
        $E0 = $C4->getLastInsertAutoIncrementValue();
        $B1[] = ["select tag php", $E6 = $C4->keySelect([$DA])];
        $B1[] = ["select tag php", $EC = $C4->keySelect([$DC])];
        $B1[] = ["select tag php", $EF = $C4->keySelect([$E0])];
        $B1[] = ["bind tag php & java to article 1 & 2", $CA->bind([
            $E6, $EC
        ], [
            $CB, $CF
        ])];
        $B1[] = ["bind tag java & python to article 2 & 3", $CA->bind([
            $EC, $EF
        ], [
            $CF, $D4
        ])];
        $B1[] = ["bind tag python & php to article 3 & 1", $CA->bind([
            $EF, $E6
        ], [
            $D4, $CB
        ])];
        $B1[] = ["get all tags of article 1", $CA->getADataAll($CB)];
        $B1[] = ["get all tags of article 2", $CA->getADataAll($CF)];
        $B1[] = ["get all tags of article 3", $CA->getADataAll($D4)];
        $B1[] = ["unbind php to article 1", $CA->unbind([
            $E6
        ], [
            $CB
        ])];
        $B1[] = ["unbind java to article 2", $CA->unbind([
            $EC
        ], [
            $CF
        ])];
        $B1[] = ["unbind python to article 3", $CA->unbind([
            $EF
        ], [
            $D4
        ])];
        $B1[] = ["get all tags of article 1", $CA->getADataAll($CB)];
        $B1[] = ["get all tags of article 2", $CA->getADataAll($CF)];
        $B1[] = ["get all tags of article 3", $CA->getADataAll($D4)];
        $B1[] = ["replace php to article 1", $CA->replace([
            $E6
        ], [
            $CB
        ])];
        $B1[] = ["replace java to article 2", $CA->replace([
            $EC
        ], [
            $CF
        ])];
        $B1[] = ["replace python to article 3", $CA->replace([
            $EF
        ], [
            $D4
        ])];
        $B1[] = ["get all tags of article 1", $CA->getADataAll($CB)];
        $B1[] = ["get all tags of article 2", $CA->getADataAll($CF)];
        $B1[] = ["get all tags of article 3", $CA->getADataAll($D4)];
        $B1[] = ["get all articles of tag php", $CA->getBDataAll($E6)];
        $B1[] = ["get all articles of tag java", $CA->getBDataAll($EC)];
        $B1[] = ["get all articles of tag python", $CA->getBDataAll($EF)];
        return $B1;
    }
}