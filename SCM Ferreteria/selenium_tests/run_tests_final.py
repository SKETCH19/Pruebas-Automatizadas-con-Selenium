import pytest
import os
import datetime
import subprocess
import sys

def run_pruebas_finales():
    """Ejecuta las pruebas definitivas (sin problemas de proxy)"""
    
    # Crear directorios
    os.makedirs("reports", exist_ok=True)
    
    # Timestamp
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    report_file = f"reports/test_final_{timestamp}.html"
    
    print("="*60)
    print("🚀 EJECUTANDO PRUEBAS DEFINITIVAS - SIN SELENIUM")
    print("="*60)
    
    # Ejecutar todas las pruebas del archivo final
    pytest_args = [
        "tests/test_final.py",
        f"--html={report_file}",
        "--self-contained-html",
        "-v",
        "--tb=no",
        "--capture=no"
    ]
    
    try:
        exit_code = pytest.main(pytest_args)
        
        print("\n" + "="*60)
        print(f"📊 Reporte generado: {report_file}")
        
        # Abrir reporte
        if os.path.exists(report_file):
            subprocess.Popen(f'start "" "{report_file}"', shell=True)
        
        return exit_code
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1

if __name__ == "__main__":
    # Primero instalar requests si no está
    try:
        import requests
    except:
        print("📦 Instalando requests...")
        import subprocess
        subprocess.check_call([sys.executable, "-m", "pip", "install", "requests"])
    
    sys.exit(run_pruebas_finales())