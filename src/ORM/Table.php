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
namespace Digital2Go\Redshift\ORM;

use Cake\ORM\Table as CakeTable;

class Table extends CakeTable
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->_alias = strtolower($this->getAlias());

        parent::initialize($config);
    }

    /**
     * {@inheritDoc}
     */
    public function belongsTo($associated, array $options = [])
    {
        if (empty($options['className'])) {
            $options['className'] = $associated;
        }
        $associated = strtolower($associated);

        return parent::belongsTo($associated, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function hasOne($associated, array $options = [])
    {
        if (empty($options['className'])) {
            $options['className'] = $associated;
        }
        $associated = strtolower($associated);

        return parent::hasOne($associated, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function hasMany($associated, array $options = [])
    {
        if (empty($options['className'])) {
            $options['className'] = $associated;
        }
        $associated = strtolower($associated);

        return parent::hasMany($associated, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function belongsToMany($associated, array $options = [])
    {
        if (empty($options['className'])) {
            $options['className'] = $associated;
        }
        $associated = strtolower($associated);

        return parent::belongsToMany($associated, $options);
    }
}
