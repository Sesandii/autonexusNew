from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoSuchElementException

class AdminDashboardPage:
    # Proof dashboard loaded
    SIDEBAR = (By.CSS_SELECTOR, "aside.sidebar")  # adjust if your admin sidebar differs
    TITLE = (By.CSS_SELECTOR, "h1.page-title")    # "Admin Dashboard"

    # Language switcher (preferred, after adding data-e2e)
    LANG_EN = (By.CSS_SELECTOR, "[data-e2e='lang-en']")
    LANG_SI = (By.CSS_SELECTOR, "[data-e2e='lang-si']")
    LANG_TA = (By.CSS_SELECTOR, "[data-e2e='lang-ta']")
    # Fallbacks (without data-e2e)
    _FALLBACK_LANG_EN = [(By.CSS_SELECTOR, ".lang-switcher a[href*='?lang=en']")]
    _FALLBACK_LANG_SI = [(By.CSS_SELECTOR, ".lang-switcher a[href*='?lang=si']")]
    _FALLBACK_LANG_TA = [(By.CSS_SELECTOR, ".lang-switcher a[href*='?lang=ta']")]

    # Nav to Services
    NAV_SERVICES = (By.CSS_SELECTOR, "[data-e2e='nav-services']")  # if you add this in admin sidebar
    # Quick Link to Add Service
    QL_ADD_SERVICE = (By.CSS_SELECTOR, "[data-e2e='ql-add-service']")
    # Fallbacks
    _FALLBACK_SERVICES = [
        (By.CSS_SELECTOR, "a[href*='/admin/services']"),                     # any /admin/services*
        (By.XPATH, "//a[contains(., 'Service') or contains(., 'Services')]") # text-based
    ]

    def __init__(self, driver, base_url=None):
        self.driver = driver
        self.base_url = base_url

    def wait_loaded(self, timeout=12):
        WebDriverWait(self.driver, timeout).until(
            EC.any_of(
                EC.visibility_of_element_located(self.SIDEBAR),
                EC.visibility_of_element_located(self.TITLE),
            )
        )

    def _click_first_that_exists(self, locators, timeout=8):
        last = None
        for by, sel in locators:
            try:
                el = WebDriverWait(self.driver, timeout).until(
                    EC.element_to_be_clickable((by, sel))
                )
                el.click()
                return True
            except Exception as e:
                last = e
        if last:
            raise last
        raise NoSuchElementException("None of the locators matched")

    # Public helpers
    def go_services(self):
        # Prefer an admin nav item if present
        try:
            WebDriverWait(self.driver, 3).until(EC.element_to_be_clickable(self.NAV_SERVICES)).click()
            return
        except Exception:
            pass
        # Otherwise, use Quick Link to Add Service (exists in your markup)
        try:
            WebDriverWait(self.driver, 3).until(EC.element_to_be_clickable(self.QL_ADD_SERVICE)).click()
            return
        except Exception:
            pass
        # Fallback to any services link
        self._click_first_that_exists(self._FALLBACK_SERVICES)

    def set_lang_en(self):
        try:
            WebDriverWait(self.driver, 3).until(EC.element_to_be_clickable(self.LANG_EN)).click()
        except Exception:
            self._click_first_that_exists(self._FALLBACK_LANG_EN)

    def set_lang_si(self):
        try:
            WebDriverWait(self.driver, 3).until(EC.element_to_be_clickable(self.LANG_SI)).click()
        except Exception:
            self._click_first_that_exists(self._FALLBACK_LANG_SI)

    def set_lang_ta(self):
        try:
            WebDriverWait(self.driver, 3).until(EC.element_to_be_clickable(self.LANG_TA)).click()
        except Exception:
            self._click_first_that_exists(self._FALLBACK_LANG_TA)
