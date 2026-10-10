import { expect, test } from '@playwright/test';

const users = {
    admin: { email: 'e2e-admin@example.test', password: 'Password123!', dashboard: '/admin/dashboard', navigation: ['Supplier Management', '/admin/suppliers'] },
    staff: { email: 'e2e-staff@example.test', password: 'Password123!', dashboard: '/staff/dashboard', navigation: ['Customer Orders', '/staff/orders'] },
    finance: { email: 'e2e-finance@example.test', password: 'Password123!', dashboard: '/finance/dashboard', navigation: ['Finance', '/finance/expenses'] },
};

async function login(page, role) {
    await page.goto('/login');
    await page.getByLabel('Email Address').fill(users[role].email);
    await page.getByLabel('Password').fill(users[role].password);
    await page.getByRole('button', { name: 'Login' }).click();
    await expect(page).toHaveURL(new RegExp(`${users[role].dashboard}$`));
}

test('each role reaches its own dashboard after signing in', async ({ page }) => {
    for (const role of Object.keys(users)) {
        await login(page, role);
        await expect(page.locator('body')).toContainText('Dashboard');
        await page.getByRole('link', { name: users[role].navigation[0] }).click();
        await expect(page).toHaveURL(new RegExp(`${users[role].navigation[1]}$`));
        await page.getByRole('button', { name: /logout/i }).click();
        await expect(page).toHaveURL(/\/login$/);
    }
});

test('staff cannot access admin or finance dashboards', async ({ page }) => {
    await login(page, 'staff');

    const adminResponse = await page.goto('/admin/dashboard');
    expect(adminResponse?.status()).toBe(403);

    const financeResponse = await page.goto('/finance/dashboard');
    expect(financeResponse?.status()).toBe(403);
});

test('staff and admin can trace a customer order while finance remains restricted', async ({ browser }) => {
    const staffPage = await browser.newPage();
    await login(staffPage, 'staff');

    await staffPage.goto('/staff/customers/create');
    await staffPage.getByLabel('Customer Name').fill('Playwright Order Customer');
    await staffPage.getByLabel('Phone Number').fill('0123456789');
    await staffPage.getByLabel('Email').fill('playwright-order@example.test');
    await staffPage.getByLabel('Address').fill('Playwright Customer Address');
    await staffPage.getByRole('button', { name: 'Create Customer' }).click();
    await expect(staffPage.locator('body')).toContainText('Customer added successfully.');

    await staffPage.goto('/staff/orders/create');
    await staffPage.getByLabel('Customer').selectOption({ label: 'Playwright Order Customer' });
    await staffPage.getByLabel('Order Date').fill('2026-10-10');
    await staffPage.locator('input[name$="[product_name]"]').first().fill('Playwright Product A');
    await staffPage.locator('input[name$="[quantity]"]').first().fill('2');
    await staffPage.locator('input[name$="[price]"]').first().fill('12.50');
    await staffPage.getByRole('button', { name: 'Add Item' }).click();
    await staffPage.locator('input[name$="[product_name]"]').nth(1).fill('Playwright Product B');
    await staffPage.locator('input[name$="[quantity]"]').nth(1).fill('1');
    await staffPage.locator('input[name$="[price]"]').nth(1).fill('25.00');
    await staffPage.getByRole('button', { name: 'Create Order' }).click();
    await expect(staffPage.locator('body')).toContainText('Customer order CO-001 created successfully.');

    await staffPage.getByRole('link', { name: 'View' }).click();
    await expect(staffPage.locator('body')).toContainText('Playwright Order Customer');
    await expect(staffPage.locator('body')).toContainText('Playwright Product A');
    await expect(staffPage.locator('body')).toContainText('Playwright Product B');
    await expect(staffPage.locator('body')).toContainText('RM 50.00');

    await staffPage.getByRole('link', { name: 'Back to Customer Orders' }).click();
    await staffPage.getByLabel('Search Order or Customer').fill('Playwright Order Customer');
    await staffPage.getByRole('button', { name: 'Filter' }).click();
    await expect(staffPage.locator('body')).toContainText('CO-001');
    await staffPage.getByRole('link', { name: 'Edit' }).click();
    await staffPage.getByLabel('Status').selectOption('production');
    await staffPage.getByRole('button', { name: 'Update Order' }).click();
    await expect(staffPage.locator('body')).toContainText('Customer order updated successfully.');
    await expect(staffPage.locator('body')).toContainText('Production');
    await staffPage.close();

    const adminPage = await browser.newPage();
    await login(adminPage, 'admin');
    await adminPage.goto('/staff/orders/1');
    await expect(adminPage.locator('body')).toContainText('Playwright Product A');
    await adminPage.close();

    const financePage = await browser.newPage();
    await login(financePage, 'finance');
    const response = await financePage.goto('/staff/orders/1');
    expect(response?.status()).toBe(403);
    await financePage.close();
});

test('admin can create, view, update, validate, and delete a supplier', async ({ page }) => {
    const supplierName = 'Playwright Test Supplier';
    const updatedSupplierName = 'Playwright Updated Supplier';

    await login(page, 'admin');
    await page.goto('/admin/suppliers');
    await page.getByRole('link', { name: 'Add New Supplier' }).click();

    await page.getByRole('button', { name: 'Create Supplier' }).click();
    await expect(page.locator('#supplier_name')).toBeFocused();
    await expect(page.locator('#supplier_name')).toHaveJSProperty('validationMessage', 'Please fill out this field.');

    await page.getByLabel('Supplier Name').fill(supplierName);
    await page.getByLabel('Phone Number').fill('0123456789');
    await page.getByLabel('Email').fill('playwright-supplier@example.test');
    await page.getByLabel('Address').fill('Playwright Test Address');
    await page.getByRole('button', { name: 'Create Supplier' }).click();
    await expect(page).toHaveURL(/\/admin\/suppliers$/);
    await expect(page.locator('body')).toContainText(supplierName);

    await page.getByRole('link', { name: 'Edit' }).click();
    await page.getByLabel('Supplier Name').fill(updatedSupplierName);
    await page.getByRole('button', { name: 'Update Supplier' }).click();
    await expect(page.locator('body')).toContainText(updatedSupplierName);

    page.once('dialog', (dialog) => dialog.accept());
    await page.getByRole('button', { name: 'Delete' }).click();
    await expect(page.locator('body')).not.toContainText(updatedSupplierName);
});

test('staff request is approved by finance and then admin', async ({ browser }) => {
    const staffPage = await browser.newPage();
    await login(staffPage, 'staff');
    await staffPage.goto('/staff/pr/create');
    await staffPage.locator('input[name="material_item"]').fill('Playwright Workflow Material');
    await staffPage.locator('input[name="quantity"]').fill('5');
    await staffPage.locator('input[name="estimated_cost"]').fill('125');
    await staffPage.getByRole('button', { name: 'Submit Purchase Request' }).click();
    await expect(staffPage.locator('body')).toContainText('Purchase Request PR-001 submitted successfully!');
    await staffPage.close();

    const financePage = await browser.newPage();
    await login(financePage, 'finance');
    await financePage.getByRole('link', { name: 'Review PR' }).click();
    financePage.once('dialog', (dialog) => dialog.accept());
    await financePage.getByRole('button', { name: 'Approve & Forward to Admin' }).click();
    await expect(financePage.locator('body')).toContainText('Purchase Request forwarded to Admin approval.');
    await expect(financePage.locator('body')).toContainText('No Purchase Requests awaiting Finance review.');
    await financePage.close();

    const adminPage = await browser.newPage();
    await login(adminPage, 'admin');
    await adminPage.goto('/admin/purchase-requests');
    await adminPage.getByRole('link', { name: 'Review' }).click();
    adminPage.once('dialog', (dialog) => dialog.accept());
    await adminPage.getByRole('button', { name: 'Final Approve' }).click();
    await expect(adminPage).toHaveURL(/\/admin\/dashboard$/);
    await adminPage.goto('/admin/purchase-requests');
    await expect(adminPage.locator('body')).toContainText('No purchase request waiting for approval.');
    await adminPage.close();
});

test('staff can manage suppliers including search, details, validation, and deletion', async ({ page }) => {
    const supplierName = 'Playwright Staff Supplier';
    const updatedSupplierName = 'Playwright Staff Supplier Updated';

    await login(page, 'staff');
    await page.getByRole('link', { name: 'Supplier Management' }).click();
    await expect(page).toHaveURL(/\/staff\/suppliers$/);
    await page.getByRole('link', { name: 'Add New Supplier' }).click();

    await page.getByRole('button', { name: 'Create Supplier' }).click();
    await expect(page.locator('#supplier_name')).toBeFocused();

    await page.getByLabel('Supplier Name').fill(supplierName);
    await page.getByLabel('Phone Number').fill('0112233445');
    await page.getByLabel('Email').fill('playwright-staff-supplier@example.test');
    await page.getByLabel('Address').fill('Playwright Staff Address');
    await page.getByRole('button', { name: 'Create Supplier' }).click();
    await expect(page.locator('body')).toContainText('Supplier added successfully.');

    await page.getByRole('link', { name: 'View' }).click();
    await expect(page.locator('body')).toContainText('playwright-staff-supplier@example.test');
    await page.getByRole('link', { name: 'Back to Suppliers' }).click();

    await page.getByLabel('Search suppliers').fill('0112233445');
    await page.getByRole('button', { name: 'Search' }).click();
    await expect(page.locator('body')).toContainText(supplierName);

    await page.getByRole('link', { name: 'Edit' }).click();
    await page.getByLabel('Supplier Name').fill(updatedSupplierName);
    await page.getByRole('button', { name: 'Update Supplier' }).click();
    await expect(page.locator('body')).toContainText(updatedSupplierName);

    await page.getByRole('link', { name: 'Add New Supplier' }).click();
    await page.getByLabel('Supplier Name').fill(updatedSupplierName);
    await page.getByRole('button', { name: 'Create Supplier' }).click();
    await expect(page.locator('body')).toContainText('The supplier name has already been taken.');

    await page.getByRole('link', { name: 'Cancel' }).click();
    page.once('dialog', (dialog) => dialog.accept());
    await page.getByRole('button', { name: 'Delete' }).click();
    await expect(page.locator('body')).toContainText('Supplier deleted successfully.');
});

test('finance cannot access staff supplier management directly', async ({ page }) => {
    await login(page, 'finance');

    const response = await page.goto('/staff/suppliers');
    expect(response?.status()).toBe(403);
});

test('staff cannot delete a supplier referenced by a purchase request', async ({ browser }) => {
    const adminPage = await browser.newPage();
    await login(adminPage, 'admin');
    await adminPage.goto('/admin/suppliers/create');
    await adminPage.getByLabel('Supplier Name').fill('Azalea Chemical Sdn Bhd');
    await adminPage.getByRole('button', { name: 'Create Supplier' }).click();
    await adminPage.close();

    const staffPage = await browser.newPage();
    await login(staffPage, 'staff');
    await staffPage.goto('/staff/pr/create');
    await staffPage.locator('input[name="material_item"]').fill('Referenced Supplier Material');
    await staffPage.locator('input[name="quantity"]').fill('2');
    await staffPage.locator('input[name="estimated_cost"]').fill('20');
    await staffPage.getByRole('button', { name: 'Submit Purchase Request' }).click();
    await staffPage.goto('/staff/suppliers');

    const supplierRow = staffPage.locator('tr').filter({ hasText: 'Azalea Chemical Sdn Bhd' });
    staffPage.once('dialog', (dialog) => dialog.accept());
    await supplierRow.getByRole('button', { name: 'Delete' }).click();
    await expect(staffPage.locator('body')).toContainText('Supplier cannot be deleted because it is referenced by existing purchase requests.');
    await expect(supplierRow).toContainText('Azalea Chemical Sdn Bhd');
    await staffPage.close();
});
