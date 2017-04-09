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
  private $update = false;

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
      die(json_encode(array('outcome' => false, 'message' => 'Unable to connect. ' . $e)));
    }
  }

  public function setUpdate($update = true)
  {
    $this->update = $update;
    Pindah::debug('Melakukan pemindahan dalam mode UPDATE');
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

  public function count($table)
  {
    $query = $this->db->query('SELECT count(*) FROM '.$table);
    return $query->fetch();
  }

  public function getByID($table, $id, $field = 'id')
  {
    $query = $this->db->prepare('SELECT * FROM '.$table.' WHERE '.$field.'=?');
    $query->execute(array($id));
    return $query->fetchAll(PDO::FETCH_ASSOC);
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

  private function isIgnored($table, $field)
  {
    $ignored['mst_author']    = array('author_id');
    $ignored['mst_topic']     = array('topic_id');
    $ignored['mst_gmd']       = array('gmd_id');
    $ignored['mst_language']  = array('language_id');
    $ignored['mst_place']     = array('place_id');
    $ignored['mst_publisher'] = array('publisher_id');
    $ignored['mst_coll_type'] = array('coll_type_id');
    $ignored['biblio']        = array('biblio_id');
    $ignored['item']          = array('item_id');

    if ($this->update) {
      foreach ($ignored as $key => $value) {
        if ($key == $table && in_array($field, $ignored[$table])) {
          return true;
        }
      }
    }
    return false;
  }

  public function insertIgnore($table, $data, $field_compare = null)
  {
    $fields = '';
    $values = '';
    $update = '';
    $array_value = array();
    foreach ($data as $key => $value) {
      if ($this->isIgnored($table, $key)) {
        continue;
      }
      $fields .= $key.',';
      $values .= '?,';
      $array_value[] = $value;
      $update .= $key .'=\''.addslashes($value).'\',';
    }
    $fields = substr_replace($fields, '', -1);
    $values = substr_replace($values, '', -1);
    $update = substr_replace($update, '', -1);
    if ($this->update) {
      $sql = 'INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.') ON DUPLICATE KEY UPDATE ' . $update;
      $sth = $this->db->prepare($sql);
      if ($sth->execute($array_value)) {
        return $this->db->lastInsertId();
      } else {
        return FALSE;
      }
    } else {
      // check field_compare
      if (!is_null($field_compare)) {
        $sql_compare = 'SELECT '.$field_compare.' FROM '.$table.' WHERE '.$field_compare.'=?';
        $sth = $this->db->prepare($sql_compare);
        $sth->execute(array($data[$field_compare]));
        if ($sth->rowCount() > 0) {
          return;
        }
        $sql_compare = null;
      }

      $sth = $this->db->prepare('INSERT IGNORE INTO '.$table.' ('.$fields.') VALUES ('.$values.')');
      if ($sth->execute($array_value)) {
        return $this->db->lastInsertId();
      } else {
        return FALSE;
      }
    }
  }

  public static function debug($message)
  {
    echo PHP_EOL . $message;
  }

  public static function showLoading($count)
  {
    if ($count % 10 == 0) {
      $index = ($count / 10) - 1;
      if ($index % 70 == 0 || $index < 1) {
        echo PHP_EOL;
      }
      if ($index < 840) {
        echo Pindah::me($index);
      } else {
        echo '.';
      }
    }
  }

  public static function me($index)
  {
    $string = '===========================.L.O.A.D.I.N.G.============================......######..######...........##....######......######....######.........##......##..##.....................####......####....##......##.......##..........##...........####......##..##..##..##....##.................####......##.............##......##..##..##..##......####...............######..##.............##......##..##..##..##........######...............##..##.............##......##....##....##............##.......##......##..##......##.....##......##....##....##....##......##.........######..############...######..######......######....######.....======================================================================.............M.I.G.R.A.T.O.R.....BY.....I.D.O..A.L.I.T................======================================================================';
    return $string[$index];
  } 
}
