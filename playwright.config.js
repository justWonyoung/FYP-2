import { defineConfig, devices } from '@playwright/test';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const php = 'C:\\laragon\\bin\\php\\php-8.3.33-Win32-vs16-x64\\php.exe';
const database = 'C:\\laragon\\www\\dbfm\\database\\playwright.sqlite';
const projectRoot = path.dirname(fileURLToPath(import.meta.url));
const chromiumExecutable = path.join(
    projectRoot,
    'node_modules',
    'playwright-core',
    '.local-browsers',
    'chromium-1248',
    'chrome-win64',
    'chrome.exe'
);

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,
    workers: 1,
    timeout: 30_000,
    retries: 0,
    globalSetup: './playwright/global-setup.js',
    reporter: [
        ['list'],
        ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ],
    use: {
        baseURL: 'http://127.0.0.1:8000',
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
        launchOptions: {
            executablePath: chromiumExecutable,
        },
    },
    webServer: {
        command: `"${php}" artisan serve --host=127.0.0.1 --port=8000`,
        url: 'http://127.0.0.1:8000/login',
        reuseExistingServer: false,
        timeout: 120_000,
        env: {
            APP_ENV: 'testing',
            APP_URL: 'http://127.0.0.1:8000',
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: database,
            SESSION_DRIVER: 'file',
            CACHE_STORE: 'array',
            QUEUE_CONNECTION: 'sync',
            MAIL_MAILER: 'array',
        },
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
