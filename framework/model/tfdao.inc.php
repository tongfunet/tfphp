<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

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
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }

    public function select(array $query): ?array{
        return null;
    }

    public function insert(array $data, array $options=null): bool{
        return false;
    }

    public function update(array $query, array $data, array $options=null): bool{
        return false;
    }

    public function delete(array $query): bool{
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