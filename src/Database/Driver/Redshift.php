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
namespace Digital2Go\Redshift\Database\Driver;

use Digital2Go\Redshift\Database\Dialect\RedshiftDialectTrait;
use Cake\Database\Driver\Postgres as PostgresDriver;
use PDOException;
use PDO;

/**
 * Class Postgres
 */
class Redshift extends PostgresDriver
{

    use RedshiftDialectTrait;

    /**
     * {@inheritDoc}
     */
    public function lastInsertId($table = null, $column = null) // $table = null, $column = null
    {
        $this->connect();

        $schema = $this->_config['schema'];
        // $table = $this->_connection->quote($table);
        // $column = $this->_connection->quote($column);

        $stmt = $this->_connection->query('SELECT MAX(' . $column . ') AS id FROM ' . $schema . '.' . $table . ';');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['id'];
    }
}
