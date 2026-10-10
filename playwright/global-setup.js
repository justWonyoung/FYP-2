import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const database = path.join(projectRoot, 'database', 'playwright.sqlite');
const php = 'C:\\laragon\\bin\\php\\php-8.3.33-Win32-vs16-x64\\php.exe';

export default function globalSetup() {
    if (path.normalize(database) !== path.join(projectRoot, 'database', 'playwright.sqlite')) {
        throw new Error('Playwright may only reset database/playwright.sqlite.');
    }

    execFileSync(
        php,
        [
            'artisan',
            'migrate:fresh',
            '--seed',
            '--seeder=PlaywrightTestUserSeeder',
            '--force',
            '--no-interaction',
        ],
        {
            cwd: projectRoot,
            env: {
                ...process.env,
                APP_ENV: 'testing',
                APP_URL: 'http://127.0.0.1:8000',
                DB_CONNECTION: 'sqlite',
                DB_DATABASE: database,
                SESSION_DRIVER: 'file',
                CACHE_STORE: 'array',
                QUEUE_CONNECTION: 'sync',
                MAIL_MAILER: 'array',
            },
            stdio: 'inherit',
        }
    );
}
