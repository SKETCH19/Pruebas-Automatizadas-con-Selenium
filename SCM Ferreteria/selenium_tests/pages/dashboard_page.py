from selenium.webdriver.common.by import By
from .base_page import BasePage

class DashboardPage(BasePage):
    # Locators
    PRODUCT_MANAGEMENT_LINK = (By.LINK_TEXT, "Gestión de Productos")
    ADD_PRODUCT_BUTTON = (By.LINK_TEXT, "➕ Agregar Producto")
    VIEW_PRODUCTS_BUTTON = (By.LINK_TEXT, "📋 Ver Todos los Productos")
    TOTAL_PRODUCTS_COUNT = (By.CSS_SELECTOR, ".card:nth-child(1) .number")
    LOW_STOCK_COUNT = (By.CSS_SELECTOR, ".card:nth-child(3) .number")
    
    def navigate_to_product_management(self):
        self.click(self.PRODUCT_MANAGEMENT_LINK)
    
    def click_add_product(self):
        self.click(self.ADD_PRODUCT_BUTTON)
    
    def click_view_products(self):
        self.click(self.VIEW_PRODUCTS_BUTTON)
    
    def get_total_products_count(self):
        return int(self.get_text(self.TOTAL_PRODUCTS_COUNT))
    
    def get_low_stock_count(self):
        return int(self.get_text(self.LOW_STOCK_COUNT))