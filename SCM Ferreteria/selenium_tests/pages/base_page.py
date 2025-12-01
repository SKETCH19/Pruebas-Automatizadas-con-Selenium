from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
import os
import time

class BasePage:
    def __init__(self, driver):
        self.driver = driver
        self.wait = WebDriverWait(driver, 10)
    
    def find_element(self, locator):
        return self.wait.until(EC.presence_of_element_located(locator))
    
    def find_elements(self, locator):
        return self.wait.until(EC.presence_of_all_elements_located(locator))
    
    def find_clickable_element(self, locator):
        return self.wait.until(EC.element_to_be_clickable(locator))
    
    def click(self, locator):
        self.find_clickable_element(locator).click()
    
    def type(self, locator, text):
        element = self.find_element(locator)
        element.clear()
        element.send_keys(text)
    
    def get_text(self, locator):
        return self.find_element(locator).text
    
    def take_screenshot(self, name):
        timestamp = time.strftime("%Y%m%d_%H%M%S")
        filename = f"{name}_{timestamp}.png"
        screenshot_path = os.path.join("screenshots", filename)
        self.driver.save_screenshot(screenshot_path)
        return screenshot_path
    
    def is_element_present(self, locator):
        try:
            self.find_element(locator)
            return True
        except TimeoutException:
            return False