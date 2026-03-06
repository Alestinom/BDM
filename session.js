/**
 * session.js — Sesión y control de acceso por roles
 * En producción se reemplaza por PHP $_SESSION
 */
const SESSION = (() => {

  const USUARIOS_DEMO = [
    { correo: 'supervisor@demo.com', password: 'Super1@', tipo: 'Supervisor', nombre: 'Roberto Soto',   iniciales: 'RS' },
    { correo: 'ajustador@demo.com',  password: 'Ajust1@', tipo: 'Ajustador',  nombre: 'Luis Garza',     iniciales: 'LG' },
    { correo: 'asegurado@demo.com',  password: 'Asegu1@', tipo: 'Asegurado',  nombre: 'Juan Pérez',     iniciales: 'JP' },
  ];

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
    get home()      { return HOME[this.tipo] || 'index.html'; },

    login(correo, password) {
      const u = USUARIOS_DEMO.find(
        u => u.correo === correo.trim().toLowerCase() && u.password === password
      );
      if (!u) return { ok: false };
      _set({ tipo: u.tipo, nombre: u.nombre, iniciales: u.iniciales, correo: u.correo });
      return { ok: true };
    },

    requerir(pagina) {
      const sesion = _get();
      if (!sesion) { window.location.href = 'index.html'; return; }
      const lista = PERMISOS[sesion.tipo] || [];
      if (!lista.includes(pagina)) {
        window.location.href = HOME[sesion.tipo] || 'index.html';
      }
    },

    puede(pagina) {
      const lista = PERMISOS[this.tipo] || [];
      return lista.includes(pagina);
    },

    es(...tipos) { return tipos.includes(this.tipo); },

    cerrar() {
      sessionStorage.removeItem('siniestros_session');
      window.location.href = 'index.html';
    },

    get usuariosDemo() { return USUARIOS_DEMO; },
  };
})();
