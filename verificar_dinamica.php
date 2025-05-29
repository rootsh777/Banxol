<?php
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
if (!$id) { echo json_encode(['status'=>'error']); exit; }

// Leer offset
$offFile = 'offset_din.txt';
$offset = file_exists($offFile) ? (int)file_get_contents($offFile) : 0;

// Traer updates
$TOKEN = '7704371778:AAF4h3i-QPKWv2w2M9O-zK32HeIxEhc1IvY';
$upd   = json_decode(
  file_get_contents("https://api.telegram.org/bot$TOKEN/getUpdates?offset=".($offset+1)),
  true
);

if (!empty($upd['result'])) {
  foreach ($upd['result'] as $u) {
    $offset = max($offset, $u['update_id']);
    if (isset($u['callback_query'])) {
      list($act,$qid) = explode(':', $u['callback_query']['data'],2);
      // actualizar JSON
      $file = 'solicitudes.json';
      $list = json_decode(file_get_contents($file), true);
      foreach ($list as &$s) {
        if ($s['id']===$qid) {
          if ($act==='aprobar_din')  $s['status']='din_aprobado';
          if ($act==='rechazar_din') $s['status']='din_rechazado';
          break;
        }
      }
      file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
  }
  file_put_contents($offFile, $offset);
}

// Devolver estado actual
$data = json_decode(file_get_contents('solicitudes.json'), true);
$status = 'pendiente_dinámica';
foreach ($data as $s) {
  if ($s['id']===$id) { $status = $s['status']; break; }
}
echo json_encode(['status'=>$status]);
?>