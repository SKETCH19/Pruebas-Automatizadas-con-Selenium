from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from .base_page import BasePage

class AddProductPage(BasePage):
    # Locators
    CODIGO_INPUT = (By.ID, "codigo")
    NOMBRE_INPUT = (By.ID, "nombre")
    DESCRIPCION_INPUT = (By.ID, "descripcion")
    PRECIO_INPUT = (By.ID, "precio")
    STOCK_INPUT = (By.ID, "stock")
    CATEGORIA_SELECT = (By.ID, "categoria")
    PROVEEDOR_INPUT = (By.ID, "proveedor")
    UBICACION_INPUT = (By.ID, "ubicacion")
    GUARDAR_BUTTON = (By.CSS_SELECTOR, "button[type='submit']")
    CANCELAR_BUTTON = (By.LINK_TEXT, "❌ Cancelar")
    ERROR_ALERT = (By.CLASS_NAME, "alert error")
    
    def fill_product_form(self, product_data):
        """Llenar el formulario con datos del producto
        
        Args:
            product_data (dict): Diccionario con los campos del producto
        """
        self.type(self.CODIGO_INPUT, product_data['codigo'])
        self.type(self.NOMBRE_INPUT, product_data['nombre'])
        self.type(self.DESCRIPCION_INPUT, product_data['descripcion'])
        self.type(self.PRECIO_INPUT, str(product_data['precio']))
        self.type(self.STOCK_INPUT, str(product_data['stock']))
        
        # Seleccionar categoría usando Select
        categoria_select = Select(self.find_element(self.CATEGORIA_SELECT))
        categoria_select.select_by_visible_text(product_data['categoria'])
        
        self.type(self.PROVEEDOR_INPUT, product_data['proveedor'])
        self.type(self.UBICACION_INPUT, product_data['ubicacion'])
    
    def submit_form(self):
        """Enviar el formulario"""
        self.click(self.GUARDAR_BUTTON)
    
    def cancel_form(self):
        """Cancelar el formulario"""
        self.click(self.CANCELAR_BUTTON)
    
    def is_error_displayed(self):
        """Verificar si se muestra un mensaje de error"""
        return self.is_element_present(self.ERROR_ALERT)
    
    def get_error_message(self):
        """Obtener el mensaje de error si existe"""
        if self.is_error_displayed():
            return self.get_text(self.ERROR_ALERT)
        return ""