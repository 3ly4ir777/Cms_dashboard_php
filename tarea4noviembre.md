# Tarea 4 Noviembre

## 🔥 En progreso
- [ ] Fix: persistencia de carrito cliente con localStorage (código arriba)
- [ ] Diagnóstico: cliente_ordenes_api devuelve [] en curl pero UI muestra órdenes (SQL pending)

## 📌 Próximo
- [ ] Subir a InfinityFree (`ely4ir777.infinityfree.me/eliascms/`)
  - [ ] Crear `db_credentials.php` con datos del panel
  - [ ] Modificar `db.php` para leer credenciales
  - [ ] Subir zip, descomprimir, mover public/* a raíz
  - [ ] `.htaccess` en config/controllers/models
  - [ ] Correr migrar.php una vez y borrarlo

## 💡 Después
- [ ] Notificaciones al admin (polling cada 15s + toast Bootstrap)
- [ ] Imágenes de productos (URL primero, upload después)
- [ ] Slug de producto (`/producto/nombre-producto` con .htaccess RewriteRule)
- [ ] Categorías con tabla propia + FK

## 🏪 Futuro (CMS universal)
- [ ] Documentar en README: opciones A (1 tienda = 1 instalación) vs B (multi-tenant)
- [ ] Si da tiempo: agregar `tienda_id INT DEFAULT 1` a tablas (preparación)


subirlo a infinityfree
(revisar si seran 1 o 2 dominios frontend y backend)
revisar el carrito en cliente ya que no muestra los pedidos del cliente
notificaciones push para el administrador


**(recordatorio el CMS tiene el fin de ser universal para infinidad de tiendas con el mismo sistema)**


(no son aun tarea pero si es idea para mas adelante)
ver sobre las imagenes para los productos
lo de las categorias estaria bien darle un uso mas estructurado mas adelante
tambien ver mas adelante que al hacer click en un producto nos envie a la pagina del producto con una url que sea el nombre del producto