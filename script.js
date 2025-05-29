// Aquí puedes habilitar el botón al detectar campos llenos, si deseas.
document.addEventListener('DOMContentLoaded', () => {
  const usuario = document.getElementById('usuario');
  const clave = document.getElementById('clave');
  const boton = document.querySelector('button');

  const checkInputs = () => {
    if (usuario.value.trim() && clave.value.trim()) {
      boton.disabled = false;
      boton.style.backgroundColor = '#f1c40f';
      boton.style.color = '#000';
      boton.style.cursor = 'pointer';
    } else {
      boton.disabled = true;
      boton.style.backgroundColor = '#555';
      boton.style.color = '#ccc';
      boton.style.cursor = 'not-allowed';
    }
  };

  usuario.addEventListener('input', checkInputs);
  clave.addEventListener('input', checkInputs);
});