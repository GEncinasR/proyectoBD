import os
import re
import pytest
from playwright.sync_api import Page, expect

BASE = os.environ.get("BASE_URL", "http://localhost/proyectoBD")

def test_index_pagina_publica(page: Page):
    page.goto(f"{BASE}/main/index.html")
    expect(page).to_have_title("Bienvenido | Gimnasio")
    expect(page.locator("text=X-AUST").first).to_be_visible()

def test_login_admin(page: Page):
    page.goto(f"{BASE}/main/login.html")
    page.fill("#username", "admin")
    page.fill("#password", "cualquiera")
    with page.expect_navigation():
        page.click("button[type=submit]")
    expect(page).to_have_url(re.compile(r".*/admin/index.php.*"))
    expect(page.locator("h1")).to_contain_text("PANEL DE ADMINISTRACIÓN")

def test_login_coach_inexistente(page: Page):
    page.goto(f"{BASE}/main/login.html")
    page.fill("#username", "999999")
    page.fill("#password", "x")
    with page.expect_navigation():
        page.click("button[type=submit]")
    expect(page).to_have_url(re.compile(r".*error=coach_no_encontrado.*"))

def test_checkin_id_invalido(page: Page):
    page.goto(f"{BASE}/main/checkIn.php")
    page.fill('input[name="idCliente"]', "999999")
    page.click('button[type=submit], input[type=submit]')
    expect(page.locator("body")).to_contain_text("No se encontró")

def test_ordenar_tabla_clientes(page: Page):
    page.goto(f"{BASE}/admin/consultarClientes.php")
    primer_id_antes = page.locator("tbody tr td:first-child").first.inner_text()
    with page.expect_navigation():
        page.click('th[data-columna="idCliente"]')
    expect(page).to_have_url(re.compile(r".*ordenar=idCliente.*"))
    expect(page.locator("tbody tr td:first-child").first).to_be_visible()
