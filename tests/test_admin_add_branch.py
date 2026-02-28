from datetime import datetime
from tests.pages.login_page import LoginPage
from tests.pages.admin_dashboard_page import AdminDashboardPage
from tests.pages.branches_page import BranchCreatePage, BranchListPage
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def _code_br_3():
    # BR + exactly 3 digits, e.g., BR123
    return "BR" + datetime.now().strftime("%S%f")[-3:]  # pretty random 3 digits

def _attempt_create(driver, base_url, payload):
    """Submit the form and then check the list page for the new row.
       Returns True if the row is found (success), False otherwise."""
    # Submit already done by caller; always go to list and check
    driver.get(f"{base_url}/admin/branches")
    lst = BranchListPage(driver)
    lst.wait_loaded()
    lst.wait_table()
    return lst.has_row_with(code=payload["code"], name=payload["name"])

def test_admin_can_add_branch(driver, base_url, creds_admin):
    # 1) Login
    LoginPage(driver, base_url).open()
    LoginPage(driver, base_url).login(**creds_admin)

    dash = AdminDashboardPage(driver, base_url)
    dash.wait_loaded()

    # 2) Open Add Branch form
    dash.go_add_branch()

    create = BranchCreatePage(driver)
    create.wait_loaded()

    code = _code_br_3()
    name = f"Test Branch {code}"
    base_payload = {
        "code":         code,
        "status":       "active",
        "name":         name,
        "city":         "Galle",
        "phone":        "091-1234567",
        "email":        f"{code.lower()}@autonexus.test",
        "capacity":     10,
        "staff":        5,
        "working_hours":"Mon–Fri 08:00–17:00",
        "notes":        "Created by Selenium",
        # created_at: your form already sets today's date; we can omit
    }

    # 3) Choose a manager that isn't already assigned.
    managers = create.get_manager_values()  # list of (value, text), excludes empty
    assert managers, "No managers available. Create a manager first."

    success = False
    for value, label in managers:
        # reload fresh form each attempt to avoid stale state
        driver.get(f"{base_url}/admin/branches/create")
        create.wait_loaded()

        payload = dict(base_payload)
        payload["manager"] = value

        create.fill_and_submit(payload)
        if _attempt_create(driver, base_url, payload):
            success = True
            break

    assert success, "Could not create a branch with any available manager (they may all be assigned already)."

    # Final sanity: no PHP warnings on the list page
    html = driver.page_source
    assert "Warning:" not in html and "Notice:" not in html and "Fatal error" not in html
