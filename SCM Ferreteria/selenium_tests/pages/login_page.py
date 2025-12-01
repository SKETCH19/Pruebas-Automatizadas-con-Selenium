from selenium.webdriver.common.by import By
from .base_page import BasePage

class LoginPage(BasePage):
    # IMPORTANTE: Tu sistema NO tiene login, así que estos selectores son ficticios
    # Si tu sistema tuviera login, ajustarías estos selectores
    USERNAME_INPUT = (By.ID, "username")  
    PASSWORD_INPUT = (By.ID, "password")  
    LOGIN_BUTTON = (By.ID, "login-btn")   
    ERROR_MESSAGE = (By.CLASS_NAME, "error")
    SUCCESS_MESSAGE = (By.CLASS_NAME, "success")
    
    def enter_username(self, username):
        if self.is_element_present(self.USERNAME_INPUT):
            self.type(self.USERNAME_INPUT, username)
    
    def enter_password(self, password):
        if self.is_element_present(self.PASSWORD_INPUT):
            self.type(self.PASSWORD_INPUT, password)
    
    def click_login(self):
        if self.is_element_present(self.LOGIN_BUTTON):
            self.click(self.LOGIN_BUTTON)
    
    def login(self, username, password):
        self.enter_username(username)
        self.enter_password(password)
        self.click_login()
    
    def get_error_message(self):
        if self.is_element_present(self.ERROR_MESSAGE):
            return self.get_text(self.ERROR_MESSAGE)
        return ""
    
    def is_login_successful(self):
        # Verificar si estamos en la página principal
        return "index.php" in self.driver.current_url