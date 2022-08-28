<?php

namespace App\Helpers;

use Exception;

class Helper
{

    /**
     * @param $builder
     * @param $columns
     * @param string $operator
     * @param null $value
     * @return mixed
     * @throws \Exception
     */
    public static function whereConcat($builder, $columns, $operator = '=', $value = null)
    {
        $phrase = self::Builder($builder, $columns);

        return $builder->whereRaw("{$phrase} {$operator} ?", $value);
    }

    /**
     * @param $builder
     * @param $columns
     * @param string $operator
     * @param null $value
     * @return mixed
     * @throws \Exception
     */
    public static function orWhereConcat($builder, $columns, $operator = '=', $value = null)
    {
        $phrase = self::Builder($builder, $columns);

        return $builder->orWhereRaw("{$phrase} {$operator} ?", $value);
    }

    /**
     * @param $builder
     * @param $columns
     * @return string
     * @throws \Exception
     */
    private static function Builder($builder, $columns)
    {
        switch ($builder->getConnection()->getDriverName()) {
            case 'mysql':
                foreach ($columns as $key => $column) {
                    $columns[$key] = "`{$column}`";
                }
                $cols = implode(", ' ', ", $columns);

                $concatPhrase = "CONCAT({$cols})";
                break;
            case 'sqlite':
                foreach ($columns as $key => $column) {
                    $columns[$key] = "`{$column}`";
                }
                $cols = implode(" || ' ' ||", $columns);

                $concatPhrase = $cols;
                break;
            default:
                throw new Exception("Unsupported driver for whereConcat");
                break;
        }

        return $concatPhrase;
    }
}
