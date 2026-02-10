"""
Módulo de utilidades y funciones auxiliares para las pruebas automatizadas
"""
import time
import os
import random
import string
from datetime import datetime
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoAlertPresentException


def generate_random_string(length=8):
    """Generar una cadena aleatoria
    
    Args:
        length (int): Longitud de la cadena
        
    Returns:
        str: Cadena aleatoria de letras y números
    """
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=length))


def generate_product_code(prefix="TEST"):
    """Generar un código de producto único
    
    Args:
        prefix (str): Prefijo para el código
        
    Returns:
        str: Código de producto único
    """
    timestamp = datetime.now().strftime("%m%d%H%M%S")
    random_suffix = generate_random_string(3)
    return f"{prefix}{timestamp}{random_suffix}"


def generate_test_product(codigo=None):
    """Generar datos de producto de prueba
    
    Args:
        codigo (str, optional): Código específico, si no se proporciona se genera uno
        
    Returns:
        dict: Diccionario con datos del producto
    """
    if codigo is None:
        codigo = generate_product_code()
    
    return {
        'codigo': codigo,
        'nombre': f'Producto Test {generate_random_string(4)}',
        'descripcion': f'Descripción de prueba generada automáticamente {datetime.now()}',
        'precio': str(round(random.uniform(10, 10000), 2)),
        'stock': str(random.randint(1, 100)),
        'categoria': random.choice(['Herramientas', 'Fijaciones', 'Pinturas', 'Materiales', 'Eléctricos', 'Plomería']),
        'proveedor': f'Proveedor Test {generate_random_string(3)}',
        'ubicacion': f'Bodega {random.choice(["A", "B", "C"])} - Estante {random.randint(1, 10)}'
    }


def wait_for_page_load(driver, timeout=10):
    """Esperar a que la página termine de cargar
    
    Args:
        driver: Instancia del WebDriver
        timeout (int): Tiempo máximo de espera en segundos
        
    Returns:
        bool: True si la página cargó correctamente
    """
    try:
        WebDriverWait(driver, timeout).until(
            lambda d: d.execute_script('return document.readyState') == 'complete'
        )
        return True
    except TimeoutException:
        return False


def wait_for_alert(driver, timeout=5):
    """Esperar a que aparezca una alerta
    
    Args:
        driver: Instancia del WebDriver
        timeout (int): Tiempo máximo de espera en segundos
        
    Returns:
        Alert: Objeto alerta si aparece, None en caso contrario
    """
    try:
        WebDriverWait(driver, timeout).until(EC.alert_is_present())
        return driver.switch_to.alert
    except TimeoutException:
        return None


def accept_alert(driver, timeout=5):
    """Aceptar una alerta si está presente
    
    Args:
        driver: Instancia del WebDriver
        timeout (int): Tiempo máximo de espera
        
    Returns:
        bool: True si se aceptó la alerta, False en caso contrario
    """
    try:
        alert = wait_for_alert(driver, timeout)
        if alert:
            alert.accept()
            return True
        return False
    except NoAlertPresentException:
        return False


def dismiss_alert(driver, timeout=5):
    """Rechazar una alerta si está presente
    
    Args:
        driver: Instancia del WebDriver
        timeout (int): Tiempo máximo de espera
        
    Returns:
        bool: True si se rechazó la alerta, False en caso contrario
    """
    try:
        alert = wait_for_alert(driver, timeout)
        if alert:
            alert.dismiss()
            return True
        return False
    except NoAlertPresentException:
        return False


def get_alert_text(driver, timeout=5):
    """Obtener el texto de una alerta
    
    Args:
        driver: Instancia del WebDriver
        timeout (int): Tiempo máximo de espera
        
    Returns:
        str: Texto de la alerta o None si no hay alerta
    """
    try:
        alert = wait_for_alert(driver, timeout)
        if alert:
            return alert.text
        return None
    except NoAlertPresentException:
        return None


def take_screenshot_with_timestamp(driver, name, directory="screenshots"):
    """Tomar captura de pantalla con timestamp
    
    Args:
        driver: Instancia del WebDriver
        name (str): Nombre base del archivo
        directory (str): Directorio donde guardar
        
    Returns:
        str: Ruta del archivo guardado
    """
    # Crear directorio si no existe
    if not os.path.exists(directory):
        os.makedirs(directory)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"{name}_{timestamp}.png"
    filepath = os.path.join(directory, filename)
    
    driver.save_screenshot(filepath)
    return filepath


def scroll_to_element(driver, element):
    """Hacer scroll hasta un elemento
    
    Args:
        driver: Instancia del WebDriver
        element: Elemento al que hacer scroll
    """
    driver.execute_script("arguments[0].scrollIntoView(true);", element)
    time.sleep(0.5)


def highlight_element(driver, element, duration=2):
    """Resaltar un elemento temporalmente (útil para debugging)
    
    Args:
        driver: Instancia del WebDriver
        element: Elemento a resaltar
        duration (int): Duración en segundos
    """
    original_style = element.get_attribute('style')
    driver.execute_script(
        "arguments[0].setAttribute('style', arguments[1]);",
        element,
        "border: 3px solid red; background-color: yellow;"
    )
    time.sleep(duration)
    driver.execute_script(
        "arguments[0].setAttribute('style', arguments[1]);",
        element,
        original_style
    )


def clear_screenshots(directory="screenshots"):
    """Limpiar directorio de screenshots
    
    Args:
        directory (str): Directorio a limpiar
        
    Returns:
        int: Número de archivos eliminados
    """
    if not os.path.exists(directory):
        return 0
    
    count = 0
    for filename in os.listdir(directory):
        if filename.endswith('.png'):
            os.remove(os.path.join(directory, filename))
            count += 1
    return count


def wait_and_retry(func, max_attempts=3, delay=1):
    """Reintentar una función si falla
    
    Args:
        func: Función a ejecutar
        max_attempts (int): Número máximo de intentos
        delay (int): Delay entre intentos en segundos
        
    Returns:
        El resultado de la función si tiene éxito
        
    Raises:
        Exception: La última excepción si todos los intentos fallan
    """
    last_exception = None
    
    for attempt in range(max_attempts):
        try:
            return func()
        except Exception as e:
            last_exception = e
            if attempt < max_attempts - 1:
                time.sleep(delay)
    
    raise last_exception


def compare_products(product1, product2, ignore_fields=None):
    """Comparar dos diccionarios de productos
    
    Args:
        product1 (dict): Primer producto
        product2 (dict): Segundo producto
        ignore_fields (list): Campos a ignorar en la comparación
        
    Returns:
        tuple: (bool, dict) - (True si son iguales, diferencias encontradas)
    """
    if ignore_fields is None:
        ignore_fields = []
    
    differences = {}
    
    all_keys = set(product1.keys()) | set(product2.keys())
    
    for key in all_keys:
        if key in ignore_fields:
            continue
        
        val1 = product1.get(key, None)
        val2 = product2.get(key, None)
        
        # Normalizar valores para comparación
        if val1 is not None:
            val1 = str(val1).strip()
        if val2 is not None:
            val2 = str(val2).strip()
        
        if val1 != val2:
            differences[key] = {'expected': val1, 'actual': val2}
    
    return len(differences) == 0, differences


def format_price(price):
    """Formatear precio al formato esperado
    
    Args:
        price: Precio a formatear (puede ser str, int o float)
        
    Returns:
        str: Precio formateado
    """
    if isinstance(price, str):
        price = float(price)
    return f"{price:.2f}"


def validate_product_data(product_data):
    """Validar que los datos del producto sean válidos
    
    Args:
        product_data (dict): Datos del producto
        
    Returns:
        tuple: (bool, list) - (True si es válido, lista de errores)
    """
    errors = []
    
    required_fields = ['codigo', 'nombre', 'precio', 'stock', 'categoria']
    
    for field in required_fields:
        if field not in product_data or not product_data[field]:
            errors.append(f"Campo requerido faltante: {field}")
    
    # Validar tipos
    if 'precio' in product_data:
        try:
            price = float(product_data['precio'])
            if price <= 0:
                errors.append("El precio debe ser mayor que 0")
        except ValueError:
            errors.append("El precio debe ser un número válido")
    
    if 'stock' in product_data:
        try:
            stock = int(product_data['stock'])
            if stock < 0:
                errors.append("El stock no puede ser negativo")
        except ValueError:
            errors.append("El stock debe ser un número entero")
    
    return len(errors) == 0, errors


def create_screenshots_directory():
    """Crear directorio de screenshots si no existe"""
    directory = "screenshots"
    if not os.path.exists(directory):
        os.makedirs(directory)
        print(f"✓ Directorio '{directory}' creado")
    return directory


def log_test_step(step_number, description):
    """Registrar un paso de prueba
    
    Args:
        step_number (int): Número del paso
        description (str): Descripción del paso
    """
    timestamp = datetime.now().strftime("%H:%M:%S")
    print(f"[{timestamp}] Paso {step_number}: {description}")


def get_current_timestamp():
    """Obtener timestamp actual en formato legible
    
    Returns:
        str: Timestamp formateado
    """
    return datetime.now().strftime("%Y%m%d_%H%M%S")


def safe_click(driver, element, timeout=5):
    """Hacer click de forma segura (esperando que sea clickeable)
    
    Args:
        driver: Instancia del WebDriver
        element: Elemento o locator
        timeout (int): Timeout en segundos
        
    Returns:
        bool: True si el click fue exitoso
    """
    try:
        if isinstance(element, tuple):
            # Es un locator
            element = WebDriverWait(driver, timeout).until(
                EC.element_to_be_clickable(element)
            )
        else:
            # Es un WebElement
            WebDriverWait(driver, timeout).until(
                EC.element_to_be_clickable(element)
            )
        
        element.click()
        return True
    except Exception as e:
        print(f"Error al hacer click: {e}")
        return False
