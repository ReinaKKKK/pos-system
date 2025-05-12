
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.remote.webdriver import WebDriver
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

BASE_URL = "http://host.docker.internal:18888/pos-system"
GRID_URL = "http://selenium:4444/wd/hub"

@pytest.fixture
def driver() -> WebDriver:
    chrome_opts = Options()
    chrome_opts.add_argument("--headless")
    chrome_opts.add_argument("--no-sandbox")
    chrome_opts.add_argument("--disable-dev-shm-usage")
    driver = webdriver.Remote(
        command_executor=GRID_URL,
        options=chrome_opts
    )
    yield driver
    driver.quit()

def login(driver: WebDriver, username="tanaka", password="Tanaka123!"):
    driver.get(f"{BASE_URL}/auth.php")
    driver.find_element(By.NAME, "username").send_keys(username)
    driver.find_element(By.NAME, "password").send_keys(password)
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
    WebDriverWait(driver, 5).until(
        EC.url_contains("index.php")
    )

def test_login(driver):
    login(driver)
    # index.php にリダイレクトされていること
    WebDriverWait(driver, 5).until(EC.url_contains("index.php"))
    assert "index.php" in driver.current_url 

def test_create_item(driver):
    name = "Espresso"
    price = "300"
    stock = "10"
    login(driver)
    driver.get(f"{BASE_URL}/items/create.php")

    driver.find_element(By.NAME, "name").send_keys(name)
    driver.find_element(By.NAME, "price").send_keys(price)
    driver.find_element(By.NAME, "stock").send_keys(stock)
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()

    # items/list.php にリダイレクトされるのを待つ
    WebDriverWait(driver, 5).until(
            EC.url_contains("items/list.php")
    )
    # (リダイレクトが確実でなければ) 一覧画面を直接開いて検証
    driver.get(f"{BASE_URL}/items/list.php")
    assert name in driver.page_source

def test_sales_and_csv(driver):
    login(driver)

    # --- 在庫あり商品を作成 ---
    name = "TestCoffee"
    price = "300"
    stock = "10"
    driver.get(f"{BASE_URL}/items/create.php")
    driver.find_element(By.NAME, "name").send_keys(name)
    driver.find_element(By.NAME, "price").send_keys(price)
    driver.find_element(By.NAME, "stock").send_keys(stock)
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
    WebDriverWait(driver, 5).until(EC.url_contains("items/list.php"))

    # --- ここから売上登録 ---
    driver.get(f"{BASE_URL}/sales/create.php")
    select_el = driver.find_element(By.NAME, "item_id")
    select = Select(select_el)
    # テキスト部分に商品名が含まれる最初の option を探して value を取得
    item_value = None
    for opt in select.options:
        if name in opt.text:
            item_value = opt.get_attribute("value")
            break
    assert item_value, f"商品 '{name}' の option が見つかりませんでした"
    select.select_by_value(item_value)
    # --- ここから売上登録の残りステップ ---
    qty = driver.find_element(By.NAME, "quantity")
    qty.clear()                     # 既存の '1' を消す
    qty.send_keys("2")              # 改めて「2」を入力
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
    # 登録後リダイレクトを待つ
    WebDriverWait(driver, 5).until(EC.url_contains("list.php"))

    # 売上一覧画面へ移動して検証
    # （リダイレクト後は自動的に一覧なので driver.get は不要かもしれませんが、念のため）
    driver.get(f"{BASE_URL}/sales/list.php")
    assert name in driver.page_source
    assert "660円" in driver.page_source  # 300×2 + tax10%

    # --- CSV エクスポートの中身をチェック ---
    import urllib.request
    url = f"{BASE_URL}/sales/export.php"
    resp = urllib.request.urlopen(url)
    csv = resp.read().decode('utf-8')

    # ヘッダー行があること
    assert "日時,商品ID,商品名,数量,税額,税込価格" in csv
    # 先ほど登録したレコードが含まれていること
    assert name in csv
    # 数量・合計金額も
    assert ",2," in csv
    assert ",660" in csv
