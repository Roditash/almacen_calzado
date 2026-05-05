/* ============================================
   ALMACÉN DE CALZADO "EL PASO PERFECTO"
   Lógica principal — Proyecto Escolar 2026
   ============================================ */

// Iconos representativos según marca
const ICONOS_MARCA = {
  'Nike':        'fa-solid fa-shoe-prints',
  'Adidas':      'fa-solid fa-person-running',
  'Vans':        'fa-solid fa-skating',
  'Converse':    'fa-solid fa-shoe-prints',
  'Puma':        'fa-solid fa-paw',
  'Reebok':      'fa-solid fa-dumbbell',
  'New Balance': 'fa-solid fa-walking'
};

let productosCargados = [];
let filtroActivo = 'all';

// ============================================
// CARGAR PRODUCTOS DESDE JSON
// ============================================
async function cargarProductos() {
  try {
    const response = await fetch('productos.json');
    if (!response.ok) throw new Error('Error al cargar productos.json');
    const productos = await response.json();
    productosCargados = productos;
    renderizarProductos(productos);
  } catch (error) {
    console.error(error);
    document.getElementById('products-grid').innerHTML = `
      <div class="loading" style="color:#dc3545;">
        <i class="fas fa-exclamation-triangle"></i>
        Error al cargar los productos. Asegúrate de abrir el sitio con un servidor local
        (puede usar la extensión "Live Server" o ejecutar <code>python -m http.server</code>).
      </div>`;
  }
}

// ============================================
// RENDERIZAR PRODUCTOS EN GRID
// ============================================
function renderizarProductos(lista) {
  const grid = document.getElementById('products-grid');

  if (lista.length === 0) {
    grid.innerHTML = `
      <div class="loading">
        <i class="fas fa-box-open"></i> No hay productos para esta marca.
      </div>`;
    return;
  }

  grid.innerHTML = lista.map((p, idx) => {
    const icono = ICONOS_MARCA[p.marca] || 'fa-solid fa-shoe-prints';
    const stockClass = p.stock < 10 ? 'low' : '';
    const stockText  = p.stock < 10 ? `¡Quedan ${p.stock}!` : `Stock: ${p.stock}`;

    return `
      <article class="product-card" style="animation: fadeInUp 0.5s ease ${idx * 0.05}s backwards;">
        <div class="product-image">
          <span class="product-badge">${p.codigo}</span>
          <span class="stock-badge ${stockClass}">${stockText}</span>
          <i class="${icono}"></i>
        </div>
        <div class="product-info">
          <p class="product-marca">${p.marca}</p>
          <h3 class="product-name">${p.nombre}</h3>
          <div class="product-meta">
            <span><i class="fas fa-ruler"></i> Talla ${p.talla}</span>
            <span><i class="fas fa-palette"></i> ${p.color}</span>
          </div>
          <div class="product-footer">
            <p class="product-price">$${p.precio}<small> USD</small></p>
            <button class="add-cart" aria-label="Añadir al carrito" title="Añadir al carrito">
              <i class="fas fa-cart-plus"></i>
            </button>
          </div>
        </div>
      </article>
    `;
  }).join('');

  // Pequeña interacción al hacer clic en "añadir"
  document.querySelectorAll('.add-cart').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      btn.innerHTML = '<i class="fas fa-check"></i>';
      btn.style.background = '#28a745';
      setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-cart-plus"></i>';
        btn.style.background = '';
      }, 1200);
    });
  });
}

// ============================================
// FILTROS POR MARCA
// ============================================
function inicializarFiltros() {
  const filtros = document.querySelectorAll('.filter-btn');
  filtros.forEach(btn => {
    btn.addEventListener('click', () => {
      filtros.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filtroActivo = btn.dataset.filter;

      const filtrados = filtroActivo === 'all'
        ? productosCargados
        : productosCargados.filter(p => p.marca === filtroActivo);

      renderizarProductos(filtrados);
    });
  });
}

// ============================================
// EVIDENCIAS — POLAROID GALLERY
// ============================================
const EVIDENCIAS = [
  {
    img: 'images/evidencia1.png',
    titulo: 'Listar Productos',
    descripcion: 'db.productos.find()'
  },
  {
    img: 'images/evidencia2.png',
    titulo: 'Filtro por Marca Nike',
    descripcion: '{ marca: "Nike" }'
  },
  {
    img: 'images/evidencia3.png',
    titulo: 'Precio mayor a $25',
    descripcion: '{ precio: { $gt: 25 } }'
  },
  {
    img: 'images/evidencia4.png',
    titulo: 'Stock menor a 5',
    descripcion: '{ stock: { $lt: 5 } }'
  },
  {
    img: 'images/evidencia5.png',
    titulo: 'Consulta con Sort',
    descripcion: 'sort({ precio: 1 })'
  },
  {
    img: 'images/evidencia6.png',
    titulo: 'Listar Clientes',
    descripcion: 'db.clientes.find()'
  },
  {
    img: 'images/evidencia7.png',
    titulo: 'Listar Ventas',
    descripcion: 'db.Ventas.find()'
  },
  {
    img: 'images/evidencia8.png',
    titulo: 'Buscar Venta V001',
    descripcion: '{ numero_venta: "V001" }'
  }
];

function renderizarEvidencias() {
  const gallery = document.getElementById('evidence-gallery');
  gallery.innerHTML = EVIDENCIAS.map(ev => `
    <figure class="polaroid" data-img="${ev.img}">
      <img src="${ev.img}" alt="${ev.titulo}" loading="lazy">
      <figcaption class="polaroid-caption">
        <h4>${ev.titulo}</h4>
        <p>${ev.descripcion}</p>
      </figcaption>
    </figure>
  `).join('');

  // Lightbox al hacer clic
  document.querySelectorAll('.polaroid').forEach(p => {
    p.addEventListener('click', () => abrirLightbox(p.dataset.img));
  });
}

// ============================================
// LIGHTBOX
// ============================================
function crearLightbox() {
  const lb = document.createElement('div');
  lb.className = 'lightbox';
  lb.id = 'lightbox';
  lb.innerHTML = `
    <button class="lightbox-close" aria-label="Cerrar"><i class="fas fa-times"></i></button>
    <img src="" alt="Evidencia ampliada" id="lightbox-img">
  `;
  document.body.appendChild(lb);

  lb.addEventListener('click', (e) => {
    if (e.target === lb || e.target.closest('.lightbox-close')) {
      lb.classList.remove('active');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') lb.classList.remove('active');
  });
}

function abrirLightbox(src) {
  const lb = document.getElementById('lightbox');
  document.getElementById('lightbox-img').src = src;
  lb.classList.add('active');
}

// ============================================
// MENÚ MÓVIL
// ============================================
function inicializarMenu() {
  const toggle = document.getElementById('menu-toggle');
  const nav = document.getElementById('main-nav');

  toggle.addEventListener('click', () => {
    nav.classList.toggle('open');
  });

  // Cerrar menú al hacer clic en un link
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
    });
  });
}

// ============================================
// NAV ACTIVO POR SCROLL
// ============================================
function inicializarScrollSpy() {
  const links = document.querySelectorAll('.nav-link');
  const sections = ['inicio', 'productos', 'contacto', 'evidencias']
    .map(id => document.getElementById(id))
    .filter(Boolean);

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        links.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === `#${id}`);
        });
      }
    });
  }, { rootMargin: '-40% 0px -55% 0px' });

  sections.forEach(s => observer.observe(s));
}

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', () => {
  cargarProductos();
  inicializarFiltros();
  renderizarEvidencias();
  crearLightbox();
  inicializarMenu();
  inicializarScrollSpy();

  console.log('%c🥿 El Paso Perfecto — Almacén de Calzado',
              'color:#ff6b35; font-size:16px; font-weight:bold;');
  console.log('Proyecto Escolar 2026 — MongoDB + HTML + CSS + JS');
});
