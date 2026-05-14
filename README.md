# Almacén de Calzado "El Paso Perfecto" — PHP + Base de Datos

Proyecto escolar — Sistema web completo en **PHP** con base de datos `almacen_calzado` (estilo MongoDB) para un almacén de calzado. Incluye catálogo dinámico, panel de base de datos con consultas en vivo, carrito de compras funcional y checkout que registra ventas en la base de datos.

---

## Tecnologías

* **Backend:** PHP 8.4 (servidor embebido)
* **Base de datos:** SQLite con capa de abstracción `MongoLikeDB` que emula la API de MongoDB (`find`, `findOne`, `insertOne`, `$gt`, `$lt`, `sort`, `ObjectId`, etc.). Los documentos se almacenan como JSON dentro de cada "colección".
* **Frontend:** HTML5, CSS3 (Grid, Flexbox, animaciones), JavaScript ES6+ (fetch para carrito AJAX)
* **Fuentes / iconos:** Google Fonts (Poppins, Caveat, JetBrains Mono) + Font Awesome 6

---

## Estructura

```
/
├── index.php              # Página principal (hero, catálogo, contacto)
├── db.php                 # Panel de la base de datos (conexión, colecciones, consultas, evidencias)
├── carrito.php            # Carrito de compras (sesión PHP)
├── checkout.php           # Finalizar compra (registra cliente y venta en DB)
├── includes/
│   ├── db.php             # Conexión + emulador MongoDB sobre SQLite
│   ├── helpers.php        # Helpers + carrito en sesión
│   ├── header.php         # Header/nav común
│   └── footer.php         # Footer común
├── db/
│   └── almacen_calzado.sqlite   # Base de datos (auto-creada y poblada al primer acceso)
├── images/
│   ├── evidencia1.png ... evidencia8.png   # Capturas de MongoDB Atlas
├── productos.json         # Seed: 10 productos
├── clientes.json          # Seed: 5 clientes
├── ventas.json            # Seed: 5 ventas
├── styles.css             # Estilos completos
└── script.js              # Carrito AJAX + lightbox + menú
```

---

## Funcionalidades

| # | Funcionalidad | Estado |
|---|---|---|
| 1 | Página principal con hero, productos y contacto | ✔ |
| 2 | Catálogo cargado dinámicamente desde la BD | ✔ |
| 3 | Filtros por marca (Nike, Adidas, Vans, etc.) | ✔ |
| 4 | **Carrito de compras funcional** (añadir, modificar cantidad, eliminar, vaciar) | ✔ |
| 5 | Carrito persistente en sesión PHP | ✔ |
| 6 | Añadir al carrito vía **AJAX** (sin recargar página) | ✔ |
| 7 | Checkout que registra cliente y ventas en la BD | ✔ |
| 8 | **Sección `db.php`** con estado de conexión en vivo | ✔ |
| 9 | Visualizador de colecciones estilo MongoDB Compass (JSON + ObjectId) | ✔ |
| 10 | **8 consultas del rubro** ejecutadas en vivo contra la BD | ✔ |
| 11 | Mongo Shell simulada con `db.runCommand({ping:1})`, `show collections`, `db.stats()` | ✔ |
| 12 | Galería polaroid con capturas originales de MongoDB Atlas | ✔ |
| 13 | Diseño responsive (móvil, tablet, escritorio) | ✔ |
| 14 | Menú móvil hamburguesa | ✔ |
| 15 | Toast de notificaciones + contador animado del carrito | ✔ |

---

## Modelo de Datos

Base de datos: **`almacen_calzado`** — 3 colecciones.

### `productos` (10 documentos)
```json
{ "_id": "ObjectId(...)", "codigo": "P001", "nombre": "Air Max 90",
  "marca": "Nike", "talla": 42, "color": "Blanco", "precio": 120, "stock": 15 }
```

### `clientes` (5 documentos)
```json
{ "_id": "ObjectId(...)", "codigo_cliente": "C001", "nombre": "Carlos",
  "apellido": "López", "telefono": "7777-1111", "direccion": "San Salvador" }
```

### `ventas` (5 documentos)
```json
{ "_id": "ObjectId(...)", "numero_venta": "V001", "fecha": "2026-05-01",
  "cliente": "Carlos López", "producto": "Air Max 90", "cantidad": 1, "total": 120 }
```

---

## Consultas implementadas (Parte 3 del rubro)

Las 8 consultas se ejecutan en vivo al abrir `db.php`:

| # | Consulta MongoDB | Código PHP equivalente |
|---|---|---|
| 1 | `db.productos.find()` | `db()->find('productos')` |
| 2 | `db.productos.find({ marca: "Nike" })` | `db()->find('productos', ['marca' => 'Nike'])` |
| 3 | `db.productos.find({ precio: { $gt: 25 } })` | `db()->find('productos', ['precio' => ['$gt' => 25]])` |
| 4 | `db.productos.find({ stock: { $lt: 5 } })` | `db()->find('productos', ['stock' => ['$lt' => 5]])` |
| 5 | `db.productos.find().sort({ precio: 1 })` | `db()->find('productos', [], ['sort' => ['precio' => 1]])` |
| 6 | `db.clientes.find()` | `db()->find('clientes')` |
| 7 | `db.ventas.find()` | `db()->find('ventas')` |
| 8 | `db.ventas.findOne({ numero_venta: "V001" })` | `db()->findOne('ventas', ['numero_venta' => 'V001'])` |

---

## Cómo ejecutar

### Opción 1 — PHP servidor embebido (recomendado)
```bash
php -S 0.0.0.0:8080
```
Luego abre: `http://localhost:8080/index.php`

> La base de datos `db/almacen_calzado.sqlite` se crea automáticamente la primera vez y se puebla con los 10 productos, 5 clientes y 5 ventas desde los archivos JSON.

### Opción 2 — XAMPP / WAMP / LAMP
1. Copia toda la carpeta a `htdocs/almacen-calzado/`
2. Abre `http://localhost/almacen-calzado/index.php`

---

## Flujo de uso

1. **Inicio (`index.php`)** → ver catálogo cargado desde la BD, filtrar por marca, añadir productos al carrito.
2. **Carrito (`carrito.php`)** → modificar cantidades, eliminar, vaciar.
3. **Checkout (`checkout.php`)** → llenar datos del cliente; al confirmar:
   - Si el teléfono ya existe → reutiliza el cliente.
   - Si es nuevo → genera código `C00X` y lo inserta en la colección `clientes`.
   - Genera una venta `V00X` por cada producto del carrito en la colección `ventas`.
4. **DB (`db.php`)** → verificar conexión, ver colecciones, ejecutar consultas, ver evidencias.

---

## Autor

**Proyecto Escolar 2026** — *Almacén de Calzado "El Paso Perfecto"*
Base de datos `almacen_calzado` · PHP · MongoDB-compatible API.

© 2026 — Todos los derechos reservados.
