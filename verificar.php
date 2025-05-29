<?php
// verificar.php
header('Content-Type: application/json');

// 1) Leer ID que consulta el loader
$id = $_GET['id'] ?? '';
if (!$id) {
  echo json_encode(['status'=>'error']);
  exit;
}

// 2) Procesar actualizaciones de Telegram (polling con getUpdates)
// Guarda el último offset en un archivo
$offsetFile = 'offset.txt';
$offset = file_exists($offsetFile)
    ? (int)file_get_contents($offsetFile)
    : 0;

$TOKEN = '7704371778:AAF4h3i-QPKWv2w2M9O-zK32HeIxEhc1IvY';
$resp = file_get_contents("https://api.telegram.org/bot$TOKEN/getUpdates?offset=" . ($offset+1));
$updates = json_decode($resp, true);

if (!empty($updates['result'])) {
  foreach ($updates['result'] as $upd) {
    $offset = max($offset, $upd['update_id']);
    if (isset($upd['callback_query'])) {
      $data = $upd['callback_query']['data'];          // "aprobar:ID" o "rechazar:ID"
      list($accion, $qid) = explode(':', $data, 2);
      // Leer solicitudes
      $file = 'solicitudes.json';
      $list = json_decode(file_get_contents($file), true);
      foreach ($list as &$s) {
        if ($s['id'] === $qid) {
          $s['status'] = ($accion==='aprobar') ? 'aprobado' : 'rechazado';
          break;
        }
      }
      // Guardar cambios
      file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
  }
  // Actualizar offset
  file_put_contents($offsetFile, $offset);
}

// 3) Devolver el estado actual de la solicitud consultada
$solicitudes = json_decode(file_get_contents('solicitudes.json'), true);
$status = 'pendiente';
foreach ($solicitudes as $s) {
  if ($s['id'] === $id) {
    $status = $s['status'];
    break;
  }
}
echo json_encode(['status'=>$status]);
?>