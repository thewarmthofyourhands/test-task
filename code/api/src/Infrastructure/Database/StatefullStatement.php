<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Eva\Database\PDO\Statement;

class StatefullStatement extends Statement
{
    public function execute(null|array $parameters = null): void
    {

        if (null !== $parameters) {
            $listParameters = [];

//            foreach ($parameters as $parameterName => $parameterValue) {
//                if (true === is_array($parameterValue)) {
//                    $sql = $this->getNativeStatement()->queryString;
//                    $newSqlParameterNameList = [];
//
//                    foreach ($parameterValue as $key => $item) {
//                        $newParameterName = $parameterName . '_' . $key;
//                        $newSqlParameterNameList[] = ':'.$newParameterName;
//                        $listParameters[$parameterName . '_' . $key] = $item;
//                    }
//
//                    $sql = str_replace(':' . $parameterName, implode(', ', $newSqlParameterNameList), $sql);
//                    unset($parameters[$parameterName]);
//                }
//            }
//
//            $parameters = array_merge($parameters, $listParameters);
        }

        $this->statement->execute($parameters);
    }
}
