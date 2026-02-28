# tests/conftest.py
import os, time, pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options

BASE_URL = os.environ.get("AUTONEXUS_BASE_URL", "http://localhost/autonexus")

@pytest.fixture(scope="session")
def base_url():
    return BASE_URL

@pytest.fixture(scope="session")
def creds_admin():
    return {"email": "admin@autonexus.com", "password": "Admin@123"}  # adjust to real creds

@pytest.fixture
def driver(request):
    opts = Options()
    # comment out next line if you want to SEE the browser
    opts.add_argument("--headless=new")
    opts.add_argument("--window-size=1366,900")

    # move capabilities here (Selenium 4 way)
    opts.set_capability("goog:loggingPrefs", {"browser": "ALL"})

    drv = webdriver.Chrome(options=opts)

    yield drv

    # take a screenshot if the test failed
    try:
        failed = getattr(request.node, "rep_call", None)
        if failed and failed.failed:
            ts = int(time.time())
            os.makedirs("tests_artifacts", exist_ok=True)
            path = f"tests_artifacts/screenshot_{request.node.name}_{ts}.png"
            drv.save_screenshot(path)
            print(f"\n[Saved screenshot] {path}")
    finally:
        drv.quit()

# proper hook for accessing test outcome in fixtures
@pytest.hookimpl(hookwrapper=True, tryfirst=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    rep = outcome.get_result()
    setattr(item, "rep_" + rep.when, rep)
