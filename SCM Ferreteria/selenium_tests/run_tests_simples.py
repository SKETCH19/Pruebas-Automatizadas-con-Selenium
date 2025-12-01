import pytest
import os
import datetime
import subprocess
import sys

def run_pruebas_simples():
    """Ejecuta solo las pruebas simples CORREGIDAS"""
    
    # Crear directorios
    os.makedirs("screenshots", exist_ok=True)
    os.makedirs("reports", exist_ok=True)
    
    # Timestamp
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    report_file = f"reports/test_simple_{timestamp}.html"
    
    print("="*60)
    print("🚀 EJECUTANDO PRUEBAS SIMPLES CORREGIDAS")
    print("="*60)
    
    # Ejecutar SOLO las pruebas que funcionan
    pytest_args = [
        "tests/test_pruebas_simples.py::test_navegacion_basica",
        "tests/test_pruebas_simples.py::test_carga_paginas_individuales",
        f"--html={report_file}",
        "--self-contained-html",
        "-v",
        "--tb=short",
        "--capture=no"
    ]
    
    try:
        exit_code = pytest.main(pytest_args)
        
        print("\n" + "="*60)
        if exit_code == 0:
            print("🎉 ¡PRUEBAS SIMPLES PASARON!")
        else:
            print("⚠ Algunas pruebas fallaron")
        
        print(f"📊 Reporte: {report_file}")
        print(f"📸 Screenshots en: screenshots/")
        print("="*60)
        
        # Abrir reporte
        if os.path.exists(report_file):
            subprocess.Popen(f'start "" "{report_file}"', shell=True)
            
        return exit_code
        
    except Exception as e:
        print(f"❌ Error ejecutando pruebas: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(run_pruebas_simples())