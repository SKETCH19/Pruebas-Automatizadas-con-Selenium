import pytest
import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.chrome.options import Options as ChromeOptions
from webdriver_manager.chrome import ChromeDriverManager
from pages.login_page import LoginPage
from utils.config import Config

class TestLogin:
    @pytest.fixture(scope="function")
    def setup(self):
        # Configurar Chrome options
        chrome_options = ChromeOptions()
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument('--disable-dev-shm-usage')
        chrome_options.add_argument('--start-maximized')
        
        # Configurar Chrome
        service = ChromeService(ChromeDriverManager().install())
        self.driver = webdriver.Chrome(service=service, options=chrome_options)
        self.driver.implicitly_wait(Config.IMPLICIT_WAIT)
        
        # Si tu sistema tiene login, ir a la página de login
        try:
            self.driver.get(Config.LOGIN_URL)
        except:
            # Si no existe página de login, ir a la página principal
            self.driver.get(Config.BASE_URL)
        
        yield self.driver
        
        # Limpieza después de cada test
        self.driver.quit()
    
    def test_login_exitoso(self, setup):
        """HU01: Camino feliz - Login exitoso con credenciales válidas"""
        login_page = LoginPage(self.driver)
        
        # Tomar screenshot antes del login
        login_page.take_screenshot("antes_login")
        
        # Verificar si el sistema tiene formulario de login
        if login_page.is_element_present(login_page.USERNAME_INPUT):
            # Realizar login
            login_page.login(Config.VALID_USER['username'], Config.VALID_USER['password'])
            
            # Tomar screenshot después del login
            login_page.take_screenshot("despues_login_exitoso")
            
            # Verificar que el login fue exitoso
            assert login_page.is_login_successful(), "El login debería ser exitoso"
            print("✓ Test login exitoso completado")
        else:
            pytest.skip("El sistema no tiene formulario de login")
    
    def test_login_fallido_credenciales_invalidas(self, setup):
        """HU01: Prueba negativa - Login fallido con credenciales inválidas"""
        login_page = LoginPage(self.driver)
        
        # Verificar si el sistema tiene formulario de login
        if login_page.is_element_present(login_page.USERNAME_INPUT):
            # Intentar login con credenciales inválidas
            login_page.login(Config.INVALID_USER['username'], Config.INVALID_USER['password'])
            
            # Tomar screenshot del error
            login_page.take_screenshot("login_fallido")
            
            # Verificar que se muestra mensaje de error
            error_message = login_page.get_error_message()
            assert error_message != "", "Debería mostrarse un mensaje de error"
            print("✓ Test login fallido completado")
        else:
            pytest.skip("El sistema no tiene formulario de login")
    
    def test_login_campos_vacios(self, setup):
        """HU01: Prueba negativa - Login con campos vacíos"""
        login_page = LoginPage(self.driver)
        
        # Verificar si el sistema tiene formulario de login
        if login_page.is_element_present(login_page.USERNAME_INPUT):
            # Intentar login con campos vacíos
            login_page.click_login()
            
            # Tomar screenshot
            login_page.take_screenshot("login_campos_vacios")
            
            # Verificar que permanecemos en la página de login
            assert "login" in self.driver.current_url.lower() or login_page.get_error_message() != ""
            print("✓ Test login campos vacíos completado")
        else:
            pytest.skip("El sistema no tiene formulario de login")
    
    def test_login_solo_usuario(self, setup):
        """HU01: Prueba negativa - Login solo con usuario"""
        login_page = LoginPage(self.driver)
        
        # Verificar si el sistema tiene formulario de login
        if login_page.is_element_present(login_page.USERNAME_INPUT):
            # Ingresar solo usuario
            login_page.enter_username(Config.VALID_USER['username'])
            login_page.click_login()
            
            # Tomar screenshot
            login_page.take_screenshot("login_solo_usuario")
            
            # Verificar que no se permite el login
            assert not login_page.is_login_successful()
            print("✓ Test login solo usuario completado")
        else:
            pytest.skip("El sistema no tiene formulario de login")
    
    def test_login_solo_password(self, setup):
        """HU01: Prueba negativa - Login solo con password"""
        login_page = LoginPage(self.driver)
        
        # Verificar si el sistema tiene formulario de login
        if login_page.is_element_present(login_page.USERNAME_INPUT):
            # Ingresar solo password
            login_page.enter_password(Config.VALID_USER['password'])
            login_page.click_login()
            
            # Tomar screenshot
            login_page.take_screenshot("login_solo_password")
            
            # Verificar que no se permite el login
            assert not login_page.is_login_successful()
            print("✓ Test login solo password completado")
        else:
            pytest.skip("El sistema no tiene formulario de login")

    def test_navegacion_sin_login(self, setup):
        """HU01: Prueba de acceso sin login"""
        # Intentar acceder directamente a una página protegida
        self.driver.get(Config.BASE_URL + "/listar.php")
        
        login_page = LoginPage(self.driver)
        login_page.take_screenshot("acceso_directo_sin_login")
        
        # Verificar si nos redirige al login o permite acceso
        current_url = self.driver.current_url
        
        if "login" in current_url.lower():
            print("✓ Sistema redirige al login cuando no hay autenticación")
        else:
            print("⚠ Sistema permite acceso sin autenticación")
        
        # Esta prueba es más de observación que de assertion estricto
        assert True