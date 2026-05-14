/* ============================================
   ALMACÉN DE CALZADO "EL PASO PERFECTO"
   Lógica del frontend (PHP backend)
   ============================================ */

// ============================================
// CARRITO — AJAX (añadir sin recargar)
// ============================================
function inicializarCarritoAjax() {
  document.querySelectorAll('form.add-cart-form[data-ajax="1"]').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button.add-cart');
      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      try {
        const data = new FormData(form);
        const res = await fetch(form.action, {
          method: 'POST',
          body: data,
          headers: { 'X-Requested-With': 'fetch' }
        });
        const json = await res.json();

        // ✓ verde
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#28a745';

        // Actualizar contador en el nav
        const counter = document.getElementById('cart-count');
        if (counter && typeof json.count !== 'undefined') {
          counter.textContent = json.count;
          counter.classList.remove('bump');
          void counter.offsetWidth; // restart anim
          counter.classList.add('bump');
        }

        // Toast
        mostrarToast(`✓ Añadido al carrito · Total: $${(json.total || 0).toFixed(2)}`);

        setTimeout(() => {
          btn.innerHTML = originalHTML;
          btn.style.background = '';
          btn.disabled = false;
        }, 1200);
      } catch (err) {
        console.error(err);
        btn.innerHTML = '<i class="fas fa-times"></i>';
        btn.style.background = '#dc3545';
        setTimeout(() => {
          btn.innerHTML = originalHTML;
          btn.style.background = '';
          btn.disabled = false;
        }, 1500);
      }
    });
  });
}

// ============================================
// TOAST
// ============================================
function mostrarToast(mensaje) {
  let toast = document.getElementById('app-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'app-toast';
    toast.style.cssText = `
      position: fixed; bottom: 24px; right: 24px;
      background: #1a1a2e; color: #fff;
      padding: 14px 22px; border-radius: 30px;
      font-weight: 500; font-size: 0.92rem;
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
      z-index: 9999;
      opacity: 0; transform: translateY(20px);
      transition: all 0.4s ease;
      max-width: 90vw;
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = mensaje;
  toast.style.opacity = '1';
  toast.style.transform = 'translateY(0)';
  clearTimeout(toast._timer);
  toast._timer = setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
  }, 2400);
}

// ============================================
// LIGHTBOX (para polaroids de evidencias)
// ============================================
function crearLightbox() {
  if (document.getElementById('lightbox')) return;
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

function inicializarPolaroids() {
  document.querySelectorAll('.polaroid').forEach(p => {
    p.addEventListener('click', () => {
      const lb = document.getElementById('lightbox');
      if (!lb) return;
      document.getElementById('lightbox-img').src = p.dataset.img;
      lb.classList.add('active');
    });
  });
}

// ============================================
// MENÚ MÓVIL
// ============================================
function inicializarMenu() {
  const toggle = document.getElementById('menu-toggle');
  const nav = document.getElementById('main-nav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', () => nav.classList.toggle('open'));
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => nav.classList.remove('open'));
  });
}

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', () => {
  inicializarCarritoAjax();
  crearLightbox();
  inicializarPolaroids();
  inicializarMenu();

  console.log('%c🥿 El Paso Perfecto — Almacén de Calzado',
              'color:#ff6b35; font-size:16px; font-weight:bold;');
  console.log('Proyecto Escolar 2026 — PHP + Base de Datos almacen_calzado');
});
