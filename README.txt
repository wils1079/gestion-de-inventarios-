===========================================================
SISTEMA DE GESTIÓN DE INVENTARIOS - COMPAÑÍA DE productos variados
===========================================================

DESCRIPCIÓN:
Este sistema permite gestionar los productos, categorías y existencias
de una compañía de tecnología. Está desarrollado en PHP, HTML y CSS, 
utilizando MySQL como base de datos a través de phpMyAdmin.

-----------------------------------------------------------
CARACTERÍSTICAS PRINCIPALES:
- Administración de productos (crear, editar, eliminar, listar).
- Gestión de categorías.
- Validación de stock mínimo.
- Reportes del inventario.
- Diseño adaptable y fácil de usar.

-----------------------------------------------------------
REQUISITOS:
- PHP 8.0 o superior
- Servidor Apache (XAMPP, Laragon, WAMP, etc.)
- MySQL con phpMyAdmin
- Navegador web moderno

-----------------------------------------------------------
INSTALACIÓN:
1. Copiar el proyecto a la carpeta htdocs (por ejemplo: C:\xampp\htdocs).
2. Iniciar los servicios de Apache y MySQL desde XAMPP.
3. Crear la base de datos `inventario_db` en phpMyAdmin.
4. Importar el archivo SQL del proyecto (si existe).
5. Abrir el navegador y acceder a:
   http://localhost/gestion-de-inventarios/

-----------------------------------------------------------
ESTRUCTURA DEL PROYECTO:
- index.php ................ Página principal del sistema
- categorias.php ........... Módulo de categorías
- productos.php ............ Módulo de productos
- conexion.php ............. Configuración de la base de datos
- estilos/ ................. Archivos CSS
- scripts/ ................. Archivos JS (si existen)
- .github/workflows/ ....... Flujo de integración continua (GitHub Actions)

