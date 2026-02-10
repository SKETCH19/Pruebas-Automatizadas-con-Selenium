#!/usr/bin/env python3
"""
Script de ejemplo para ejecutar pruebas automatizadas de forma completa
Demuestra el uso de todas las funcionalidades implementadas
"""

import sys
import os
from datetime import datetime

# Agregar el directorio padre al path para importar módulos
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

from pages.product_list_page import ProductListPage
from pages.add_product_page import AddProductPage
from pages.edit_product_page import EditProductPage
from utils.config import Config
from utils.helpers import (
    generate_test_product, 
    generate_product_code,
    wait_for_page_load,
    create_screenshots_directory,
    log_test_step,
    accept_alert
)


def configurar_driver():
    """Configurar el WebDriver con las opciones necesarias"""
    print("🔧 Configurando WebDriver...")
    
    # Deshabilitar proxy
    os.environ['NO_PROXY'] = '*'
    os.environ['HTTP_PROXY'] = ''
    os.environ['HTTPS_PROXY'] = ''
    
    # Opciones de Chrome
    chrome_options = Options()
    chrome_options.add_argument('--no-proxy-server')
    chrome_options.add_argument('--proxy-bypass-list=*')
    chrome_options.add_argument('--start-maximized')
    chrome_options.add_argument('--disable-extensions')
    
    # Crear servicio y driver
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=chrome_options)
    driver.implicitly_wait(Config.IMPLICIT_WAIT)
    
    print("✅ WebDriver configurado correctamente\n")
    return driver


def demostrar_crear_producto(driver, product_list):
    """Demostración de crear un producto"""
    print("=" * 70)
    print("DEMOSTRACIÓN 1: CREAR PRODUCTO")
    print("=" * 70)
    
    # Generar datos de prueba
    product_code = generate_product_code("DEMO")
    test_product = generate_test_product(product_code)
    
    log_test_step(1, "Navegando a agregar producto")
    product_list.click_add_product()
    
    log_test_step(2, "Llenando formulario con datos de prueba")
    add_page = AddProductPage(driver)
    add_page.fill_product_form(test_product)
    add_page.take_screenshot("demo_01_form_filled")
    
    log_test_step(3, "Enviando formulario")
    add_page.submit_form()
    wait_for_page_load(driver)
    
    print(f"✅ Producto '{test_product['nombre']}' creado exitosamente")
    print(f"   Código: {product_code}\n")
    
    return product_code, test_product


def demostrar_editar_producto(driver, product_list, product_code):
    """Demostración de editar un producto"""
    print("=" * 70)
    print("DEMOSTRACIÓN 2: EDITAR PRODUCTO")
    print("=" * 70)
    
    log_test_step(1, f"Buscando producto con código {product_code}")
    success = product_list.click_edit_product(product_code)
    
    if not success:
        print("❌ No se pudo encontrar el producto para editar")
        return False
    
    log_test_step(2, "Cargando página de edición")
    edit_page = EditProductPage(driver)
    
    if not edit_page.verify_page_loaded():
        print("❌ La página de edición no cargó correctamente")
        return False
    
    log_test_step(3, "Obteniendo valores actuales")
    current_values = edit_page.get_current_values()
    print(f"   Nombre actual: {current_values['nombre']}")
    print(f"   Precio actual: {current_values['precio']}")
    print(f"   Stock actual: {current_values['stock']}")
    
    log_test_step(4, "Actualizando campos")
    edit_page.update_field('nombre', 'PRODUCTO EDITADO - DEMO')
    edit_page.update_field('precio', '2999.99')
    edit_page.update_field('stock', '75')
    edit_page.take_screenshot("demo_02_edited")
    
    log_test_step(5, "Guardando cambios")
    edit_page.submit_form()
    wait_for_page_load(driver)
    
    print("✅ Producto editado exitosamente\n")
    return True


def demostrar_eliminar_producto(driver, product_list, product_code):
    """Demostración de eliminar un producto"""
    print("=" * 70)
    print("DEMOSTRACIÓN 3: ELIMINAR PRODUCTO")
    print("=" * 70)
    
    log_test_step(1, "Contando productos antes de eliminar")
    count_before = product_list.get_product_count()
    print(f"   Productos antes: {count_before}")
    
    log_test_step(2, f"Eliminando producto {product_code}")
    success = product_list.click_delete_product(product_code)
    
    if not success:
        print("❌ No se pudo eliminar el producto")
        return False
    
    # Aceptar la alerta de confirmación (ya se hace en click_delete_product)
    wait_for_page_load(driver)
    
    log_test_step(3, "Verificando eliminación")
    count_after = product_list.get_product_count()
    print(f"   Productos después: {count_after}")
    
    # Verificar que el producto ya no existe
    deleted_product = product_list.search_product_by_code(product_code)
    
    if deleted_product is None and count_after < count_before:
        print("✅ Producto eliminado exitosamente\n")
        return True
    else:
        print("❌ El producto no fue eliminado correctamente\n")
        return False


def demostrar_flujo_completo(driver):
    """Demostración del flujo completo CRUD"""
    print("\n" + "=" * 70)
    print("🚀 INICIANDO DEMOSTRACIÓN COMPLETA DE FUNCIONALIDADES")
    print("=" * 70 + "\n")
    
    # Crear directorio de screenshots
    create_screenshots_directory()
    
    # Navegar a la página de productos
    print(f"🌐 Navegando a {Config.BASE_URL}/listar.php")
    driver.get(f"{Config.BASE_URL}/listar.php")
    wait_for_page_load(driver)
    
    # Inicializar página
    product_list = ProductListPage(driver)
    product_list.take_screenshot("demo_00_initial")
    
    # Demostración 1: Crear
    product_code, test_product = demostrar_crear_producto(driver, product_list)
    
    # Demostración 2: Editar
    demostrar_editar_producto(driver, product_list, product_code)
    
    # Demostración 3: Eliminar
    demostrar_eliminar_producto(driver, product_list, product_code)
    
    print("=" * 70)
    print("✅ DEMOSTRACIÓN COMPLETADA EXITOSAMENTE")
    print("=" * 70)
    print(f"\n📸 Screenshots guardados en: screenshots/")
    print(f"⏰ Timestamp: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")


def main():
    """Función principal"""
    driver = None
    
    try:
        # Configurar driver
        driver = configurar_driver()
        
        # Ejecutar demostración
        demostrar_flujo_completo(driver)
        
        input("\n⏸️  Presiona Enter para cerrar el navegador...")
        
    except Exception as e:
        print(f"\n❌ Error durante la demostración: {e}")
        import traceback
        traceback.print_exc()
        
    finally:
        if driver:
            print("\n🔚 Cerrando navegador...")
            driver.quit()
            print("✅ Navegador cerrado\n")


if __name__ == "__main__":
    main()
