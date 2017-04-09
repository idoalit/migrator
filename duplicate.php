<?php

require 'pemindah.lib.php';

$config['db'] = array(
  'host' => 'localhost',
  'port' => 3306,
  'name' => 'senayandb_1',
  'user' => 'root',
  'pass' => ''
);
$start = microtime(true);
Pindah::debug('Aplikasi berjalan pada ' . date("Y-m-d H:i:s"));
Pindah::debug('Mencoba mengkoneksi database');
$obj = new Pindah($config['db']);
$db  = $obj->getConnection();
// melihat data pinjaman
$loan_count = $obj->count('loan')[0];
Pindah::debug('Mengamankan data biblio terkait dengan peminjaman.');
Pindah::debug('Data pinjaman sejumlah: ' . $loan_count);
$limit_loan = 100;
$loop_for_loan = ceil($loan_count / $limit_loan);
$biblio_loan = array();
echo PHP_EOL;
for ($l=0; $l < $loop_for_loan; $l++) {
  $loan_q = $db->query('SELECT DISTINCT i.biblio_id FROM loan AS l
    LEFT JOIN item AS i ON i.item_code=l.item_code LIMIT '.$limit_loan.' OFFSET '.($l*$limit_loan));
  // menyimpan biblio id ke dalam array
  while ($loan = $loan_q->fetch()) {
    $biblio_loan[$loan['biblio_id']] = $loan['biblio_id'];
    echo '.';
  }
}
Pindah::debug('Data biblio terkait loan sejumlah: ' . count($biblio_loan));
// ----------------------------------------------------------------------------
$biblio_count = $obj->count('biblio')[0];
Pindah::debug('Total data biblio sejumlah: ' . $biblio_count);
$limit_biblio = 8400;
$loop_for_biblio = ceil($biblio_count/$limit_biblio);
for ($b=0; $b < $loop_for_biblio; $b++) {
  $dari = ($b*$limit_biblio) + 1;
  $sampai = ($biblio_count < ($b+1)*$limit_biblio) ? $biblio_count : $dari + $limit_biblio - 1 ;
  Pindah::debug('Memproses data biblio dari cantuman ke ' . $dari . ' sampai ' . $sampai . ' dari total ' . $biblio_count);
  // --------------------------------------------------------------------------
  $distinct_title = $db->query('SELECT jml.title, jml.biblio_id FROM (SELECT count(*) AS jumlah, title, biblio_id
    FROM biblio GROUP BY title) AS jml WHERE jml.jumlah > 1 ORDER BY jml.jumlah DESC LIMIT '.$limit_biblio.' OFFSET '.($b*$limit_biblio));
  while ($biblio = $distinct_title->fetch()) {
    if (!in_array($biblio['biblio_id'], $biblio_loan)) {
      // mengambil data biblio dengan judul yang sama
      $sth = $db->prepare('SELECT biblio_id FROM biblio WHERE title=? ORDER BY biblio_id ASC');
      $sth->execute(array($biblio['title']));
      $_biblio = $sth->fetchAll(PDO::FETCH_ASSOC);
      if (count($_biblio) > 1) {
        // gunakan biblio_id terkecil
        $biblio_id = $_biblio[0]['biblio_id'];
        // update data biblio
        foreach ($_biblio as $_b) {
          $db->query('UPDATE item SET biblio_id='.$biblio_id.' WHERE biblio_id='.$_b['biblio_id']);
        }
      }
    }
    echo '.';
  }
}

Pindah::debug('Menghapus data yang sudah tidak terpakai');
$db->query('DELETE b.*, ba.*, bt.* FROM biblio AS b
  LEFT JOIN biblio_author AS ba ON b.biblio_id=ba.biblio_id
  LEFT JOIN biblio_topic AS bt ON b.biblio_id=bt.biblio_id
  LEFT JOIN item AS i ON i.biblio_id=b.biblio_id
  WHERE i.item_code IS NULL
');

// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
Pindah::debug('Proses selesai pada ' . date("Y-m-d H:i:s"));
$time_elapsed_secs = microtime(true) - $start;
Pindah::debug('Proses memakan waktu ' . ($time_elapsed_secs/60) . ' menit');
