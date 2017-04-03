<?php

// ----------------------------------------------------------------------------
// database configuration
// ----------------------------------------------------------------------------
// database yang akan dipindahkan
$config['db']['a'] = array(
  'host' => 'localhost',
  'port' => 3306,
  'name' => 'senayandb_1',
  'user' => 'root',
  'pass' => ''
);
// database target
$config['db']['b'] = array(
  'host' => 'localhost',
  'port' => 3306,
  'name' => 'senayandb_2',
  'user' => 'root',
  'pass' => ''
);

// in cli
$config['cli'] = true;

// run application
require 'pemindah.action.php';
