# Almacén de Calzado "El Paso Perfecto"

Proyecto escolar — Sistema web estático para un almacén de calzado, conectado conceptualmente a una base de datos MongoDB Atlas (`almacen_calzado`). El sitio funciona **100% en el navegador** abriendo `index.html`, sin servidor ni backend.

---

## Objetivos del Proyecto

* Mostrar dinámicamente un catálogo de productos cargado desde un archivo JSON.
* Presentar evidencias visuales (capturas) de las consultas realizadas en MongoDB Atlas.
* Implementar un diseño moderno, limpio y responsive utilizando únicamente HTML, CSS y JavaScript puro.

---

## Funcionalidades Implementadas

| # | Funcionalidad | Estado |
|---|---|---|
| 1 | Header con nombre del almacén y logo | ✔ |
| 2 | Menú de navegación (Inicio, Productos, Contacto, Evidencias) | ✔ |
| 3 | Banner principal (Hero) con animaciones | ✔ |
| 4 | Strip de características destacadas | ✔ |
| 5 | Catálogo dinámico cargado desde `productos.json` (fetch) | ✔ |
| 6 | Filtros por marca (Nike, Adidas, Vans, etc.) | ✔ |
| 7 | Tarjetas de producto con nombre, marca, precio, talla, color y stock | ✔ |
| 8 | Sección de Evidencias estilo **galería de polaroids** con animaciones | ✔ |
| 9 | Lightbox para ver evidencias ampliadas | ✔ |
| 10 | Sección de contacto (teléfono, dirección, correo, WhatsApp) | ✔ |
| 11 | Footer con nombre del proyecto y año | ✔ |
| 12 | Diseño responsive (móvil, tablet, escritorio) | ✔ |
| 13 | Efectos hover, sombras suaves y animaciones | ✔ |
| 14 | Menú móvil hamburguesa | ✔ |
| 15 | Scroll-spy (sección activa en la navegación) | ✔ |

---

## Estructura del Proyecto

```
/
├── index.html          # Página principal (todas las secciones)
├── styles.css          # Estilos generales + polaroids + responsive
├── script.js           # Carga JSON, filtros, lightbox, menú
├── productos.json      # 10 productos del almacén
├── clientes.json       # 5 clientes
├── ventas.json         # 5 ventas
├── images/
│   ├── evidencia1.png  # db.productos.find()
│   ├── evidencia2.png  # { marca: "Nike" }
│   ├── evidencia3.png  # { precio: { $gt: 25 } }
│   ├── evidencia4.png  # { stock: { $lt: 5 } }
│   ├── evidencia5.png  # sort({ precio: 1 })
│   ├── evidencia6.png  # db.clientes.find()
│   ├── evidencia7.png  # db.Ventas.find()
│   └── evidencia8.png  # { numero_venta: "V001" }
└── README.md
```

---

## URIs / Rutas Disponibles

Como es un sitio estático SPA-like, la navegación es por **anclas**:

| Ruta | Descripción |
|---|---|
| `index.html` | Página principal completa |
| `index.html#inicio` | Banner / Hero |
| `index.html#productos` | Catálogo dinámico de productos |
| `index.html#evidencias` | Galería de polaroids con consultas MongoDB |
| `index.html#contacto` | Información de contacto |

**Recursos de datos (servidos como archivos estáticos):**

| Recurso | Descripción |
|---|---|
| `productos.json` | Listado de 10 productos |
| `clientes.json` | Listado de 5 clientes |
| `ventas.json` | Listado de 5 ventas |

---

## Modelo de Datos

### Colección `productos`
```json
{ "codigo": "P001", "nombre": "Air Max 90", "marca": "Nike",
  "talla": 42, "color": "Blanco", "precio": 120, "stock": 15 }
```

### Colección `clientes`
```json
{ "codigo_cliente": "C001", "nombre": "Carlos", "apellido": "López",
  "telefono": "7777-1111", "direccion": "San Salvador" }
```

### Colección `ventas`
```json
{ "numero_venta": "V001", "fecha": "2026-05-01", "cliente": "Carlos López",
  "producto": "Air Max 90", "cantidad": 1, "total": 120 }
```

---

## Cómo ejecutar

> ⚠ Como el proyecto usa `fetch()` para cargar `productos.json`, **debe abrirse mediante un servidor local** (de lo contrario, el navegador bloquea la lectura por la política `file://`).

**Opción 1 — VS Code:**
1. Instala la extensión **Live Server**.
2. Clic derecho sobre `index.html` → *Open with Live Server*.

**Opción 2 — Python:**
```bash
python -m http.server 8080
```
Y abrir `http://localhost:8080`.

**Opción 3 — Node:**
```bash
npx serve
```

---

## Tecnologías

* **HTML5** semántico
* **CSS3** (Grid, Flexbox, animaciones, variables, responsive)
* **JavaScript ES6+** puro (fetch, async/await, IntersectionObserver)
* **Font Awesome 6** (iconos vía CDN)
* **Google Fonts** — Poppins + Caveat (cursiva para polaroids)

---

## Funcionalidades No Implementadas / Próximos Pasos

* Carrito de compras funcional (actualmente solo es animación visual).
* Persistencia local con `localStorage`.
* Búsqueda por texto en el catálogo.
* Sección dinámica de clientes y ventas (los JSON existen pero no se renderizan).
* Modo oscuro.
* Formulario de contacto funcional.
* Conexión real a MongoDB (requeriría backend, fuera de alcance del proyecto escolar).

---

## Autor

**Proyecto Escolar 2026** — *Almacén de Calzado "El Paso Perfecto"*

© 2026 — Todos los derechos reservados.
