# 💻 Sistema de Gestión de Inventarios - Compañía de Tecnología

Sistema web desarrollado en **PHP, HTML y CSS** con base de datos **MySQL**, diseñado para administrar productos, categorías y niveles de inventario de una empresa tecnológica.

---

## 🚀 Características Principales

- 🗂️ Gestión de categorías y productos  
- 📦 Registro, edición y eliminación de inventario  
- ⚠️ Alertas de stock bajo  
- 📊 Reporte general de existencias  
- 🔒 Conexión segura a base de datos (MySQL / phpMyAdmin)  
- 🧩 Integración continua con **GitHub Actions (workflow PHP CI)**

---

## 🛠️ Tecnologías Utilizadas

| Componente | Tecnología |
|-------------|-------------|
| Lenguaje | PHP 8.2 |
| Base de datos | MySQL 8.0 |
| Servidor | Apache (XAMPP / Laragon) |
| Frontend | HTML5, CSS3 |
| Control de versiones | Git + GitHub |

---

## ⚙️ Instalación y Configuración

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/tuusuario/gestion-de-inventarios.git
   ```
2. Copiar el proyecto a la carpeta de tu servidor local (`htdocs` o `www`).
3. Iniciar **Apache** y **MySQL** desde XAMPP o Laragon.
4. Crear la base de datos `inventario_db` en **phpMyAdmin**.
5. Importar el archivo SQL incluido (si existe).
6. Acceder desde tu navegador:
   ```
   http://localhost/gestion-de-inventarios/
   ```

---

## 🧩 Estructura del Proyecto

```
📁 gestion-de-inventarios/
│
├── index.php
├── productos.php
├── categorias.php
├── conexion.php
│
├── estilos/              # Archivos CSS
├── scripts/              # Archivos JavaScript
├── img/                  # Recursos gráficos
│
└── .github/workflows/    # Flujo de CI (php-ci.yml)
```

---

## 🔄 Workflow GitHub Actions

Este proyecto incluye un flujo de **Integración Continua (CI)** ubicado en  
`.github/workflows/php-ci.yml`, que:

- Configura PHP 8.2 en Ubuntu.  
- Instala dependencias y extensiones.  
- Valida la sintaxis de los archivos PHP.  
- Comprueba la conexión con MySQL.  
- Informa el estado de la compilación directamente en GitHub.

![CI Status](https://img.shields.io/github/actions/workflow/status/tuusuario/gestion-de-inventarios/php-ci.yml?branch=main)



## 📝 Licencia

Este proyecto se distribuye bajo la licencia **MIT**.  
Puedes usarlo y modificarlo libremente con fines educativos o de desarrollo.

---
