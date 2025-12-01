import pytest
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from pages.dashboard_page import DashboardPage
from pages.product_list_page import ProductListPage
from pages.add_product_page import AddProductPage
from utils.config import Config

class TestNavigation:
    @pytest.fixture(scope="function")
    def setup(self):
        # DESHABILITAR PROXY
        os.environ['NO_PROXY'] = '*'
        os.environ['HTTP_PROXY'] = ''
        os.environ['HTTPS_PROXY'] = ''
        
        # Configurar Chrome options
        chrome_options = Options()
        chrome_options.add_argument('--proxy-server=direct://')
        chrome_options.add_argument('--proxy-bypass-list=*')
        chrome_options.add_argument('--start-maximized')
        
        self.driver = webdriver.Chrome(
            executable_path=ChromeDriverManager().install(),
            options=chrome_options
        )
        self.driver.implicitly_wait(Config.IMPLICIT_WAIT)
        self.driver.get(Config.BASE_URL)
        yield
        self.driver.quit()
    
    def test_navegacion_desde_dashboard(self, setup):
        """HU03: Navegación entre páginas desde el dashboard"""
        dashboard = DashboardPage(self.driver)
        dashboard.take_screenshot("dashboard_inicio")
        
        # Navegar a gestión de productos
        dashboard.navigate_to_product_management()
        product_list = ProductListPage(self.driver)
        assert "listar.php" in self.driver.current_url
        product_list.take_screenshot("lista_productos")
        
        # Volver al dashboard
        product_list.click((By.LINK_TEXT, "← Volver al Inicio"))
        dashboard = DashboardPage(self.driver)
        assert "index.php" in self.driver.current_url
    
    def test_acceso_agregar_producto_multiples_rutas(self, setup):
        """HU02: Acceso a agregar producto desde múltiples rutas"""
        dashboard = DashboardPage(self.driver)
        
        # Ruta 1: Desde el dashboard principal
        dashboard.click_add_product()
        add_product_page = AddProductPage(self.driver)
        assert "agregar.php" in self.driver.current_url
        add_product_page.take_screenshot("agregar_desde_dashboard")
        
        # Volver y probar otra ruta
        self.driver.back()
        dashboard.navigate_to_product_management()
        
        # Ruta 2: Desde la lista de productos
        product_list = ProductListPage(self.driver)
        product_list.click_add_product()
        assert "agregar.php" in self.driver.current_url
        add_product_page.take_screenshot("agregar_desde_lista")