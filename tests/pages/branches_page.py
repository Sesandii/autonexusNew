# tests/pages/branches_page.py
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC

class BranchCreatePage:
    TITLE    = (By.XPATH, "//h1[contains(.,'Add New Branch')] | //h2[contains(.,'Add New Branch')]")
    CODE     = (By.NAME, "code")
    STATUS   = (By.NAME, "status")
    NAME     = (By.NAME, "name")
    CITY     = (By.NAME, "city")
    MANAGER  = (By.NAME, "manager")
    PHONE    = (By.NAME, "phone")
    EMAIL    = (By.NAME, "email")
    CREATED  = (By.NAME, "created_at")
    CAPACITY = (By.NAME, "capacity")
    STAFF    = (By.NAME, "staff")
    HOURS    = (By.NAME, "working_hours")
    NOTES    = (By.NAME, "notes")
    SUBMIT   = (By.CSS_SELECTOR, "button[type='submit'], input[type='submit']")

    def __init__(self, driver):
        self.driver = driver

    def wait_loaded(self, timeout=12):
        WebDriverWait(self.driver, timeout).until(
            EC.visibility_of_element_located(self.TITLE)
        )

    def _fill(self, locator, value, clear=True, optional=False):
        if value is None:
            return
        try:
            el = WebDriverWait(self.driver, 8).until(EC.visibility_of_element_located(locator))
            if clear:
                try: el.clear()
                except: pass
            el.send_keys(str(value))
        except Exception:
            if not optional:
                raise

    def _select_by_value(self, locator, value, optional=False):
        try:
            el = WebDriverWait(self.driver, 8).until(EC.element_to_be_clickable(locator))
            sel = Select(el)
            sel.select_by_value(value)  # raise if not found
        except Exception:
            if not optional:
                raise

    def get_manager_values(self):
        """Return list of (value, text) for manager select, EXCLUDING the empty '— None —' value."""
        el = WebDriverWait(self.driver, 8).until(EC.presence_of_element_located(self.MANAGER))
        sel = Select(el)
        vals = []
        for opt in sel.options:
            val = (opt.get_attribute("value") or "").strip()
            if val != "":  # exclude empty option
                vals.append((val, opt.text.strip()))
        return vals

    def fill_and_submit(self, data):
        # required fields
        self._fill(self.CODE,   data["code"])
        self._fill(self.NAME,   data["name"])
        self._fill(self.CITY,   data["city"])

        # selects (status optional, manager required by your logic)
        self._select_by_value(self.STATUS,  data.get("status", "active"), optional=True)
        if "manager" in data:
            self._select_by_value(self.MANAGER, data["manager"])
        else:
            raise AssertionError("manager is required by app logic; provide a non-empty value")

        # optionals
        self._fill(self.PHONE,    data.get("phone"), optional=True)
        self._fill(self.EMAIL,    data.get("email"), optional=True)
        self._fill(self.CREATED,  data.get("created_at"), optional=True)
        self._fill(self.CAPACITY, data.get("capacity"), optional=True)
        self._fill(self.STAFF,    data.get("staff"), optional=True)
        self._fill(self.HOURS,    data.get("working_hours"), optional=True)
        self._fill(self.NOTES,    data.get("notes"), optional=True)

        WebDriverWait(self.driver, 8).until(EC.element_to_be_clickable(self.SUBMIT)).click()


class BranchListPage:
    TITLE = (By.XPATH, "//h1[contains(.,'Branch Management')] | //h2[contains(.,'Branch Management')]")
    TABLE = (By.CSS_SELECTOR, "table#tbl")
    ADD_LINK = (By.CSS_SELECTOR, "a.add-btn[href*='/admin/branches/create']")

    def __init__(self, driver):
        self.driver = driver

    def wait_loaded(self, timeout=12):
        WebDriverWait(self.driver, timeout).until(
            EC.any_of(
                EC.visibility_of_element_located(self.TITLE),
                EC.visibility_of_element_located(self.TABLE),
                EC.visibility_of_element_located(self.ADD_LINK),
            )
        )

    def wait_table(self, timeout=10):
        WebDriverWait(self.driver, timeout).until(EC.visibility_of_element_located(self.TABLE))

    def has_row_with(self, code=None, name=None):
        page = self.driver.page_source
        ok = True
        if code: ok = ok and (code in page)
        if name: ok = ok and (name in page)
        return ok
