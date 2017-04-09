<?php

require 'pemindah.lib.php';

// start
$start = microtime(true);
Pindah::debug('Pemindah mulai berjalan pada ' . date("Y-m-d H:i:s"));

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
  Pindah::debug('Mencoba mengkoneksi database');
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

  // update mode
  if (isset($modules['--update'])) {
    $obj_b->setUpdate();
  }

  // bibliography action
  if (isset($modules['bibliography'])) {
    Pindah::debug('Melakukan pemindahan data bibliografy');
    $_return = array();
    // memindahkan table table master terlebih dahulu
    // ------------------------------------------------------------------------
    // tabel mst_author
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data pengarang');
    $mst_author = $obj_a->get('mst_author');
    $_return['count']['author'] = 0;
    foreach ($mst_author as $author) {
      $author['author_id'] = '';
      $insert = $obj_b->insertIgnore('mst_author', $author);
      if ($insert === FALSE) {
        $_return['error']['author'][] = $author['author_name'];
      }
      $_return['count']['author']++;
      Pindah::showLoading($_return['count']['author']);
    }
    $mst_author = null; // free out memory

    // ------------------------------------------------------------------------
    // tabel mst_topic
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data subjek');
    $mst_topic = $obj_a->get('mst_topic');
    $_return['count']['topic'] = 0;
    foreach ($mst_topic as $topic) {
      $topic['topic_id'] = '';
      $insert = $obj_b->insertIgnore('mst_topic', $topic);
      if ($insert === FALSE) {
        $_return['error']['topic'][] = $topic['topic'];
      }
      $_return['count']['topic']++;
      Pindah::showLoading($_return['count']['topic']);
    }
    $mst_topic = null; // free out memory

    // ------------------------------------------------------------------------
    // tabel mst_gmd
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data gmd');
    $mst_gmd = $obj_a->get('mst_gmd');
    $_return['count']['gmd'] = 0;
    foreach ($mst_gmd as $gmd) {
      $gmd['gmd_id'] = '';
      $insert = $obj_b->insertIgnore('mst_gmd', $gmd);
      if ($insert === FALSE) {
        $_return['error']['gmd'][] = $gmd['gmd_name'];
      }
      $_return['count']['gmd']++;
      Pindah::showLoading($_return['count']['gmd']);
    }

    // ------------------------------------------------------------------------
    // tabel mst_language
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data bahasa');
    $mst_language = $obj_a->get('mst_language');
    $_return['count']['language'] = 0;
    foreach ($mst_language as $lang) {
      $lang['language_id'] = '';
      $insert = $obj_b->insertIgnore('mst_language', $lang);
      if ($insert === FALSE) {
        $_return['error']['lang'][] = $lang['language_name'];
      }
      $_return['count']['language']++;
      Pindah::showLoading($_return['count']['language']);
    }

    // ------------------------------------------------------------------------
    // tabel mst_place
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data tempat terbit');
    $mst_place = $obj_a->get('mst_place');
    $_return['count']['place'] = 0;
    foreach ($mst_place as $place) {
      $place['place_id'] = '';
      $insert = $obj_b->insertIgnore('mst_place', $place);
      if ($insert === FALSE) {
        $_return['error']['place'][] = $place['place_name'];
      }
      $_return['count']['place']++;
      Pindah::showLoading($_return['count']['place']);
    }

    // ------------------------------------------------------------------------
    // tabel mst_publisher
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data penerbit');
    $mst_publisher = $obj_a->get('mst_publisher');
    $_return['count']['publisher'] = 0;
    foreach ($mst_publisher as $publisher) {
      $publisher['publisher_id'] = '';
      $insert = $obj_b->insertIgnore('mst_publisher', $publisher);
      if ($insert === FALSE) {
        $_return['error']['publisher'][] = $publisher['publisher_name'];
      }
      $_return['count']['publisher']++;
      Pindah::showLoading($_return['count']['publisher']);
    }

    // ------------------------------------------------------------------------
    // tabel mst_coll_type
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data tipe koleksi');
    $mst_coll_type = $obj_a->get('mst_coll_type');
    $_return['count']['collection_type'] = 0;
    foreach ($mst_coll_type as $coll_type) {
      $coll_type['coll_type_id'] = '';
      $insert = $obj_b->insertIgnore('mst_coll_type', $coll_type);
      if ($insert === FALSE) {
        $_return['error']['collection_type'][] = $coll_type['coll_type_name'];
      }
      $_return['count']['collection_type']++;
      Pindah::showLoading($_return['count']['collection_type']);
    }

    // ------------------------------------------------------------------------
    // tabel biblio
    // ------------------------------------------------------------------------
    Pindah::debug('Memindahkan data biblio');
    $biblio_count = $obj_a->count('biblio')[0];
    //sleep(3);
    Pindah::debug('Data biblio sejumlah: ' . $biblio_count);
    $limit = 840 * 10; // turunkan limit ini jika memori tidak mencukupi
    $loop_for = ceil($biblio_count / $limit);
    // collect last biblio_id
    $last_biblio_ids = array();
    for ($i=0; $i < $loop_for; $i++) {
      $dari = ($i*$limit) + 1;
      $sampai = ($biblio_count < ($i+1)*$limit) ? $biblio_count : $dari + $limit - 1 ;
      Pindah::debug('Memproses data biblio dari cantuman ke ' . $dari . ' sampai ' . $sampai . ' dari total ' . $biblio_count);
      $biblio = $obj_a->get('biblio', $limit, $i*$limit);
      $_return['count']['biblio'] = 0;
      $_return['count']['biblio_author'] = 0;
      $_return['count']['biblio_topic'] = 0;
      $_return['count']['item'] = 0;
      foreach ($biblio as $_biblio) {
        // get gmd_id
        $gmd = $obj_a->getByID('mst_gmd', $_biblio['gmd_id'], 'gmd_id');
        if (count($gmd) > 0) {
          $gmd_name = $gmd[0]['gmd_name'];
          $_biblio['gmd_id'] = $obj_b->getID('mst_gmd', 'gmd_id', 'gmd_name', $gmd_name);
        }

        // publisher_id
        $publisher = $obj_a->getByID('mst_publisher', $_biblio['publisher_id'], 'publisher_id');
        if (count($publisher) > 0) {
          $publisher_name = $publisher[0]['publisher_name'];
          $_biblio['publisher_id'] = $obj_b->getID('mst_publisher', 'publisher_id', 'publisher_name', $publisher_name);
        }

        // language_id
        $language = $obj_a->getByID('mst_language', $_biblio['language_id'], 'language_id');
        if (count($language) > 0) {
          $language_name = $language[0]['language_name'];
          $_biblio['language_id'] = $obj_b->getID('mst_language', 'language_id', 'language_name', $language_name);
        }

        // publish_place_id
        $place = $obj_a->getByID('mst_place', $_biblio['publish_place_id'], 'place_id');
        if (count($place) > 0) {
          $place_name = $place[0]['place_name'];
          $_biblio['publish_place_id'] = $obj_b->getID('mst_place', 'place_id', 'place_name', $place_name);
        }

        // store old bilbio_id
        $biblio_id_old = $_biblio['biblio_id'];
        // reset biblio_id
        $_biblio['biblio_id'] = '';
        // get insert id
        $_biblio_id = $obj_b->insertIgnore('biblio', $_biblio, 'title');
        $biblio_ids[] = array('old' => $biblio_id_old, 'new' => $_biblio_id);
        $_return['count']['biblio']++;
        Pindah::showLoading($_return['count']['biblio']);
      }
      // free out memory
      $biblio         = null;
      $mst_gmd        = null;
      $mst_coll_type  = null;
      $mst_language   = null;
      $mst_publisher  = null;
      $mst_place      = null;
    }

    Pindah::debug('Memproses data bibliografi di database yang baru');
    $bn = 0;
    foreach ($biblio_ids as $b_ID){

      if ($b_ID['new'] == '' || empty($b_ID['new']) || is_null($b_ID['new'])) {
        // this is duplicate title
        // get title
        $_d_title = $obj_a->getID('biblio', 'title', 'biblio_id', $b_ID['old']);
        if (is_null($_d_title)) {
          continue;
        }
        $b_ID['new'] = $obj_b->getID('biblio', 'biblio_id', 'title', $_d_title);
        if (is_null($b_ID['new'])) {
          continue;
        }
      }

      // get & insert biblio author
      // ----------------------------------------------------------------------
      $biblio_author = $obj_a->getByID('biblio_author', $b_ID['old'], 'biblio_id');
      foreach ($biblio_author as $ba){
        $author = $obj_a->getByID('mst_author', $ba['author_id'], 'author_id');
        if (count($author) < 1) {
          continue;
        }
        $author_name = $author[0]['author_name'];
        // get id in new database
        $author_id = $obj_b->getID('mst_author', 'author_id', 'author_name', $author_name);
        $_ba['biblio_id'] = $b_ID['new'];
        $_ba['author_id'] = $author_id;
        $_ba['level'] = $ba['level'];
        // insert into biblio_author
        $insert = $obj_b->insertIgnore('biblio_author', $_ba);
        $_return['count']['biblio_author']++;
      }
      $biblio_author = null; // melegakan memory

      // get & insert biblio topic
      // ----------------------------------------------------------------------
      $biblio_topic = $obj_a->getByID('biblio_topic', $b_ID['old'], 'biblio_id');
      foreach ($biblio_topic as $bt) {
        $topic = $obj_a->getByID('mst_topic', $bt['topic_id'], 'topic_id');
        if (count($topic) < 1) {
          continue;
        }
        $topic_name = $topic[0]['topic'];
        // get topic id from new database
        $topic_id = $obj_b->getID('mst_topic', 'topic_id', 'topic', $topic_name);
        $_bt['biblio_id'] = $b_ID['new'];
        $_bt['topic_id'] = $topic_id;
        $_bt['level'] = $bt['level'];
        // insert into biblio_topic
        $insert = $obj_b->insertIgnore('biblio_topic', $_bt);
        $_return['count']['biblio_topic']++;
      }
      $biblio_topic = null;

      // get & insert item
      // ----------------------------------------------------------------------
      $items = $obj_a->getByID('item', $b_ID['old'], 'biblio_id');
      foreach ($items as $item) {
        // coll_type_id
        $coll_type = $obj_a->getByID('mst_coll_type', $item['coll_type_id'], 'coll_type_id');
        if (count($coll_type) > 0) {
          $coll_type_name = $coll_type[0]['coll_type_name'];
          $coll_type_id = $obj_b->getID('mst_coll_type', 'coll_type_id', 'coll_type_name', $coll_type_name);
        }
        // location_id
        // item_status_id
        $item['item_id'] = '';
        $item['biblio_id'] = $b_ID['new'];
        // insert into item
        $insert = $obj_b->insertIgnore('item', $item);
        $_return['count']['item']++;
      }
      $items = null;

      $bn++;
      Pindah::showLoading($bn);
    }

    if (empty($_return)) {
      $_return['status'] = 'OK';
    }
    echo PHP_EOL;
    Pindah::debug('Proses pemindahan selesai pada ' . date("Y-m-d H:i:s"));
    $time_elapsed_secs = microtime(true) - $start;
    Pindah::debug('Pemindahan memakan waktu ' . ($time_elapsed_secs/60) . ' menit');
    echo PHP_EOL;
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
