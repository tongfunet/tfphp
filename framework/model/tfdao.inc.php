<?php 

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

/**
 * Class tfdao
 * @package tfphp\framework\model
 * @datetime 2025/7/17
 */
abstract class tfdao{
    const RELATION_PARAM_TYPE_ARRAY = 1;
    const RELATION_PARAM_TYPE_SQL = 2;
    const FIELD_TYPE_INT = 1;
    const FIELD_TYPE_STR = 2;
    protected tfphp $tfphp;
    public function __construct(tfphp $AE){
        $this->tfphp = $AE;
    }

    public function select(array $B0): ?array{
        return null;
    }
    public function constraintSelect(array $B2, string $B5="default"): ?array{
        return null;
    }
    public function keySelect(array $B2, string $B5="default"): ?array{
        return null;
    }
    public function sqlWhereSelect(string $B7, array $B2, array $BC=null): ?array{
        return null;
    }

    public function insert(array $BE, array $BC=null): bool{
        return false;
    }

    public function update(array $B0, array $BE, array $BC=null): bool{
        return false;
    }
    public function constraintUpdate(array $B2, array $BE, string $B5="default", array $BC=null): bool{
        return false;
    }
    public function keyUpdate(array $B2, array $BE, string $B5="default", array $BC=null): bool{
        return false;
    }

    public function delete(array $B0): bool{
        return false;
    }
    public function constraintDelete(array $B2, string $B5="default"): bool{
        return false;
    }
    public function keyDelete(array $B2, string $B5="default"): bool{
        return false;
    }

    public function getAutoIncrementField(): ?string{
        return null;
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        return null;
    }
    public function getLastInsertData(): ?array{
        return null;
    }

    public function getTFDO(): ?tfdo{
        return null;
    }
}