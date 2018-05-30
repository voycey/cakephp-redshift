# Redshift plugin for CakePHP

This plugin is still in development and hasn't been tested completely.  *Be extremely careful using this plugin and make sure your data is always backed up.*

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require digital2go/cakephp-redshift
```

You can set the datasource up in your `config/app.php`.

```
'Datasources' => [
    'default' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Digital2Go\Redshift\Database\Driver\Redshift',
        'persistent' => false,
        'host' => 'my-redshift-datasource.us-west-2.redshift.amazonaws.com',
        'port' => '5439',
        'username' => 'username',
        'password' => 'password',
        'database' => 'my_redshift_database',
    ],
],
```

Tables using this datasource need to extend the Redshift Table class:

```
<?php
namespace App\Model\Table;

use Digital2Go\Redshift\ORM\Table;

class ProfilesTable extends Table
{
```

Alternatively, if you don't want to extend the class you need to make sure that you do the following:


```
// Set a completely lowercase version of the alias as the alias.
public function initialize(array $config)
{
    $this->setAlias('profiles');
}

// Make sure the relationship alias is lowercase, and that the
// className is set correctly.
$this->belongsTo('users', [ // alias
    'className' => 'Users',
    'foreignKey' => 'user_id',
    'joinType' => 'INNER'
]);
```
