<?php

require 'pemindah.lib.php';

if (isset($_GET['data']) && !empty($_GET['data']) || isset($argv)) {
  if (isset($config['cli'])) {
    $a = $config['db']['a'];
    $b = $config['db']['b'];
  } else {
    // database konfigurasi A
    $a['host'] = trim($_GET['host-a']);
    $a['port'] = trim($_GET['port-a']);
    $a['name'] = trim($_GET['name-a']);
    $a['user'] = trim($_GET['user-a']);
    $a['pass'] = trim($_GET['pass-a']);
    // database konfigurasi B
    $b['host'] = trim($_GET['host-b']);
    $b['port'] = trim($_GET['port-b']);
    $b['name'] = trim($_GET['name-b']);
    $b['user'] = trim($_GET['user-b']);
    $b['pass'] = trim($_GET['pass-b']);
  }
  // Create instance
  $obj_a = new Pindah($a);
  $obj_b = new Pindah($b);

  $modules = array();
  // fetch post data
  if (isset($_GET['data'])) {
    foreach ($_GET['data'] as $d) {
      $modules[$d] = $d;
    }
  }

  // fetch $argv
  if (isset($argv)) {
    foreach ($argv as $ar) {
      $modules[$ar] = $ar;
    }
  }

  // bibliography action
  if (isset($modules['bibliography'])) {
    $_return = array();
    // memindahkan table table master terlebih dahulu
    // ------------------------------------------------------------------------
    // tabel mst_author
    // ------------------------------------------------------------------------
    $mst_author = $obj_a->get('mst_author');
    $_return['count']['author'] = 0;
    foreach ($mst_author as $author) {
      $author['author_id'] = '';
      $insert = $obj_b->insertIgnore('mst_author', $author);
      if ($insert === FALSE) {
        $_return['error']['author'][] = $author['author_name'];
      }
      $_return['count']['author']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_topic
    // ------------------------------------------------------------------------
    $mst_topic = $obj_a->get('mst_topic');
    $_return['count']['topic'] = 0;
    foreach ($mst_topic as $topic) {
      $topic['topic_id'] = '';
      $insert = $obj_b->insertIgnore('mst_topic', $topic);
      if ($insert === FALSE) {
        $_return['error']['topic'][] = $topic['topic'];
      }
      $_return['count']['topic']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_gmd
    // ------------------------------------------------------------------------
    $mst_gmd = $obj_a->get('mst_gmd');
    $_return['count']['gmd'] = 0;
    foreach ($mst_gmd as $gmd) {
      $gmd['gmd_id'] = '';
      $insert = $obj_b->insertIgnore('mst_gmd', $gmd);
      if ($insert === FALSE) {
        $_return['error']['gmd'][] = $gmd['gmd_name'];
      }
      $_return['count']['gmd']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_language
    // ------------------------------------------------------------------------
    $mst_language = $obj_a->get('mst_language');
    $_return['count']['language'] = 0;
    foreach ($mst_language as $lang) {
      $lang['language_id'] = '';
      $insert = $obj_b->insertIgnore('mst_language', $lang);
      if ($insert === FALSE) {
        $_return['error']['lang'][] = $lang['language_name'];
      }
      $_return['count']['language']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_place
    // ------------------------------------------------------------------------
    $mst_place = $obj_a->get('mst_place');
    $_return['count']['place'] = 0;
    foreach ($mst_place as $place) {
      $place['place_id'] = '';
      $insert = $obj_b->insertIgnore('mst_place', $place);
      if ($insert === FALSE) {
        $_return['error']['place'][] = $place['place_name'];
      }
      $_return['count']['place']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_publisher
    // ------------------------------------------------------------------------
    $mst_publisher = $obj_a->get('mst_publisher');
    $_return['count']['publisher'] = 0;
    foreach ($mst_publisher as $publisher) {
      $publisher['publisher_id'] = '';
      $insert = $obj_b->insertIgnore('mst_publisher', $publisher);
      if ($insert === FALSE) {
        $_return['error']['publisher'][] = $publisher['publisher_name'];
      }
      $_return['count']['publisher']++;
    }

    // ------------------------------------------------------------------------
    // tabel mst_coll_type
    // ------------------------------------------------------------------------
    $mst_coll_type = $obj_a->get('mst_coll_type');
    $_return['count']['collection_type'] = 0;
    foreach ($mst_coll_type as $coll_type) {
      $coll_type['coll_type_id'] = '';
      $insert = $obj_b->insertIgnore('mst_coll_type', $coll_type);
      if ($insert === FALSE) {
        $_return['error']['collection_type'][] = $coll_type['coll_type_name'];
      }
      $_return['count']['collection_type']++;
    }

    // ------------------------------------------------------------------------
    // tabel biblio
    // ------------------------------------------------------------------------
    $biblio = $obj_a->get('biblio');
    $_return['count']['biblio'] = 0;
    $_return['count']['biblio_author'] = 0;
    $_return['count']['biblio_topic'] = 0;
    $_return['count']['item'] = 0;
    foreach ($biblio as $_biblio) {
      // get gmd_id
      $gmd_name = '';
      foreach ($mst_gmd as $gmd) {
        if ($gmd['gmd_id'] == $_biblio['gmd_id']) {
          $gmd_name = $gmd['gmd_name'];
          break;
        }
      }
      $_biblio['gmd_id'] = $obj_b->getID('mst_gmd', 'gmd_id', 'gmd_name', $gmd_name);

      // publisher_id
      $publisher_name = '';
      foreach ($mst_publisher as $publisher) {
        if ($publisher['publisher_id'] == $_biblio['publisher_id']) {
          $publisher_name = $publisher['publisher_name'];
          break;
        }
      }
      $_biblio['publisher_id'] = $obj_b->getID('mst_publisher', 'publisher_id', 'publisher_name', $publisher_name);

      // language_id
      $language_name = '';
      foreach ($mst_language as $language) {
        if ($language['language_id'] == $_biblio['language_id']) {
          $language_name = $language['language_name'];
          break;
        }
      }
      $_biblio['language_id'] = $obj_b->getID('mst_language', 'language_id', 'language_name', $language_name);

      // publish_place_id
      $place_name = '';
      foreach ($mst_place as $place) {
        if ($place['place_id'] == $_biblio['publish_place_id']) {
          $place_name = $place['place_name'];
          break;
        }
      }
      $_biblio['publish_place_id'] = $obj_b->getID('mst_place', 'place_id', 'place_name', $place_name);

      // store old bilbio_id
      $biblio_id_old = $_biblio['biblio_id'];
      // reset biblio_id
      $_biblio['biblio_id'] = '';
      // get insert id
      $_biblio_id = $obj_b->insertIgnore('biblio', $_biblio);
      if (!$_biblio_id) {
        $_return['error']['biblio'][] = $biblio_id_old;
      }
      $_return['count']['biblio']++;

      // get & insert biblio author
      // ----------------------------------------------------------------------
      $biblio_author = $obj_a->getByID('biblio_author', $biblio_id_old, 'biblio_id');
      foreach ($biblio_author as $ba) {
        $author_name = '';
        foreach ($mst_author as $author) {
          if ($author['author_id'] == $ba['author_id']) {
            $author_name = $author['author_name'];
            break;
          }
        }
        // get id in new database
        $author_id = $obj_b->getID('mst_author', 'author_id', 'author_name', $author_name);
        $_ba['biblio_id'] = $_biblio_id;
        $_ba['author_id'] = $author_id;
        $_ba['level'] = $ba['level'];
        // insert into biblio_author
        $insert = $obj_b->insertIgnore('biblio_author', $_ba);
        if ($insert === FALSE) {
          $_return['error']['biblio_author'][] = $_biblio_id;
        }
        $_return['count']['biblio_author']++;
      }

      // get & insert biblio topic
      // ----------------------------------------------------------------------
      $biblio_topic = $obj_a->getByID('biblio_topic', $biblio_id_old, 'biblio_id');
      foreach ($biblio_topic as $bt) {
        $topic_name = '';
        foreach ($mst_topic as $topic) {
          if ($topic['topic_id'] == $bt['topic_id']) {
            $topic_name = $topic['topic'];
            break;
          }
        }
        // get topic id from new database
        $topic_id = $obj_b->getID('mst_topic', 'topic_id', 'topic', $topic_name);
        $_bt['biblio_id'] = $_biblio_id;
        $_bt['topic_id'] = $topic_id;
        $_bt['level'] = $bt['level'];
        // insert into biblio_topic
        $insert = $obj_b->insertIgnore('biblio_topic', $_bt);
        if ($insert === FALSE) {
          $_return['error']['biblio_topic'][] = $_biblio_id;
        }
        $_return['count']['biblio_topic']++;
      }

      // get & insert item
      // ----------------------------------------------------------------------
      $items = $obj_a->getByID('item', $biblio_id_old, 'biblio_id');
      foreach ($items as $item) {
        // coll_type_id
        $coll_type_name = '';
        foreach ($mst_coll_type as $coll_type) {
          if ($coll_type['coll_type_id'] == $item['coll_type_id']) {
            $coll_type_name = $coll_type['coll_type_name'];
            break;
          }
        }
        $coll_type_id = $obj_b->getID('mst_coll_type', 'coll_type_id', 'coll_type_name', $coll_type_name);
        // location_id
        // item_status_id
        $item['item_id'] = '';
        $item['biblio_id'] = $_biblio_id;
        // insert into item
        $insert = $obj_b->insertIgnore('item', $item);
        if ($insert === FALSE) {
          $_return['error']['item'][] = $item['item_code'];
        }
        $_return['count']['item']++;
      }
    }

    if (empty($_return)) {
      $_return['status'] = 'OK';
    }
    echo json_encode($_return);
  }

  // member action
  if (isset($modules['member'])) {
    # code...
  }

  // circulation action
  if (isset($modules['loan'])) {
    # code...
  }

  // system action
  if (isset($modules['system'])) {
    # code...
  }

} else {
  echo 'Pilih data yang akan dipindahkan dahulu!';
}
