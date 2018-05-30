<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Digital2Go\Redshift\Database\Schema;

use Cake\Database\Schema\PostgresSchema;
use Cake\Database\Schema\TableSchema;

/**
 * Schema management/reflection features for Postgres.
 */
class RedshiftSchema extends PostgresSchema
{

    private $reserved = [
        'oid', 'tableoid',
        'xmin', 'cmin', 'xmax', 'cmax',
        'ctid', 'deletexid', 'insertxid',
    ];

    /**
     * Add/update a constraint into the schema object.
     *
     * @param \Cake\Database\Schema\TableSchema $schema The table to update.
     * @param string $name The index name.
     * @param string $type The index type.
     * @param array $row The metadata record to update with.
     * @return void
     */
    protected function _convertConstraint($schema, $name, $type, $row)
    {
        $constraint = $schema->getConstraint($name);
        if (!$constraint) {
            $constraint = [
                'type' => $type,
                'columns' => []
            ];
        }
        if (!in_array($row['attname'], $this->reserved)) {
            $constraint['columns'][] = $row['attname'];
            $schema->addConstraint($name, $constraint);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function describeColumnSql($tableName, $config)
    {
        $sql = 'SELECT DISTINCT table_schema AS schema,
            column_name AS name,
            data_type AS type,
            is_nullable AS null,
            column_default AS default,
            character_maximum_length AS char_length,
            c.collation_name,
            d.description as comment,
            ordinal_position,
            c.numeric_precision as column_precision,
            c.numeric_scale as column_scale,
                CASE
                WHEN column_default LIKE \'%identity%\' THEN 1
                ELSE 0
                END
                AS has_serial
            -- pg_get_serial_sequence(attr.attrelid::regclass::text, attr.attname) IS NOT NULL AS has_serial -- @todo
        FROM information_schema.columns c
        INNER JOIN pg_catalog.pg_namespace ns ON (ns.nspname = table_schema)
        INNER JOIN pg_catalog.pg_class cl ON (cl.relnamespace = ns.oid AND cl.relname = table_name)
        LEFT JOIN pg_catalog.pg_index i ON (i.indrelid = cl.oid AND i.indkey[0] = c.ordinal_position)
        LEFT JOIN pg_catalog.pg_description d on (cl.oid = d.objoid AND d.objsubid = c.ordinal_position)
        LEFT JOIN pg_catalog.pg_attribute attr ON (cl.oid = attr.attrelid AND column_name = attr.attname)
        WHERE table_name = ? AND table_schema = ? AND table_catalog = ?
        ORDER BY ordinal_position';

        $schema = empty($config['schema']) ? 'public' : $config['schema'];

        return [$sql, [$tableName, $schema, $config['database']]];
    }

    /**
     * {@inheritDoc}
     */
    public function describeIndexSql($tableName, $config)
    {
        $sql = 'SELECT
        c2.relname,
        a.attname,
        i.indisprimary,
        i.indisunique
        FROM pg_catalog.pg_namespace n
        INNER JOIN pg_catalog.pg_class c ON (n.oid = c.relnamespace)
        INNER JOIN pg_catalog.pg_index i ON (c.oid = i.indrelid)
        INNER JOIN pg_catalog.pg_class c2 ON (c2.oid = i.indexrelid)
        INNER JOIN pg_catalog.pg_attribute a ON (a.attrelid = c.oid AND i.indrelid = a.attrelid)
        WHERE n.nspname = ?
        -- AND a.attnum = ANY(i.indkey) -- @todo
        AND pg_get_indexdef(i.indexrelid) LIKE \'%INDEX%\'
        AND c.relname = ?
        ORDER BY i.indisprimary DESC, i.indisunique DESC, c.relname, a.attnum';

        $schema = 'public';
        if (!empty($config['schema'])) {
            $schema = $config['schema'];
        }

        return [$sql, [$schema, $tableName]];
    }

    /**
     * {@inheritDoc}
     */
    // public function convertIndexDescription(TableSchema $schema, $row)
    // {
    //     $type = TableSchema::INDEX_INDEX;
    //     $name = $row['relname'];
    //     if ($row['indisprimary']) {
    //         $name = $type = TableSchema::CONSTRAINT_PRIMARY;
    //     }
    //     if ($row['indisunique'] && $type === TableSchema::INDEX_INDEX) {
    //         $type = TableSchema::CONSTRAINT_UNIQUE;
    //     }
    //     if ($type === TableSchema::CONSTRAINT_PRIMARY || $type === TableSchema::CONSTRAINT_UNIQUE) {
    //         $this->_convertConstraint($schema, $name, $type, $row);

    //         return;
    //     }
    //     $index = $schema->getIndex($name);
    //     if (!$index) {
    //         $index = [
    //             'type' => $type,
    //             'columns' => []
    //         ];
    //     }
    //     $index['columns'][] = $row['attname'];
    //     $schema->addIndex($name, $index);
    // }
}
