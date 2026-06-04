<?php

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->company = $user->companies()->first();

    $this->withHeaders([
        'company' => $this->company->id,
    ]);

    Sanctum::actingAs($user, ['*']);
});

test('customers csv export returns downloadable csv', function () {
    Customer::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Export Test Customer',
    ]);

    $response = get('api/v1/customers/export');

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('content-disposition'))->toContain('customers-');
    expect($response->streamedContent())->toContain('Export Test Customer');
});

test('items csv export returns downloadable csv', function () {
    Item::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Export Test Item',
    ]);

    $response = get('api/v1/items/export');

    $response->assertOk();
    expect($response->streamedContent())->toContain('Export Test Item');
});

test('invoices csv export returns downloadable csv', function () {
    $customer = Customer::factory()->create([
        'company_id' => $this->company->id,
    ]);

    Invoice::factory()->create([
        'company_id' => $this->company->id,
        'customer_id' => $customer->id,
        'invoice_number' => 'EXP-INV-001',
    ]);

    $response = get('api/v1/invoices/export');

    $response->assertOk();
    expect($response->streamedContent())->toContain('EXP-INV-001');
});

test('invoices csv export includes invoice lines as json', function () {
    $customer = Customer::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'customer_id' => $customer->id,
        'invoice_number' => 'EXP-INV-LINES',
    ]);

    InvoiceItem::factory()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $invoice->id,
        'name' => 'First Line Item',
        'quantity' => 1,
    ]);

    InvoiceItem::factory()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $invoice->id,
        'name' => 'Second Line Item',
        'quantity' => 2,
    ]);

    $response = get('api/v1/invoices/export');

    $response->assertOk();

    $content = $response->streamedContent();

    expect($content)->toContain('items');
    expect($content)->toContain('First Line Item');
    expect($content)->toContain('Second Line Item');
    expect(substr_count($content, 'EXP-INV-LINES'))->toBe(1);

    $lines = array_values(array_filter(str_getcsv($content, "\n")));

    $dataRow = str_getcsv($lines[1]);
    $items = json_decode(end($dataRow), true, flags: JSON_THROW_ON_ERROR);

    expect($items)->toHaveCount(2);
    expect($items[0]['name'])->toBe('First Line Item');
    expect($items[1]['name'])->toBe('Second Line Item');

    $header = str_getcsv($lines[0]);
    expect($dataRow[array_search('customer_id', $header, true)])->toBe((string) $customer->id);
});

test('invoices csv line export returns one row per line', function () {
    $customer = Customer::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'customer_id' => $customer->id,
        'invoice_number' => 'EXP-INV-FLAT',
    ]);

    InvoiceItem::factory()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $invoice->id,
        'name' => 'Flat Line A',
        'quantity' => 1,
    ]);

    InvoiceItem::factory()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $invoice->id,
        'name' => 'Flat Line B',
        'quantity' => 1,
    ]);

    $response = get('api/v1/invoices/export?format=lines');

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('invoice-lines-');

    $rows = array_values(array_filter(str_getcsv($response->streamedContent(), "\n")));

    expect($rows)->toHaveCount(3);
    expect($rows[1])->toContain('Flat Line A');
    expect($rows[2])->toContain('Flat Line B');
});

test('estimates csv export returns downloadable csv', function () {
    $customer = Customer::factory()->create([
        'company_id' => $this->company->id,
    ]);

    Estimate::factory()->create([
        'company_id' => $this->company->id,
        'customer_id' => $customer->id,
        'estimate_number' => 'EXP-EST-001',
    ]);

    $response = get('api/v1/estimates/export');

    $response->assertOk();
    expect($response->streamedContent())->toContain('EXP-EST-001');
});

test('estimates csv export includes estimate lines as json', function () {
    $customer = Customer::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $estimate = Estimate::factory()->create([
        'company_id' => $this->company->id,
        'customer_id' => $customer->id,
        'estimate_number' => 'EXP-EST-LINES',
    ]);

    EstimateItem::factory()->create([
        'company_id' => $this->company->id,
        'estimate_id' => $estimate->id,
        'name' => 'First Estimate Line',
        'quantity' => 1,
    ]);

    EstimateItem::factory()->create([
        'company_id' => $this->company->id,
        'estimate_id' => $estimate->id,
        'name' => 'Second Estimate Line',
        'quantity' => 3,
    ]);

    $response = get('api/v1/estimates/export');

    $response->assertOk();

    $content = $response->streamedContent();

    expect($content)->toContain('items');
    expect($content)->toContain('First Estimate Line');
    expect($content)->toContain('Second Estimate Line');
    expect(substr_count($content, 'EXP-EST-LINES'))->toBe(1);

    $lines = array_values(array_filter(str_getcsv($content, "\n")));
    $dataRow = str_getcsv($lines[1]);
    $items = json_decode(end($dataRow), true, flags: JSON_THROW_ON_ERROR);

    expect($items)->toHaveCount(2);
    expect($items[0]['name'])->toBe('First Estimate Line');
    expect($items[1]['name'])->toBe('Second Estimate Line');

    $header = str_getcsv($lines[0]);
    expect($dataRow[array_search('customer_id', $header, true)])->toBe((string) $customer->id);
});

test('expenses csv export returns downloadable csv', function () {
    Expense::factory()->create([
        'company_id' => $this->company->id,
        'notes' => 'Export Test Expense Note',
    ]);

    $response = get('api/v1/expenses/export');

    $response->assertOk();
    expect($response->streamedContent())->toContain('Export Test Expense Note');
});

test('customers csv export respects list filters', function () {
    Customer::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Filtered Customer',
        'phone' => '1111111111',
    ]);

    Customer::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Other Customer',
        'phone' => '2222222222',
    ]);

    $response = get('api/v1/customers/export?phone=1111111111');

    $response->assertOk();
    expect($response->streamedContent())->toContain('Filtered Customer');
    expect($response->streamedContent())->not->toContain('Other Customer');
});
