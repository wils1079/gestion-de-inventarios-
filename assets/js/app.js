let products = [];
let categories = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadProducts();
    loadStats();
    
    document.getElementById('productForm').addEventListener('submit', handleProductSubmit);
    document.getElementById('categoryForm').addEventListener('submit', handleCategorySubmit);
    
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProducts();
        }
    });
});

async function loadStats() {
    try {
        const response = await fetch('api/products.php?action=stats');
        const stats = await response.json();
        
        document.getElementById('totalProductos').textContent = stats.total_productos;
        document.getElementById('stockBajo').textContent = stats.stock_bajo;
        document.getElementById('valorTotal').textContent = '$' + (stats.valor_total || 0).toFixed(2);
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}

async function loadCategories() {
    try {
        const response = await fetch('api/categories.php');
        categories = await response.json();
        
        const categoryFilter = document.getElementById('categoryFilter');
        const productCategory = document.getElementById('productCategory');
        
        categoryFilter.innerHTML = '<option value="">Todas las categorías</option>';
        productCategory.innerHTML = '<option value="">Sin categoría</option>';
        
        categories.forEach(cat => {
            categoryFilter.innerHTML += `<option value="${cat.id}">${cat.nombre}</option>`;
            productCategory.innerHTML += `<option value="${cat.id}">${cat.nombre}</option>`;
        });
        
        renderCategories(categories);
    } catch (error) {
        console.error('Error cargando categorías:', error);
    }
}

function renderCategories(categoryList) {
    const grid = document.getElementById('categoriesGrid');
    
    if (categoryList.length === 0) {
        grid.innerHTML = '<div class="loading">No hay categorías disponibles</div>';
        return;
    }
    
    grid.innerHTML = categoryList.map(cat => `
        <div class="category-card">
            <h3>${cat.nombre}</h3>
            <p>${cat.descripcion || 'Sin descripción'}</p>
            <div class="category-actions">
                <button onclick="editCategory(${cat.id})" class="btn btn-success">✏️ Editar</button>
                <button onclick="deleteCategory(${cat.id})" class="btn btn-danger">🗑️ Eliminar</button>
            </div>
        </div>
    `).join('');
}

async function loadProducts() {
    try {
        const response = await fetch('api/products.php');
        products = await response.json();
        renderProducts(products);
        loadStats();
    } catch (error) {
        console.error('Error cargando productos:', error);
        document.getElementById('productsBody').innerHTML = '<tr><td colspan="8" class="loading">Error cargando productos</td></tr>';
    }
}

function renderProducts(productList) {
    const tbody = document.getElementById('productsBody');
    
    if (productList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="loading">No hay productos disponibles</td></tr>';
        return;
    }
    
    tbody.innerHTML = productList.map(product => {
        const isLowStock = product.cantidad <= product.stock_minimo;
        const rowClass = isLowStock ? 'stock-low' : '';
        const valorTotal = (product.cantidad * product.precio).toFixed(2);
        
        return `
            <tr class="${rowClass}">
                <td>${product.id}</td>
                <td><strong>${product.nombre}</strong></td>
                <td>${product.descripcion || '-'}</td>
                <td>${product.categoria_nombre || 'Sin categoría'}</td>
                <td>
                    ${product.cantidad}
                    ${isLowStock ? '<span class="badge badge-warning">⚠️ Bajo</span>' : ''}
                </td>
                <td>$${parseFloat(product.precio).toFixed(2)}</td>
                <td>$${valorTotal}</td>
                <td class="actions-cell">
                    <button onclick="editProduct(${product.id})" class="btn btn-success">✏️ Editar</button>
                    <button onclick="deleteProduct(${product.id})" class="btn btn-danger">🗑️ Eliminar</button>
                </td>
            </tr>
        `;
    }).join('');
}

async function searchProducts() {
    const search = document.getElementById('searchInput').value;
    const categoria = document.getElementById('categoryFilter').value;
    
    try {
        const url = `api/products.php?action=search&q=${encodeURIComponent(search)}&categoria=${categoria}`;
        const response = await fetch(url);
        const results = await response.json();
        renderProducts(results);
    } catch (error) {
        console.error('Error buscando productos:', error);
    }
}

function showAddProductModal() {
    document.getElementById('modalTitle').textContent = 'Nuevo Producto';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productModal').style.display = 'block';
}

function showAddCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Nueva Categoría';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

async function handleProductSubmit(e) {
    e.preventDefault();
    
    const productData = {
        id: document.getElementById('productId').value,
        nombre: document.getElementById('productName').value,
        descripcion: document.getElementById('productDescription').value,
        categoria_id: document.getElementById('productCategory').value || null,
        cantidad: parseInt(document.getElementById('productQuantity').value),
        precio: parseFloat(document.getElementById('productPrice').value),
        stock_minimo: parseInt(document.getElementById('productMinStock').value)
    };
    
    try {
        const isEdit = productData.id !== '';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch('api/products.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(productData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal('productModal');
            loadProducts();
            alert(isEdit ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error guardando producto:', error);
        alert('Error guardando el producto');
    }
}

async function handleCategorySubmit(e) {
    e.preventDefault();
    
    const categoryData = {
        id: document.getElementById('categoryId').value,
        nombre: document.getElementById('categoryName').value,
        descripcion: document.getElementById('categoryDescription').value
    };
    
    try {
        const isEdit = categoryData.id !== '';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch('api/categories.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(categoryData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal('categoryModal');
            loadCategories();
            alert(isEdit ? 'Categoría actualizada exitosamente' : 'Categoría creada exitosamente');
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error guardando categoría:', error);
        alert('Error guardando la categoría');
    }
}

async function editCategory(id) {
    const category = categories.find(c => c.id === id);
    if (!category) return;
    
    document.getElementById('categoryModalTitle').textContent = 'Editar Categoría';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.nombre;
    document.getElementById('categoryDescription').value = category.descripcion || '';
    
    document.getElementById('categoryModal').style.display = 'block';
}

async function deleteCategory(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        return;
    }
    
    try {
        const response = await fetch(`api/categories.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadCategories();
            loadProducts();
            alert('Categoría eliminada exitosamente');
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error eliminando categoría:', error);
        alert('Error eliminando la categoría');
    }
}

async function editProduct(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;
    
    document.getElementById('modalTitle').textContent = 'Editar Producto';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.nombre;
    document.getElementById('productDescription').value = product.descripcion || '';
    document.getElementById('productCategory').value = product.categoria_id || '';
    document.getElementById('productQuantity').value = product.cantidad;
    document.getElementById('productPrice').value = product.precio;
    document.getElementById('productMinStock').value = product.stock_minimo;
    
    document.getElementById('productModal').style.display = 'block';
}

async function deleteProduct(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        return;
    }
    
    try {
        const response = await fetch(`api/products.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadProducts();
            alert('Producto eliminado exitosamente');
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error eliminando producto:', error);
        alert('Error eliminando el producto');
    }
}