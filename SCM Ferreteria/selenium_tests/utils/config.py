class Config:
    BASE_URL = "http://localhost/SCM Ferreteria/productos"
    BROWSER = "firefox"  # Cambiar a firefox
    IMPLICIT_WAIT = 10
    EXPLICIT_WAIT = 20
    
    TEST_PRODUCT = {
        'codigo': 'TEST001',
        'nombre': 'Martillo Testing',
        'descripcion': 'Martillo para pruebas automatizadas',
        'precio': '1500.50',
        'stock': '25',
        'categoria': 'Herramientas',
        'proveedor': 'Proveedor Test',
        'ubicacion': 'Bodega A - Estante 1'
    }

    VALID_USER = {
        'username': 'admin',
        'password': 'admin123'
    }
    
    INVALID_USER = {
        'username': 'usuario_invalido',
        'password': 'password_incorrecto'
    }
    
    LOGIN_URL = "http://localhost/SCM Ferreteria/login.php"