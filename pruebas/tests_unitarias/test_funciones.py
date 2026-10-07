import subprocess
import json

from pathlib import Path
PROYECTO = str(Path(__file__).resolve().parents[2])

def php_eval(code: str) -> str:
    r = subprocess.run(
        ["php", "-r", f"include '{PROYECTO}/funciones.php'; {code}"],
        capture_output=True, text=True, check=True,
    )
    return r.stdout.strip()

def test_calcular_fecha_vencimiento_mensualidad():
    assert php_eval("echo calcularFechaVencimiento('MENSUALIDAD','2026-01-15');") == "2026-02-15"

def test_calcular_fecha_vencimiento_anualidad():
    assert php_eval("echo calcularFechaVencimiento('ANUALIDAD','2026-01-15');") == "2027-01-15"

def test_calcular_fecha_vencimiento_provisional_es_null():
    assert php_eval("var_dump(calcularFechaVencimiento('PROVISIONAL','2026-01-15'));") == "NULL"

def test_calcular_fecha_vencimiento_dia_prueba():
    assert php_eval("echo calcularFechaVencimiento('DIA_PRUEBA','2026-03-01');") == "2026-03-02"

def test_esta_vencida_true():
    ayer = php_eval("echo (new DateTime('-1 day'))->format('Y-m-d');")
    assert php_eval(f"var_dump(estaVencida('{ayer}','MENSUALIDAD'));") == "bool(true)"

def test_esta_vencida_vigente():
    manana = php_eval("echo (new DateTime('+1 day'))->format('Y-m-d');")
    assert php_eval(f"var_dump(estaVencida('{manana}','MENSUALIDAD'));") == "bool(false)"

def test_esta_vencida_provisional():
    assert php_eval("var_dump(estaVencida('2020-01-01','PROVISIONAL'));") == "bool(false)"

def test_nombre_membresia_conocido():
    assert php_eval("echo nombreMembresia('DIA_PRUEBA');") == "Día de Prueba"

def test_nombre_membresia_desconocido_devuelve_mismo():
    assert php_eval("echo nombreMembresia('XYZ');") == "XYZ"
