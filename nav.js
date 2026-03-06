/**
 * nav.js — Sidebar dinámico con iconos SVG Heroicons
 */

// Librería de iconos SVG reutilizables
const ICON = {
  grid:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>`,
  plus:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>`,
  list:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>`,
  search:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/></svg>`,
  check:    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
  users:    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>`,
  cog:      `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
  building: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>`,
  document: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>`,
  chat:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>`,
  user:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>`,
  logout:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>`,
  shield:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>`,
};

function renderNav(activeLink = '') {

  const LINKS = {
    Supervisor: [
      { href: 'dashboard.html',          label: 'Panel Principal',      icon: ICON.grid     },
      { href: 'aprobaciones.html',        label: 'Aprobaciones',         icon: ICON.check    },
      { href: 'registro_siniestro.html',  label: 'Registrar Siniestro',  icon: ICON.plus     },
      { href: 'consulta.html',            label: 'Consultar Siniestros', icon: ICON.list     },
      { href: 'busqueda.html',            label: 'Búsqueda',             icon: ICON.search   },
      { href: 'clientes.html',            label: 'Clientes',             icon: ICON.users    },
      { href: 'registro.html',            label: 'Registrar Ajustador',  icon: ICON.user     },
      { href: 'companias.html',           label: 'Compañías',            icon: ICON.building },
      { href: 'polizas.html',             label: 'Pólizas',              icon: ICON.document },
      { href: 'usuarios.html',            label: 'Usuarios del Sistema', icon: ICON.cog      },
    ],
    Ajustador: [
      { href: 'dashboard.html',           label: 'Panel Principal',      icon: ICON.grid     },
      { href: 'registro_siniestro.html',  label: 'Registrar Siniestro',  icon: ICON.plus     },
      { href: 'registro.html',            label: 'Registrar Asegurado',  icon: ICON.user     },
      { href: 'consulta.html',            label: 'Siniestros',           icon: ICON.list     },
      { href: 'busqueda.html',            label: 'Búsqueda',             icon: ICON.search   },
      { href: 'seguimiento.html',         label: 'Seguimiento',          icon: ICON.chat     },
    ],
    Asegurado: [
      { href: 'mis_siniestros.html',      label: 'Mis Siniestros',       icon: ICON.shield   },
      { href: 'seguimiento.html',         label: 'Seguimiento',          icon: ICON.chat     },
    ],
  };

  const tipo      = SESSION.tipo || 'Supervisor';
  const nombre    = SESSION.nombre;
  const iniciales = SESSION.iniciales;
  const links     = LINKS[tipo] || LINKS['Supervisor'];

  const navLinks = links.map(l => `
    <a href="${l.href}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
      ${activeLink === l.href
        ? 'bg-blue-600 text-white shadow-sm'
        : 'text-blue-200 hover:bg-blue-700/60 hover:text-white'}">
      <span class="flex-shrink-0">${l.icon}</span>
      <span>${l.label}</span>
    </a>
  `).join('');

  const rolStyles = {
    Supervisor: 'bg-amber-400/20 text-amber-300 border border-amber-400/30',
    Ajustador:  'bg-teal-400/20 text-teal-300 border border-teal-400/30',
    Asegurado:  'bg-slate-400/20 text-slate-300 border border-slate-400/30',
  };
  const badge = rolStyles[tipo] || rolStyles['Asegurado'];

  return `
    <div class="w-64 min-h-screen bg-blue-950 flex flex-col shadow-xl flex-shrink-0 border-r border-blue-900">
      <div class="p-5 border-b border-blue-900">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
            ${ICON.shield.replace('class="w-5 h-5"','class="w-4 h-4 text-white"')}
          </div>
          <div>
            <div class="text-white font-bold text-sm leading-tight">SiniestrosAuto</div>
            <div class="text-blue-400 text-xs">Sistema de Gestión</div>
          </div>
        </div>
      </div>

      <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">${navLinks}</nav>

      <div class="p-3 border-t border-blue-900 space-y-2">
        <div class="flex items-center gap-3 px-2 py-1">
          <div class="w-8 h-8 bg-blue-700 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">${iniciales}</div>
          <div class="min-w-0">
            <div class="text-white text-xs font-medium truncate">${nombre}</div>
            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mt-0.5 ${badge}">${tipo}</span>
          </div>
        </div>
        <a href="perfil.html" class="flex items-center gap-2 w-full bg-blue-800/60 hover:bg-blue-700 text-blue-100 text-xs py-2 px-3 rounded-lg transition-colors">
          ${ICON.user.replace('class="w-5 h-5"','class="w-4 h-4"')}
          Mi Perfil
        </a>
        <button onclick="SESSION.cerrar()" class="flex items-center gap-2 w-full bg-red-500/80 hover:bg-red-500 text-white text-xs py-2 px-3 rounded-lg transition-colors">
          ${ICON.logout.replace('class="w-5 h-5"','class="w-4 h-4"')}
          Cerrar Sesión
        </button>
      </div>
    </div>
  `;
}
