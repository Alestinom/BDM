/**
 * session.js — Sesión y control de acceso por roles
 * En producción se reemplaza por PHP $_SESSION
 */
const SESSION = (() => {

  const PERMISOS = {
    Supervisor: [
      'dashboard.html','aprobaciones.html','consulta.html','busqueda.html',
      'clientes.html','usuarios.html','registro.html','registro_siniestro.html',
      'detalle_siniestro.html','seguimiento.html','perfil.html',
      'companias.html','polizas.html',
    ],
    Ajustador: [
      'dashboard.html','registro_siniestro.html','consulta.html',
      'busqueda.html','detalle_siniestro.html','seguimiento.html','perfil.html',
      'clientes.html','registro.html',
    ],
    Asegurado: [
      'mis_siniestros.html','seguimiento.html','perfil.html',
    ],
  };

  const HOME = {
    Supervisor: 'dashboard.html',
    Ajustador:  'dashboard.html',
    Asegurado:  'mis_siniestros.html',
  };

  /** Rutas relativas: index en raíz, resto en pages/ */
  function _inPages() {
    try {
      return window.location.pathname.replace(/\\/g, '/').includes('/pages/');
    } catch {
      return false;
    }
  }
  function _pathIndex() {
    return _inPages() ? '../index.html' : 'index.html';
  }
  function _pathPage(filename) {
    if (!filename || filename === 'index.html') return _pathIndex();
    return _inPages() ? filename : 'pages/' + filename;
  }

  function _get() {
    try { return JSON.parse(sessionStorage.getItem('siniestros_session')) || null; }
    catch { return null; }
  }

  function _set(data) {
    sessionStorage.setItem('siniestros_session', JSON.stringify(data));
  }

  return {
    get datos()     { return _get(); },
    get tipo()      { return (_get() || {}).tipo || null; },
    get nombre()    { return (_get() || {}).nombre || 'Usuario'; },
    get iniciales() { return (_get() || {}).iniciales || 'US'; },
    get home()      { return _pathPage(HOME[this.tipo] || 'index.html'); },

    requerir(pagina) {
      const sesion = _get();
      if (!sesion) { window.location.href = _pathIndex(); return; }
      const lista = PERMISOS[sesion.tipo] || [];
      if (!lista.includes(pagina)) {
        window.location.href = _pathPage(HOME[sesion.tipo] || 'index.html');
      }
    },

    puede(pagina) {
      const lista = PERMISOS[this.tipo] || [];
      return lista.includes(pagina);
    },

    es(...tipos) { return tipos.includes(this.tipo); },

    cerrar() {
      sessionStorage.removeItem('siniestros_session');
      window.location.href = _pathIndex();
    },
  };
})();
