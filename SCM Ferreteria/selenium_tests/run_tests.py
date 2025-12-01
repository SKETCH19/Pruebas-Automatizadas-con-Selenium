import pytest
import os
import datetime
import subprocess
import sys

def run_tests():
    # Crear directorios necesarios
    os.makedirs("screenshots", exist_ok=True)
    os.makedirs("reports/html_reports", exist_ok=True)
    
    # Timestamp para el reporte
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    report_file = f"reports/html_reports/test_report_{timestamp}.html"
    
    print("🚀 Iniciando pruebas automatizadas...")
    print(f"📊 Reporte se guardará en: {report_file}")
    print(f"📸 Screenshots en carpeta: screenshots/")
    
    # Ejecutar pruebas con pytest
    pytest_args = [
        "tests/",
        f"--html={report_file}",
        "--self-contained-html",
        "--capture=tee-sys",
        "-v",
        "--tb=short",
        "--maxfail=5"
    ]
    
    try:
        exit_code = pytest.main(pytest_args)
        
        print("\n" + "="*50)
        if exit_code == 0:
            print(f"✅ TODAS las pruebas PASARON!")
        elif exit_code == 1:
            print(f"⚠ ALGUNAS pruebas FALLARON")
        else:
            print(f"❌ Error en ejecución (código: {exit_code})")
        print("="*50)
        
        print(f"\n📋 Reporte generado: {report_file}")
        
    except Exception as e:
        print(f"❌ Error crítico ejecutando pruebas: {e}")
        return 1
    
    # Abrir el reporte automáticamente en el navegador
    if os.path.exists(report_file):
        print("🌐 Abriendo reporte en navegador...")
        try:
            subprocess.Popen(f'start "" "{report_file}"', shell=True)
        except:
            print("⚠ No se pudo abrir el reporte automáticamente")
    
    return exit_code if 'exit_code' in locals() else 0

if __name__ == "__main__":
    sys.exit(run_tests())