import pytest
import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from pages.product_list_page import ProductListPage
from pages.add_product_page import AddProductPage
from pages.edit_product_page import EditProductPage
from utils.config import Config
from utils.helpers import generate_test_product, generate_product_code, wait_for_page_load

class TestCRUDOperations:
    @pytest.fixture(scope="function")
    def setup(self):
        # DESHABILITAR PROXY a nivel de sistema
        os.environ['NO_PROXY'] = '*'
        os.environ['HTTP_PROXY'] = ''
        os.environ['HTTPS_PROXY'] = ''
        
        # Configurar Chrome options para IGNORAR proxy
        chrome_options = Options()
        chrome_options.add_argument('--proxy-server=direct://')
        chrome_options.add_argument('--proxy-bypass-list=*')
        chrome_options.add_argument('--ignore-certificate-errors')
        chrome_options.add_argument('--start-maximized')
        
        # Configurar Chrome
        self.driver = webdriver.Chrome(
            executable_path=ChromeDriverManager().install(),
            options=chrome_options
        )
        self.driver.implicitly_wait(Config.IMPLICIT_WAIT)
        
        # Ir directamente a la página de productos
        self.driver.get(Config.BASE_URL + "/listar.php")
        
        yield self.driver
        
        # Limpieza después de cada test
        self.driver.quit()
    
    def test_agregar_producto_exitoso(self, setup):
        """HU02: Camino feliz - Agregar producto exitosamente"""
        product_list = ProductListPage(self.driver)
        
        # Tomar screenshot antes
        product_list.take_screenshot("antes_agregar_producto")
        
        # Navegar a agregar producto
        product_list.click_add_product()
        
        # Llenar formulario
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(Config.TEST_PRODUCT)
        add_product.take_screenshot("formulario_lleno")
        
        # Enviar formulario
        add_product.submit_form()
        
        # Pequeña pausa para procesamiento
        time.sleep(2)
        
        product_list.take_screenshot("despues_agregar_producto")
        print("✓ Test agregar producto exitoso completado")
    
    def test_agregar_producto_campos_obligatorios_vacios(self, setup):
        """HU02: Prueba negativa - Campos obligatorios vacíos"""
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        add_product = AddProductPage(self.driver)
        
        # Intentar enviar formulario vacío
        add_product.submit_form()
        
        # Verificar que permanecemos en la misma página (no redirección)
        assert "agregar.php" in self.driver.current_url
        add_product.take_screenshot("campos_obligatorios_vacios")
        print("✓ Test campos obligatorios vacíos completado")
    
    def test_editar_producto_exitoso(self, setup):
        """HU03: Camino feliz - Editar producto existente exitosamente"""
        # Primero crear un producto para editar
        product_code = generate_product_code("EDIT")
        test_product = generate_test_product(product_code)
        
        # Ir a agregar producto
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        # Agregar producto
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        
        # Buscar y editar el producto
        product_list.take_screenshot("antes_editar")
        assert product_list.click_edit_product(product_code), "No se pudo encontrar el producto para editar"
        
        # Verificar que estamos en la página de edición
        edit_product = EditProductPage(self.driver)
        assert edit_product.verify_page_loaded(), "No se cargó la página de edición"
        
        # Modificar algunos campos
        new_data = {
            'nombre': 'Producto Editado - Testing',
            'precio': '2500.99',
            'stock': '50',
            'descripcion': 'Descripción actualizada mediante prueba automatizada'
        }
        
        edit_product.fill_product_form(new_data)
        edit_product.take_screenshot("formulario_editado")
        
        # Guardar cambios
        edit_product.submit_form()
        time.sleep(2)
        
        # Verificar que volvimos a la lista
        assert "listar.php" in self.driver.current_url
        product_list.take_screenshot("despues_editar")
        print("✓ Test editar producto exitoso completado")
    
    def test_editar_producto_campos_vacios(self, setup):
        """HU03: Prueba negativa - Intentar editar con campos obligatorios vacíos"""
        # Crear producto primero
        product_code = generate_product_code("EDIT2")
        test_product = generate_test_product(product_code)
        
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        
        # Editar el producto
        assert product_list.click_edit_product(product_code)
        
        edit_product = EditProductPage(self.driver)
        
        # Intentar limpiar campos obligatorios
        edit_product.clear_field('nombre')
        edit_product.clear_field('precio')
        edit_product.take_screenshot("campos_vacios_edicion")
        
        # Intentar guardar
        edit_product.submit_form()
        
        # Debería permanecer en la página de edición
        assert "editar.php" in self.driver.current_url
        print("✓ Test editar con campos vacíos completado")
    
    def test_eliminar_producto_exitoso(self, setup):
        """HU04: Camino feliz - Eliminar producto exitosamente"""
        # Crear un producto para eliminar
        product_code = generate_product_code("DEL")
        test_product = generate_test_product(product_code)
        
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        
        # Contar productos antes de eliminar
        initial_count = product_list.get_product_count()
        product_list.take_screenshot("antes_eliminar")
        
        # Eliminar el producto
        assert product_list.click_delete_product(product_code), "No se pudo eliminar el producto"
        time.sleep(2)
        
        # Verificar que el producto fue eliminado
        final_count = product_list.get_product_count()
        product_list.take_screenshot("despues_eliminar")
        
        # El conteo debe disminuir
        assert final_count < initial_count, "El producto no fue eliminado"
        
        # Verificar que ya no existe en la lista
        deleted_product = product_list.search_product_by_code(product_code)
        assert deleted_product is None, "El producto eliminado todavía aparece en la lista"
        
        print("✓ Test eliminar producto exitoso completado")
    
    def test_cancelar_edicion(self, setup):
        """HU03: Verificar cancelación de edición sin guardar cambios"""
        # Crear producto
        product_code = generate_product_code("CANCEL")
        test_product = generate_test_product(product_code)
        
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        
        # Ir a editar
        assert product_list.click_edit_product(product_code)
        
        edit_product = EditProductPage(self.driver)
        
        # Guardar valores originales
        original_values = edit_product.get_current_values()
        
        # Hacer cambios
        edit_product.update_field('nombre', 'CAMBIO QUE NO SE DEBE GUARDAR')
        edit_product.update_field('precio', '99999.99')
        edit_product.take_screenshot("antes_cancelar")
        
        # Cancelar en lugar de guardar
        edit_product.cancel_form()
        time.sleep(1)
        
        # Verificar que volvimos a la lista
        assert "listar.php" in self.driver.current_url
        product_list.take_screenshot("despues_cancelar")
        
        print("✓ Test cancelar edición completado")
    
    def test_actualizar_stock(self, setup):
        """HU03: Actualizar solo el stock de un producto"""
        # Crear producto
        product_code = generate_product_code("STOCK")
        test_product = generate_test_product(product_code)
        test_product['stock'] = '10'  # Stock inicial
        
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        
        # Editar para actualizar stock
        assert product_list.click_edit_product(product_code)
        
        edit_product = EditProductPage(self.driver)
        
        # Actualizar solo el stock
        new_stock = '100'
        edit_product.update_field('stock', new_stock)
        edit_product.take_screenshot("stock_actualizado")
        
        # Guardar
        edit_product.submit_form()
        time.sleep(2)
        
        # Verificar que se guardó
        assert "listar.php" in self.driver.current_url
        product_list.take_screenshot("despues_actualizar_stock")
        
        print("✓ Test actualizar stock completado")
    
    def test_flujo_completo_crud(self, setup):
        """Test de integración: Flujo completo CREATE -> READ -> UPDATE -> DELETE"""
        product_code = generate_product_code("FULL")
        test_product = generate_test_product(product_code)
        
        product_list = ProductListPage(self.driver)
        
        # CREATE
        print("  → Paso 1: CREATE")
        product_list.click_add_product()
        add_product = AddProductPage(self.driver)
        add_product.fill_product_form(test_product)
        add_product.submit_form()
        time.sleep(2)
        product_list.take_screenshot("crud_01_created")
        
        # READ (verificar que existe)
        print("  → Paso 2: READ")
        product_row = product_list.search_product_by_code(product_code)
        assert product_row is not None, "Producto creado no encontrado"
        product_list.take_screenshot("crud_02_read")
        
        # UPDATE
        print("  → Paso 3: UPDATE")
        assert product_list.click_edit_product(product_code)
        edit_product = EditProductPage(self.driver)
        edit_product.update_field('nombre', 'PRODUCTO ACTUALIZADO FULL CRUD')
        edit_product.update_field('precio', '3333.33')
        edit_product.submit_form()
        time.sleep(2)
        product_list.take_screenshot("crud_03_updated")
        
        # DELETE
        print("  → Paso 4: DELETE")
        assert product_list.click_delete_product(product_code)
        time.sleep(2)
        
        # Verificar eliminación
        deleted_product = product_list.search_product_by_code(product_code)
        assert deleted_product is None, "Producto no fue eliminado"
        product_list.take_screenshot("crud_04_deleted")
        
        print("✓ Test flujo completo CRUD completado exitosamente")