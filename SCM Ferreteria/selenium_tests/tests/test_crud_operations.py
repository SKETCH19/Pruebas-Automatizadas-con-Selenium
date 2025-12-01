import pytest
import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from pages.product_list_page import ProductListPage
from pages.add_product_page import AddProductPage
from utils.config import Config

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