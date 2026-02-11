# 🚀 Guía de Despliegue en GitHub Pages

Esta guía te ayudará a publicar tu sitio portfolio en GitHub Pages paso a paso.

---

## 📋 Requisitos Previos

- Tener una cuenta de GitHub
- Tener el repositorio `Pruebas-Automatizadas-con-Selenium` en tu cuenta de GitHub
- Los archivos del proyecto deben estar en el branch `main`

---

## ✅ Pasos para Activar GitHub Pages

### 1️⃣ Asegúrate de que los Cambios estén en GitHub

Primero, necesitas subir los archivos de la carpeta `docs/` a tu repositorio de GitHub:

```bash
# En la terminal de VS Code o Git Bash, ejecuta:
cd /workspaces/Pruebas-Automatizadas-con-Selenium

# Agrega todos los archivos nuevos
git add docs/

# Haz commit de los cambios
git commit -m "Add GitHub Pages portfolio site"

# Sube los cambios a GitHub
git push origin main
```

### 2️⃣ Configurar GitHub Pages en tu Repositorio

1. **Ve a tu repositorio en GitHub:**
   - Abre tu navegador
   - Ve a: `https://github.com/SKETCH19/Pruebas-Automatizadas-con-Selenium`

2. **Accede a la Configuración:**
   - Click en la pestaña **"Settings"** (Configuración)
   - En el menú lateral izquierdo, busca la sección **"Code and automation"**
   - Click en **"Pages"**

3. **Configura la Fuente de GitHub Pages:**
   - En **"Source"** (Fuente), selecciona: **Deploy from a branch**
   - En **"Branch"** (Rama):
     - Selecciona: `main`
     - Selecciona la carpeta: `/docs`
     - Click en **"Save"** (Guardar)

4. **Espera el Despliegue:**
   - GitHub comenzará a construir tu sitio automáticamente
   - Esto puede tomar 1-3 minutos
   - Verás un mensaje cuando esté listo

### 3️⃣ Accede a tu Sitio Publicado

Una vez que GitHub Pages termine de construir tu sitio, estará disponible en:

```
https://sketch19.github.io/Pruebas-Automatizadas-con-Selenium/
```

Puedes encontrar esta URL en la sección de GitHub Pages en Settings → Pages.

---

## 🌟 Características del Sitio Portfolio

Tu sitio incluye:

✅ **Página Principal** (`index.html`)
   - Descripción completa del proyecto
   - Stack tecnológico con badges
   - Características principales
   - Suite de pruebas automatizadas
   - Estadísticas del proyecto
   - Enlaces al repositorio

✅ **Página de Reportes** (`reports/index.html`)
   - Showcase de todos los reportes de testing
   - Categorización por tipo de test
   - Enlaces directos a cada reporte HTML
   - Fechas y descripciones

✅ **Reportes de Pruebas**
   - Todos tus reportes HTML de Selenium/Pytest
   - Accesibles directamente desde el sitio web
   - Organizados por categorías

---

## 🔧 Personalización

### Cambiar el Nombre del Propietario

Si quieres cambiar "SKETCH19" por tu nombre real o username diferente:

1. Edita `/docs/index.html`
2. Busca todas las referencias a "SKETCH19"
3. Reemplázalas con tu nombre o username preferido
4. Haz commit y push de los cambios

### Agregar tu Dominio Personalizado (Opcional)

Si tienes un dominio propio (ej: `www.tudominio.com`):

1. En GitHub Settings → Pages
2. En la sección **"Custom domain"**
3. Ingresa tu dominio
4. Configura los DNS de tu dominio según las [instrucciones de GitHub](https://docs.github.com/en/pages/configuring-a-custom-domain-for-your-github-pages-site)

---

## 🔄 Actualizar el Sitio

Cada vez que hagas cambios en la carpeta `docs/`:

```bash
git add docs/
git commit -m "Update portfolio site"
git push origin main
```

GitHub Pages se actualizará automáticamente en 1-3 minutos.

---

## 📱 Vista Previa Local

Para ver cómo se ve tu sitio antes de subirlo a GitHub:

```bash
# Opción 1: Con Python
cd docs
python3 -m http.server 8000
# Abre: http://localhost:8000

# Opción 2: Con PHP
cd docs
php -S localhost:8000
# Abre: http://localhost:8000

# Opción 3: Con la extensión Live Server de VS Code
# Click derecho en index.html → "Open with Live Server"
```

---

## ❓ Solución de Problemas

### El sitio no carga (Error 404)

1. Verifica que la carpeta `docs/` esté en el branch `main`
2. Confirma que la configuración en Settings → Pages apunte a `/docs`
3. Espera 3-5 minutos para que GitHub Pages termine de construir
4. Refresca tu navegador con Ctrl+F5 (limpia caché)

### Los estilos no se cargan

1. Verifica que `docs/assets/style.css` exista
2. Revisa la consola del navegador (F12) para ver errores
3. Asegúrate de que las rutas en `index.html` sean relativas

### Los reportes no se muestran

1. Verifica que los archivos HTML estén en `docs/reports/`
2. Confirma que se hayan subido a GitHub con `git push`
3. Revisa que los enlaces en `reports/index.html` coincidan con los nombres de archivo

---

## 📚 Recursos Adicionales

- [Documentación oficial de GitHub Pages](https://docs.github.com/en/pages)
- [Personalizar Jekyll Themes](https://docs.github.com/en/pages/setting-up-a-github-pages-site-with-jekyll/about-jekyll-themes)
- [Configurar dominio personalizado](https://docs.github.com/en/pages/configuring-a-custom-domain-for-your-github-pages-site)

---

## ✨ ¡Listo!

Tu portfolio profesional ahora está disponible en línea para compartir en:
- LinkedIn
- CV/Resume
- Aplicaciones de trabajo
- Portfolio personal

**URL para compartir:**
```
https://sketch19.github.io/Pruebas-Automatizadas-con-Selenium/
```

---

**¿Necesitas ayuda?** Abre un issue en el repositorio de GitHub.
