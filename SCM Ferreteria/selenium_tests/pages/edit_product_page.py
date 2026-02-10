from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from .base_page import BasePage
import time

class EditProductPage(BasePage):
    """Page Object para la página de editar producto"""
    
    # Locators - mismos que AddProductPage ya que usan el mismo formulario
    CODIGO_INPUT = (By.ID, "codigo")
    NOMBRE_INPUT = (By.ID, "nombre")
    DESCRIPCION_INPUT = (By.ID, "descripcion")
    PRECIO_INPUT = (By.ID, "precio")
    STOCK_INPUT = (By.ID, "stock")
    CATEGORIA_SELECT = (By.ID, "categoria")
    PROVEEDOR_INPUT = (By.ID, "proveedor")
    UBICACION_INPUT = (By.ID, "ubicacion")
    ACTUALIZAR_BUTTON = (By.CSS_SELECTOR, "button[type='submit']")
    CANCELAR_BUTTON = (By.LINK_TEXT, "❌ Cancelar")
    ERROR_ALERT = (By.CLASS_NAME, "alert error")
    SUCCESS_ALERT = (By.CLASS_NAME, "alert success")
    TITLE = (By.TAG_NAME, "h1")
    
    def verify_page_loaded(self):
        """Verificar que la página de edición está cargada"""
        try:
            title = self.get_text(self.TITLE)
            return "Editar Producto" in title
        except:
            return False
    
    def get_current_values(self):
        """Obtener los valores actuales del formulario"""
        return {
            'codigo': self.find_element(self.CODIGO_INPUT).get_attribute('value'),
            'nombre': self.find_element(self.NOMBRE_INPUT).get_attribute('value'),
            'descripcion': self.find_element(self.DESCRIPCION_INPUT).get_attribute('value'),
            'precio': self.find_element(self.PRECIO_INPUT).get_attribute('value'),
            'stock': self.find_element(self.STOCK_INPUT).get_attribute('value'),
            'categoria': self.get_selected_category(),
            'proveedor': self.find_element(self.PROVEEDOR_INPUT).get_attribute('value'),
            'ubicacion': self.find_element(self.UBICACION_INPUT).get_attribute('value')
        }
    
    def get_selected_category(self):
        """Obtener la categoría seleccionada actualmente"""
        select_element = Select(self.find_element(self.CATEGORIA_SELECT))
        return select_element.first_selected_option.text
    
    def fill_product_form(self, product_data):
        """Llenar el formulario con datos del producto
        
        Args:
            product_data (dict): Diccionario con los campos del producto
                - codigo: Código del producto
                - nombre: Nombre del producto
                - descripcion: Descripción
                - precio: Precio del producto
                - stock: Cantidad en stock
                - categoria: Categoría del producto
                - proveedor: Proveedor (opcional)
                - ubicacion: Ubicación en bodega (opcional)
        """
        # Llenar campos de texto
        if 'codigo' in product_data:
            self.type(self.CODIGO_INPUT, product_data['codigo'])
        
        if 'nombre' in product_data:
            self.type(self.NOMBRE_INPUT, product_data['nombre'])
        
        if 'descripcion' in product_data:
            self.type(self.DESCRIPCION_INPUT, product_data['descripcion'])
        
        if 'precio' in product_data:
            self.type(self.PRECIO_INPUT, str(product_data['precio']))
        
        if 'stock' in product_data:
            self.type(self.STOCK_INPUT, str(product_data['stock']))
        
        # Seleccionar categoría
        if 'categoria' in product_data:
            select_element = Select(self.find_element(self.CATEGORIA_SELECT))
            select_element.select_by_visible_text(product_data['categoria'])
        
        if 'proveedor' in product_data:
            self.type(self.PROVEEDOR_INPUT, product_data['proveedor'])
        
        if 'ubicacion' in product_data:
            self.type(self.UBICACION_INPUT, product_data['ubicacion'])
    
    def update_field(self, field_name, value):
        """Actualizar un campo específico del formulario
        
        Args:
            field_name (str): Nombre del campo (codigo, nombre, descripcion, etc.)
            value: Valor a establecer
        """
        field_map = {
            'codigo': self.CODIGO_INPUT,
            'nombre': self.NOMBRE_INPUT,
            'descripcion': self.DESCRIPCION_INPUT,
            'precio': self.PRECIO_INPUT,
            'stock': self.STOCK_INPUT,
            'proveedor': self.PROVEEDOR_INPUT,
            'ubicacion': self.UBICACION_INPUT
        }
        
        if field_name == 'categoria':
            select_element = Select(self.find_element(self.CATEGORIA_SELECT))
            select_element.select_by_visible_text(value)
        elif field_name in field_map:
            self.type(field_map[field_name], str(value))
        else:
            raise ValueError(f"Campo '{field_name}' no reconocido")
    
    def submit_form(self):
        """Enviar el formulario de actualización"""
        self.click(self.ACTUALIZAR_BUTTON)
        time.sleep(1)  # Esperar un momento para que se procese
    
    def cancel_form(self):
        """Cancelar la edición y volver a la lista"""
        self.click(self.CANCELAR_BUTTON)
    
    def clear_field(self, field_name):
        """Limpiar un campo específico
        
        Args:
            field_name (str): Nombre del campo a limpiar
        """
        field_map = {
            'codigo': self.CODIGO_INPUT,
            'nombre': self.NOMBRE_INPUT,
            'descripcion': self.DESCRIPCION_INPUT,
            'precio': self.PRECIO_INPUT,
            'stock': self.STOCK_INPUT,
            'proveedor': self.PROVEEDOR_INPUT,
            'ubicacion': self.UBICACION_INPUT
        }
        
        if field_name in field_map:
            element = self.find_element(field_map[field_name])
            element.clear()
        else:
            raise ValueError(f"Campo '{field_name}' no reconocido")
    
    def is_error_displayed(self):
        """Verificar si se muestra un mensaje de error"""
        return self.is_element_present(self.ERROR_ALERT)
    
    def is_success_displayed(self):
        """Verificar si se muestra un mensaje de éxito"""
        return self.is_element_present(self.SUCCESS_ALERT)
    
    def get_error_message(self):
        """Obtener el mensaje de error si existe"""
        if self.is_error_displayed():
            return self.get_text(self.ERROR_ALERT)
        return ""
    
    def get_success_message(self):
        """Obtener el mensaje de éxito si existe"""
        if self.is_success_displayed():
            return self.get_text(self.SUCCESS_ALERT)
        return ""
    
    def validate_required_fields(self):
        """Validar que los campos requeridos no estén vacíos
        
        Returns:
            dict: Diccionario con el estado de validación de cada campo
        """
        validation = {
            'codigo': bool(self.find_element(self.CODIGO_INPUT).get_attribute('value')),
            'nombre': bool(self.find_element(self.NOMBRE_INPUT).get_attribute('value')),
            'precio': bool(self.find_element(self.PRECIO_INPUT).get_attribute('value')),
            'stock': bool(self.find_element(self.STOCK_INPUT).get_attribute('value')),
            'categoria': bool(self.get_selected_category())
        }
        return validation
    
    def is_form_valid(self):
        """Verificar si todos los campos requeridos están completos
        
        Returns:
            bool: True si todos los campos requeridos están llenos
        """
        validation = self.validate_required_fields()
        return all(validation.values())
