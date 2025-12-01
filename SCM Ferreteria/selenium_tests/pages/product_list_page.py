from selenium.webdriver.common.by import By
from .base_page import BasePage

class ProductListPage(BasePage):
    # Locators
    ADD_PRODUCT_BUTTON = (By.LINK_TEXT, "➕ Agregar Nuevo Producto")
    PRODUCT_TABLE = (By.TAG_NAME, "table")
    PRODUCT_ROWS = (By.CSS_SELECTOR, "table tbody tr")
    EDIT_BUTTON = (By.CLASS_NAME, "btn-edit")
    DELETE_BUTTON = (By.CLASS_NAME, "btn-delete")
    SUCCESS_ALERT = (By.CLASS_NAME, "alert")
    PRODUCT_CODE_CELL = (By.CSS_SELECTOR, "td:nth-child(1)")
    
    def click_add_product(self):
        self.click(self.ADD_PRODUCT_BUTTON)
    
    def get_product_count(self):
        return len(self.find_elements(self.PRODUCT_ROWS))
    
    def search_product_by_code(self, product_code):
        rows = self.find_elements(self.PRODUCT_ROWS)
        for row in rows:
            code_cell = row.find_element(*self.PRODUCT_CODE_CELL)
            if code_cell.text == product_code:
                return row
        return None
    
    def click_edit_product(self, product_code):
        product_row = self.search_product_by_code(product_code)
        if product_row:
            edit_btn = product_row.find_element(*self.EDIT_BUTTON)
            edit_btn.click()
            return True
        return False
    
    def click_delete_product(self, product_code):
        product_row = self.search_product_by_code(product_code)
        if product_row:
            delete_btn = product_row.find_element(*self.DELETE_BUTTON)
            delete_btn.click()
            # Manejar la alerta de confirmación
            self.driver.switch_to.alert.accept()
            return True
        return False
    
    def is_success_message_displayed(self):
        return self.is_element_present(self.SUCCESS_ALERT)