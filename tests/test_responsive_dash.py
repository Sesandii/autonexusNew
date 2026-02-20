# tests/test_responsive_dash.py
from tests.pages.login_page import LoginPage
from tests.pages.admin_dashboard_page import AdminDashboardPage

def test_dashboard_collapses_sidebar_on_small_width(driver, base_url, creds_admin):
    LoginPage(driver, base_url).open()
    LoginPage(driver, base_url).login(**creds_admin)
    dash = AdminDashboardPage(driver); dash.wait_loaded()

    # desktop
    driver.set_window_size(1366, 900)
    assert dash.driver.find_element(*dash.SIDEBAR).is_displayed()

    # mobile-ish
    driver.set_window_size(390, 844)
    # if your JS toggles a burger, assert either:
    # 1) sidebar hidden by CSS class
    # 2) a mobile menu button is visible
    # tweak selector to your burger icon if you have one
    # Example: assert not dash.driver.find_element(*dash.SIDEBAR).is_displayed()
