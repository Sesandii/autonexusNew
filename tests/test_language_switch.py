# tests/test_language_switch.py
from tests.pages.login_page import LoginPage
from tests.pages.admin_dashboard_page import AdminDashboardPage
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def _wait_active_lang_on_current_page(driver, expected_text, timeout=10):
    WebDriverWait(driver, timeout).until(
        lambda d: d.find_element(By.CSS_SELECTOR, ".lang-switcher a.active").text.strip() == expected_text
    )

def _click_lang(driver, code):
    driver.find_element(By.CSS_SELECTOR, f".lang-switcher a[href*='?lang={code}']").click()

def _back_to_dashboard(driver, base_url):
    # If your dashboard route differs, adjust here:
    driver.get(f"{base_url}/admin/dashboard")

def test_switch_languages_persists_across_pages(driver, base_url, creds_admin):
    # Login
    LoginPage(driver, base_url).open()
    LoginPage(driver, base_url).login(**creds_admin)

    dash = AdminDashboardPage(driver, base_url)
    dash.wait_loaded()

    # --- Switch to Sinhala on dashboard ---
    _click_lang(driver, "si")
    _wait_active_lang_on_current_page(driver, "සිං")

    # Navigate away (Services) then return to dashboard to verify persistence
    dash.go_services()
    _back_to_dashboard(driver, base_url)
    dash.wait_loaded()
    _wait_active_lang_on_current_page(driver, "සිං")

    # --- Switch to Tamil ---
    _click_lang(driver, "ta")
    _wait_active_lang_on_current_page(driver, "தமிழ்")

    # Navigate away and back; confirm it stuck
    dash.go_services()
    _back_to_dashboard(driver, base_url)
    dash.wait_loaded()
    _wait_active_lang_on_current_page(driver, "தமிழ்")

    # --- Back to English ---
    _click_lang(driver, "en")
    _wait_active_lang_on_current_page(driver, "EN")

    # Sanity: title visible
    WebDriverWait(driver, 5).until(EC.visibility_of_element_located(AdminDashboardPage.TITLE))
