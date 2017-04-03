<?php

/**
 * Pemindah class
 */
class Pindah
{

  private $host = 'localhost';
  private $port = 3306;
  private $name = '';
  private $user = '';
  private $pass = '';
  private $db = null;

  function __construct($config)
  {
    $this->host = $config['host'];
    $this->port = $config['port'];
    $this->name = $config['name'];
    $this->user = $config['user'];
    $this->pass = $config['pass'];
    // create connection
    $this->connect();
  }

  private function connect()
  {
    try {
      $this->db = new PDO('mysql:host='.$this->host.';port='.$this->port.';dbname='.$this->name,
        $this->user, $this->pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    } catch (PDOException $e) {
      die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
    }
  }

  public function get($table, $limit = 0, $offset = 0)
  {
    $criteria = ' ';
    if ($limit > 0) {
      $criteria = ' LIMIT '.$limit.' OFFSET '.$offset;
    }
    $query = $this->db->query('SELECT * FROM '.$table.$criteria, PDO::FETCH_ASSOC);
    return $query->fetchAll();
  }

  public function getByID($table, $id, $field = 'id')
  {
    $query = $this->db->query('SELECT * FROM '.$table.' WHERE '.$field.'='.$id, PDO::FETCH_ASSOC);
    return $query->fetchAll();
  }

  public function getID($table, $field, $field_search, $string)
  {
    $sth = $this->db->prepare('SELECT '.$field.' FROM '.$table.' WHERE '.$field_search.'=?');
    $sth->bindParam(1, $string, PDO::PARAM_STR);
    if ($sth->execute()) {
      $data = $sth->fetch(PDO::FETCH_ASSOC);
      return $data[$field];
    }
    return NULL;
  }

  public function insertIgnore($tabel, $data)
  {
    $fields = '(';
    $values = '(';
    $array_value = array();
    foreach ($data as $key => $value) {
      $fields .= $key.',';
      $values .= '?,';
      $array_value[] = $value;
    }
    $fields = substr_replace($fields, ')', -1);
    $values = substr_replace($values, ')', -1);
    $sth = $this->db->prepare('INSERT IGNORE INTO '.$tabel.' '.$fields.' VALUES '.$values);
    if (!$sth->execute($array_value)) {
      return FALSE;
    }
    return $this->db->lastInsertId();
  }
}
