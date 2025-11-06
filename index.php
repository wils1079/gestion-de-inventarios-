<?php
// Simple frontend page - loads assets and provides UI to interact with API
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema de Gestión de Inventarios - TechCo</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="hero">
    <div class="container">
      <h1>💼 Sistema de Gestión de Inventarios - TechCo</h1>
      <p class="subtitle">Control total del inventario de la compañía de tecnología</p>
    </div>
  </header>

  <main class="container">
    <section class="stats">
      <div class="card stat" id="card-total">
        <div class="big" id="totalProductos">0</div>
        <div class="label">Total Productos</div>
      </div>
      <div class="card stat" id="card-stock">
        <div class="big" id="stockBajo">0</div>
        <div class="label">Stock Bajo</div>
      </div>
      <div class="card stat" id="card-valor">
        <div class="big" id="valorTotal">$0.00</div>
        <div class="label">Valor Total</div>
      </div>
    </section>

    <section class="categories">
      <h2>📁 Categorías</h2>
      <div id="categoriesGrid" class="grid"></div>
      <button class="btn primary" id="btnAddCategory">+ Nueva Categoría</button>
    </section>

    <section class="searchbar">
      <input id="q" placeholder="Buscar productos..." />
      <select id="filterCategoria"><option value="">Todas las categorías</option></select>
      <button class="btn" id="btnSearch">Buscar</button>
      <button class="btn primary" id="btnAddProduct">+ Nuevo Producto</button>
    </section>

    <section class="tablewrap">
      <table id="productsTable">
        <thead>
          <tr>
            <th>ID</th><th>Producto</th><th>Descripción</th><th>Categoría</th><th>Cantidad</th><th>Precio</th><th>Valor Total</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>
  </main>

  <!-- Modals -->
  <div class="modal" id="modalProduct" aria-hidden="true">
    <div class="modal-content">
      <h3 id="productModalTitle">Nuevo Producto</h3>
      <form id="productForm">
        <input type="hidden" name="id" id="prod_id" />
        <label>Nombre*</label>
        <input name="nombre" id="prod_nombre" required />
        <label>Descripción</label>
        <textarea name="descripcion" id="prod_descripcion"></textarea>
        <label>Categoría</label>
        <select name="categoria_id" id="prod_categoria"></select>
        <label>Cantidad*</label>
        <input type="number" name="cantidad" id="prod_cantidad" value="0" required />
        <label>Precio*</label>
        <input type="number" step="0.01" name="precio" id="prod_precio" value="0.00" required />
        <label>Stock mínimo</label>
        <input type="number" name="stock_minimo" id="prod_stockmin" value="10" />
        <div class="modal-actions">
          <button type="submit" class="btn primary">Guardar</button>
          <button type="button" class="btn" id="closeProdModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="modalCategory" aria-hidden="true">
    <div class="modal-content">
      <h3 id="catModalTitle">Nueva Categoría</h3>
      <form id="catForm">
        <input type="hidden" name="id" id="cat_id" />
        <label>Nombre*</label>
        <input name="nombre" id="cat_nombre" required />
        <label>Descripción</label>
        <textarea name="descripcion" id="cat_descripcion"></textarea>
        <div class="modal-actions">
          <button type="submit" class="btn primary">Guardar</button>
          <button type="button" class="btn" id="closeCatModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="assets/js/app.js"></script>
</body>
</html>
