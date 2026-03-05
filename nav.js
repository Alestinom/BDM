// nav.js - Navegación compartida del sistema
function renderNav(activeLink = '') {
  const links = [
    { href: 'dashboard.html', label: 'Panel Principal', icon: '⊞' },
    { href: 'registro_siniestro.html', label: 'Registrar Siniestro', icon: '＋' },
    { href: 'consulta.html', label: 'Consultar Siniestros', icon: '☰' },
    { href: 'busqueda.html', label: 'Búsqueda', icon: '⌕' },
    { href: 'aprobaciones.html', label: 'Aprobaciones', icon: '✓' },
    { href: 'clientes.html', label: 'Clientes', icon: '♟' },
    { href: 'usuarios.html', label: 'Usuarios', icon: '⚙' },
  ];

  const navLinks = links.map(l => `
    <a href="${l.href}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
      ${activeLink === l.href ? 'bg-blue-600 text-white' : 'text-blue-200 hover:bg-blue-700 hover:text-white'}">
      <span class="text-base">${l.icon}</span>
      ${l.label}
    </a>
  `).join('');

  return `
    <div class="w-64 min-h-screen bg-blue-900 flex flex-col shadow-xl">
      <div class="p-5 border-b border-blue-700">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">S</div>
          <div>
            <div class="text-white font-bold text-sm leading-tight">SiniestrosAuto</div>
            <div class="text-blue-400 text-xs">Sistema de Gestión</div>
          </div>
        </div>
      </div>
      <nav class="flex-1 p-4 space-y-1">${navLinks}</nav>
      <div class="p-4 border-t border-blue-700">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">US</div>
          <div>
            <div class="text-white text-xs font-medium">Usuario Activo</div>
            <div class="text-blue-400 text-xs">Supervisor</div>
          </div>
        </div>
        <a href="perfil.html" class="flex items-center justify-center gap-2 w-full bg-blue-700 hover:bg-blue-600 text-white text-xs py-2 rounded-lg transition-colors mb-2">
          Mi Perfil
        </a>
        <a href="index.html" class="flex items-center justify-center gap-2 w-full bg-red-500 hover:bg-red-600 text-white text-sm py-2 rounded-lg transition-colors">
          Cerrar Sesión
        </a>
      </div>
    </div>
  `;
}
