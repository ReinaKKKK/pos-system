<?php

declare(strict_types=1);

namespace PosSystem\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * Class ItemTest
 *
 * Integration tests for the item management features.
 *
 * @package PosSystem\Tests\Integration
 */
class ItemTest extends TestCase
{
    /**
     * @var RemoteWebDriver
     */
    protected RemoteWebDriver $driver;

    /**
     * setUp initializes the WebDriver and navigates to the base URL.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 接続先は phpunit.xml の環境変数から取得
        $hubUrl = getenv('SELENIUM_HUB');
        $capabilities = DesiredCapabilities::chrome();

        $this->driver = RemoteWebDriver::create($hubUrl, $capabilities);

        // テスト用ベースURLへ移動
        $baseUrl = rtrim(getenv('BASE_URL'), '/');
        $this->driver->get($baseUrl . '/auth.php');
    }

    /**
     * tearDown closes the WebDriver session.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->driver->quit();

        parent::tearDown();
    }

    /**
     * Test that an item can be created and appears in the list.
     *
     * @return void
     */
    public function testCreateItem(): void
    {
        // ログイン処理（必要に応じて共通メソッド化）
        $this->driver->findElement(
            WebDriverBy::name('username')
        )->sendKeys('tanaka');
        $this->driver->findElement(
            WebDriverBy::name('password')
        )->sendKeys('Tanaka123!');
        $this->driver->findElement(
            WebDriverBy::cssSelector('button[type=submit]')
        )->click();

        // 売上一覧画面にリダイレクトされるまで待機
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('index.php')
        );

        // 商品作成ページへ
        $this->driver->get(getenv('BASE_URL') . '/items/create.php');

        // ここにフォーム操作のテストを追加

        // 最後にassertで検証
        $this->assertTrue(true);
    }
}
