# tests/test_smoke_admin_flow.py
from tests.pages.login_page import LoginPage
from tests.pages.admin_dashboard_page import AdminDashboardPage
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def test_admin_can_login_and_navigate(driver, base_url, creds_admin):
    # 1) login
    login = LoginPage(driver, base_url)
    login.open()
    login.login(creds_admin["email"], creds_admin["password"])

    # 2) dashboard visible (assert no PHP warnings in page)
    dash = AdminDashboardPage(driver)
    dash.wait_loaded()
    html = driver.page_source
    assert "Warning:" not in html and "Fatal error" not in html and "Notice:" not in html

    # 3) open Services list (should not be hardcoded)
    dash.go_services()
    WebDriverWait(driver, 10).until(
        EC.title_contains("Service")  # or assert a table exists
    )
    assert "Create Service" in driver.page_source  # tweak to your UI

    # 4) navigate to Branches and Mechanics quickly to ensure links work
    driver.back()
    dash.go_branches()
    assert "Add Branch" in driver.page_source
    driver.back()
    dash.go_mechanics()
    assert "Add Mechanic" in driver.page_source
