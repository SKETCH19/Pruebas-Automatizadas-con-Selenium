# 🧪 Pruebas Automatizadas con Selenium - SCM Ferretería

> Sistema completo de pruebas automatizadas para el módulo de gestión de productos de SCM Ferretería usando Selenium WebDriver, Python y Pytest.

[![Python](https://img.shields.io/badge/Python-3.8+-blue.svg)](https://www.python.org/)
[![Selenium](https://img.shields.io/badge/Selenium-4.15-green.svg)](https://www.selenium.dev/)
[![Pytest](https://img.shields.io/badge/Pytest-7.4-red.svg)](https://pytest.org/)

---

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características](#-características)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Tests Disponibles](#-tests-disponibles)
- [Page Objects](#-page-objects)
- [Utilidades](#-utilidades)
- [Reportes](#-reportes)
- [Contribuir](#-contribuir)

---

## 🎯 Descripción

Este proyecto implementa un conjunto completo de pruebas automatizadas para el sistema de gestión de productos de una ferretería. Utilizando el patrón **Page Object Model (POM)**, proporciona pruebas robustas y mantenibles para todas las operaciones CRUD (Create, Read, Update, Delete).

### Funcionalidades Probadas:
- ✅ Agregar nuevos productos
- ✅ Listar productos existentes
- ✅ Editar información de productos
- ✅ Eliminar productos
- ✅ Validación de campos obligatorios
- ✅ Manejo de errores
- ✅ Flujos completos de integración

---

## ✨ Características

- 🏗️ **Arquitectura Page Object Model (POM)**: Separación clara entre lógica de test y elementos de página
- 🔄 **Tests Reutilizables**: Helpers y utilidades para generación de datos de prueba
- 📸 **Screenshots Automáticos**: Captura de pantalla en puntos clave y errores
- 📊 **Reportes HTML**: Generación de reportes detallados con pytest-html
- 🔍 **Validaciones Completas**: Tests positivos y negativos
- 🛡️ **Manejo Robusto de Errores**: Try-catch y reintentos automáticos
- 📝 **Código Documentado**: Docstrings y comentarios explicativos

---

## 📁 Estructura del Proyecto

```
SCM Ferreteria/
├── productos/                      # Aplicación PHP
│   ├── index.php                  # Dashboard
│   ├── listar.php                 # Lista de productos
│   ├── agregar.php                # Agregar producto
│   ├── editar.php                 # Editar producto
│   └── eliminar.php               # Eliminar producto
│
├── selenium_tests/                 # Tests automatizados
│   ├── pages/                     # Page Objects
│   │   ├── base_page.py          # Clase base
│   │   ├── login_page.py         # Login (futuro)
│   │   ├── dashboard_page.py     # Dashboard
│   │   ├── product_list_page.py  # Lista de productos
│   │   ├── add_product_page.py   # Agregar producto ✅
│   │   └── edit_product_page.py  # Editar producto ✅ NUEVO
│   │
│   ├── tests/                     # Test suites
│   │   ├── test_crud_operations.py   # Tests CRUD completos ✅
│   │   ├── test_navigation.py        # Tests de navegación
│   │   ├── test_login.py             # Tests de login
│   │   └── test_pruebas_simples.py   # Tests básicos
│   │
│   ├── utils/                     # Utilidades
│   │   ├── config.py             # Configuraciones
│   │   └── helpers.py            # Funciones auxiliares ✅ NUEVO
│   │
│   ├── screenshots/               # Capturas de pantalla
│   ├── reports/                   # Reportes HTML
│   ├── requirements.txt           # Dependencias Python ✅
│   ├── demo_funcionalidades.py   # Script demostración ✅ NUEVO
│   └── FUNCIONALIDADES_COMPLETADAS.md  # Documentación ✅
│
├── database/
│   └── database.php              # Conexión BD
├── css/
│   └── style.css                 # Estilos
└── README.md                      # Este archivo
```

---

## 🚀 Instalación

### Prerrequisitos

- Python 3.8 o superior
- Servidor web local (XAMPP, Laragon, WAMP, etc.)
- Google Chrome instalado
- Base de datos MySQL configurada

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/SKETCH19/Pruebas-Automatizadas-con-Selenium.git
cd Pruebas-Automatizadas-con-Selenium
```

### Paso 2: Configurar el Servidor Web

1. Copiar el directorio `SCM Ferreteria` a tu directorio web local
   - **Laragon**: `C:/laragon/www/`
   - **XAMPP**: `C:/xampp/htdocs/`

2. Configurar la base de datos:
   ```bash
   # Importar el archivo SQL
   mysql -u root -p < "SCM Ferreteria/sql/setup.sql"
   ```

3. Verificar que la aplicación esté accesible:
   ```
   http://localhost/SCM Ferreteria/productos/
   ```

### Paso 3: Instalar Dependencias Python

```bash
# Crear entorno virtual (recomendado)
python -m venv venv

# Activar entorno virtual
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# Instalar dependencias
cd "SCM Ferreteria/selenium_tests"
pip install -r requirements.txt
```

### Paso 4: Configurar Variables

Editar `utils/config.py` si es necesario:

```python
class Config:
    BASE_URL = "http://localhost/SCM Ferreteria/productos"
    BROWSER = "chrome"
    IMPLICIT_WAIT = 10
    EXPLICIT_WAIT = 20
```

---

## 💻 Uso

### Ejecutar Todos los Tests

```bash
cd "SCM Ferreteria/selenium_tests"

# Ejecutar todos los tests con reporte HTML
pytest tests/ -v --html=reports/test_report.html --self-contained-html

# Ejecutar tests específicos
pytest tests/test_crud_operations.py -v
```

### Ejecutar Tests Individuales

```bash
# Test de agregar producto
pytest tests/test_crud_operations.py::TestCRUDOperations::test_agregar_producto_exitoso -v

# Test de editar producto
pytest tests/test_crud_operations.py::TestCRUDOperations::test_editar_producto_exitoso -v

# Test de eliminar producto
pytest tests/test_crud_operations.py::TestCRUDOperations::test_eliminar_producto_exitoso -v

# Test de flujo completo
pytest tests/test_crud_operations.py::TestCRUDOperations::test_flujo_completo_crud -v
```

### Ejecutar Demostración Interactiva

```bash
# Script que muestra todas las funcionalidades
python demo_funcionalidades.py
```

Este script ejecutará automáticamente:
1. Creación de un producto
2. Edición del producto creado
3. Eliminación del producto

---

## 🧪 Tests Disponibles

### test_crud_operations.py

| Test | Descripción | Tipo |
|------|-------------|------|
| `test_agregar_producto_exitoso` | Agregar producto con datos válidos | ✅ Positivo |
| `test_agregar_producto_campos_obligatorios_vacios` | Validar campos requeridos | ❌ Negativo |
| `test_editar_producto_exitoso` | Editar producto existente | ✅ Positivo |
| `test_editar_producto_campos_vacios` | Evitar edición con campos vacíos | ❌ Negativo |
| `test_eliminar_producto_exitoso` | Eliminar producto | ✅ Positivo |
| `test_cancelar_edicion` | Cancelar edición sin guardar | ✅ Positivo |
| `test_actualizar_stock` | Actualizar solo stock | ✅ Positivo |
| `test_flujo_completo_crud` | CREATE → READ → UPDATE → DELETE | ✅ Integración |

### test_pruebas_simples.py

Tests básicos de carga y visualización de páginas.

### test_navigation.py

Tests de navegación entre diferentes secciones.

---

## 📖 Page Objects

### BasePage

Clase base con métodos comunes:
- `find_element(locator)` - Encontrar elemento con espera explícita
- `click(locator)` - Click en elemento
- `type(locator, text)` - Escribir texto
- `get_text(locator)` - Obtener texto
- `take_screenshot(name)` - Capturar pantalla
- `is_element_present(locator)` - Verificar presencia

### ProductListPage

Métodos para interactuar con la lista de productos:
- `click_add_product()` - Ir a agregar producto
- `get_product_count()` - Contar productos
- `search_product_by_code(code)` - Buscar producto
- `click_edit_product(code)` - Editar producto
- `click_delete_product(code)` - Eliminar producto

### AddProductPage

Métodos para agregar productos:
- `fill_product_form(data)` - Llenar formulario completo
- `submit_form()` - Enviar formulario
- `cancel_form()` - Cancelar
- `is_error_displayed()` - Verificar errores

### EditProductPage ✨ NUEVO

Métodos para editar productos:
- `verify_page_loaded()` - Verificar carga
- `get_current_values()` - Obtener valores actuales
- `fill_product_form(data)` - Llenar formulario
- `update_field(field, value)` - Actualizar campo específico
- `clear_field(field)` - Limpiar campo
- `submit_form()` - Guardar cambios
- `cancel_form()` - Cancelar edición
- `validate_required_fields()` - Validar campos

---

## 🛠️ Utilidades

### helpers.py ✨ NUEVO

Módulo completo de utilidades:

#### Generación de Datos
```python
from utils.helpers import generate_test_product, generate_product_code

# Generar producto de prueba
producto = generate_test_product()

# Generar código único
codigo = generate_product_code("SPECIAL")
```

#### Manejo de Alertas
```python
from utils.helpers import accept_alert, wait_for_alert

# Aceptar alerta
accept_alert(driver)

# Obtener texto de alerta
texto = get_alert_text(driver)
```

#### Screenshots
```python
from utils.helpers import take_screenshot_with_timestamp

# Captura con timestamp
ruta = take_screenshot_with_timestamp(driver, "test_paso_1")
```

#### Validación
```python
from utils.helpers import validate_product_data, compare_products

# Validar datos
valido, errores = validate_product_data(producto)

# Comparar productos
iguales, diferencias = compare_products(prod1, prod2)
```

---

## 📊 Reportes

Los reportes se generan automáticamente en formato HTML:

```bash
# Generar reporte
pytest tests/test_crud_operations.py --html=reports/mi_reporte.html --self-contained-html

# Abrir reporte
# Windows:
start reports/mi_reporte.html
# Linux:
xdg-open reports/mi_reporte.html
# Mac:
open reports/mi_reporte.html
```

Los reportes incluyen:
- ✅ Tests exitosos
- ❌ Tests fallidos
- ⏱️ Tiempo de ejecución
- 📸 Screenshots (si se configuran)
- 📝 Logs detallados

---

## 🐛 Debugging

### Ver Logs Detallados

```bash
pytest tests/test_crud_operations.py -v -s
```

### Resaltar Elementos (Debug Visual)

```python
from utils.helpers import highlight_element

element = driver.find_element(By.ID, "codigo")
highlight_element(driver, element, duration=3)
```

### Pausar Ejecución

```python
import time
time.sleep(5)  # Pausar 5 segundos para inspección manual
```

---

## 📝 Mejores Prácticas

1. **Siempre usar Page Objects**: No acceder a elementos directamente en tests
2. **Generar datos únicos**: Usar helpers para evitar conflictos
3. **Capturar screenshots**: En pasos importantes y errores
4. **Validar resultados**: Siempre verificar que las acciones tuvieron efecto
5. **Limpiar datos**: Eliminar datos de prueba después de cada test
6. **Nombres descriptivos**: Tests y variables auto-explicativos

---

## 🤝 Contribuir

Las contribuciones son bienvenidas:

1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/NuevaFuncionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/NuevaFuncionalidad`)
5. Abrir Pull Request

---

## 📄 Documentación Adicional

- [FUNCIONALIDADES_COMPLETADAS.md](SCM%20Ferreteria/selenium_tests/FUNCIONALIDADES_COMPLETADAS.md) - Detalle de funcionalidades implementadas
- [Selenium Documentation](https://www.selenium.dev/documentation/)
- [Pytest Documentation](https://docs.pytest.org/)

---

## 👥 Autor

**SKETCH19**

---

## 📜 Licencia

Este proyecto es de código abierto y está disponible para uso educativo.

---

## 🎉 Estado del Proyecto

✅ **COMPLETADO** - Todas las funcionalidades CRUD implementadas y probadas

**Última actualización:** Febrero 2026

---

## 🔗 Enlaces Útiles

- [Repositorio](https://github.com/SKETCH19/Pruebas-Automatizadas-con-Selenium)
- [Issues](https://github.com/SKETCH19/Pruebas-Automatizadas-con-Selenium/issues)
- [Documentación Selenium](https://www.selenium.dev/)

---

**¡Gracias por usar este proyecto! 🚀**
