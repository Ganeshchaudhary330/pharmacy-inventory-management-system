<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\StockTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ========== 1. Create Admin User ==========
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@pharmacy.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'phone'     => '9800000000',
            'is_active' => true,
        ]);

        // ========== 2. Create Categories ==========
        $categories = [
            ['name' => 'Antibiotics',       'description' => 'Medicines used to treat bacterial infections'],
            ['name' => 'Pain Relief',        'description' => 'Analgesics and pain management medicines'],
            ['name' => 'Cardiovascular',     'description' => 'Heart and blood pressure medications'],
            ['name' => 'Respiratory',        'description' => 'Asthma, allergy and breathing medications'],
            ['name' => 'Gastrointestinal',   'description' => 'Digestive system medications'],
            ['name' => 'Vitamins & Supplements', 'description' => 'Nutritional supplements and vitamins'],
            ['name' => 'Antidiabetics',      'description' => 'Diabetes management medications'],
            ['name' => 'Dermatological',     'description' => 'Skin care and treatment medications'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ========== 3. Create Suppliers ==========
        $suppliers = [
            ['name' => 'MediSupply Co.',    'email' => 'info@medisupply.com',    'phone' => '9810000001', 'contact_person' => 'Ram Sharma'],
            ['name' => 'PharmaDistributors', 'email' => 'info@pharmadist.com',   'phone' => '9810000002', 'contact_person' => 'Sita Devi'],
            ['name' => 'HealthCare Ltd.',   'email' => 'info@healthcareltd.com', 'phone' => '9810000003', 'contact_person' => 'Hari Prasad'],
            ['name' => 'Global Meds',       'email' => 'info@globalmeds.com',    'phone' => '9810000004', 'contact_person' => 'Krishna Thapa'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        // ========== 4. Create Medicines ==========
        $medicines = [
            ['name' => 'Amoxicillin 500mg',     'generic_name' => 'Amoxicillin',         'category_id' => 1, 'supplier_id' => 1, 'batch_number' => 'AMX-001', 'sku' => 'AMX500', 'purchase_price' => 50, 'selling_price' => 80,  'stock_quantity' => 200, 'low_stock_threshold' => 20,  'expiry_date' => '2027-06-15', 'manufacturer' => 'Cipla'],
            ['name' => 'Paracetamol 500mg',    'generic_name' => 'Paracetamol',         'category_id' => 2, 'supplier_id' => 1, 'batch_number' => 'PAR-001', 'sku' => 'PAR500', 'purchase_price' => 10, 'selling_price' => 20,  'stock_quantity' => 500, 'low_stock_threshold' => 50,  'expiry_date' => '2027-08-20', 'manufacturer' => 'GSK'],
            ['name' => 'Ibuprofen 400mg',      'generic_name' => 'Ibuprofen',           'category_id' => 2, 'supplier_id' => 2, 'batch_number' => 'IBU-001', 'sku' => 'IBU400', 'purchase_price' => 25, 'selling_price' => 45,  'stock_quantity' => 300, 'low_stock_threshold' => 30,  'expiry_date' => '2027-05-10', 'manufacturer' => 'Sun Pharma'],
            ['name' => 'Amlodipine 5mg',       'generic_name' => 'Amlodipine',          'category_id' => 3, 'supplier_id' => 2, 'batch_number' => 'AML-001', 'sku' => 'AML5',   'purchase_price' => 30, 'selling_price' => 55,  'stock_quantity' => 150, 'low_stock_threshold' => 20,  'expiry_date' => '2027-09-01', 'manufacturer' => 'Pfizer'],
            ['name' => 'Metformin 500mg',      'generic_name' => 'Metformin',           'category_id' => 7, 'supplier_id' => 3, 'batch_number' => 'MET-001', 'sku' => 'MET500', 'purchase_price' => 20, 'selling_price' => 35,  'stock_quantity' => 250, 'low_stock_threshold' => 30,  'expiry_date' => '2027-07-25', 'manufacturer' => 'Novartis'],
            ['name' => 'Omeprazole 20mg',      'generic_name' => 'Omeprazole',          'category_id' => 5, 'supplier_id' => 3, 'batch_number' => 'OME-001', 'sku' => 'OME20',  'purchase_price' => 15, 'selling_price' => 30,  'stock_quantity' => 180, 'low_stock_threshold' => 20,  'expiry_date' => '2027-04-18', 'manufacturer' => 'AstraZeneca'],
            ['name' => 'Vitamin C 500mg',      'generic_name' => 'Ascorbic Acid',       'category_id' => 6, 'supplier_id' => 4, 'batch_number' => 'VITC-01', 'sku' => 'VITC500','purchase_price' => 40, 'selling_price' => 70,  'stock_quantity' => 400, 'low_stock_threshold' => 50,  'expiry_date' => '2027-11-30', 'manufacturer' => 'Abbott'],
            ['name' => 'Cetirizine 10mg',      'generic_name' => 'Cetirizine',          'category_id' => 4, 'supplier_id' => 4, 'batch_number' => 'CET-001', 'sku' => 'CET10',  'purchase_price' => 8,  'selling_price' => 15,  'stock_quantity' => 350, 'low_stock_threshold' => 40,  'expiry_date' => '2027-10-05', 'manufacturer' => 'Dr. Reddy\'s'],
            ['name' => 'Azithromycin 250mg',   'generic_name' => 'Azithromycin',        'category_id' => 1, 'supplier_id' => 1, 'batch_number' => 'AZI-001', 'sku' => 'AZI250', 'purchase_price' => 60, 'selling_price' => 100, 'stock_quantity' => 5,   'low_stock_threshold' => 20,  'expiry_date' => '2027-03-12', 'manufacturer' => 'Cipla'],
            ['name' => 'Losartan 50mg',        'generic_name' => 'Losartan',            'category_id' => 3, 'supplier_id' => 2, 'batch_number' => 'LOS-001', 'sku' => 'LOS50',  'purchase_price' => 35, 'selling_price' => 60,  'stock_quantity' => 0,   'low_stock_threshold' => 15,  'expiry_date' => '2026-12-20', 'manufacturer' => 'Merck'],
        ];

        foreach ($medicines as $med) {
            Medicine::create($med);
        }

        // ========== 5. Create Sample Stock Transactions ==========
        $user = User::first();

        StockTransaction::create([
            'medicine_id' => 1, 'user_id' => $user->id, 'type' => 'in', 'quantity' => 200,
            'unit_price' => 50, 'total_price' => 10000, 'reference_type' => 'purchase', 'notes' => 'Initial stock'
        ]);
        StockTransaction::create([
            'medicine_id' => 2, 'user_id' => $user->id, 'type' => 'in', 'quantity' => 500,
            'unit_price' => 10, 'total_price' => 5000, 'reference_type' => 'purchase', 'notes' => 'Initial stock'
        ]);
        StockTransaction::create([
            'medicine_id' => 9, 'user_id' => $user->id, 'type' => 'in', 'quantity' => 50,
            'unit_price' => 60, 'total_price' => 3000, 'reference_type' => 'purchase', 'notes' => 'Initial stock'
        ]);

        // ========== 6. Create Sample Sale ==========
        $sale = Sale::create([
            'invoice_number' => Sale::generateInvoiceNumber(),
            'user_id'        => $user->id,
            'customer_name'  => 'John Doe',
            'customer_phone' => '9841000000',
            'subtotal'       => 180,
            'tax'            => 0,
            'discount'       => 10,
            'total'          => 170,
            'amount_paid'    => 200,
            'change_amount'  => 30,
            'payment_method' => 'cash',
            'status'         => 'completed',
            'notes'          => 'Sample sale',
        ]);

        SaleItem::create(['sale_id' => $sale->id, 'medicine_id' => 2, 'quantity' => 5, 'unit_price' => 20, 'total_price' => 100]);
        SaleItem::create(['sale_id' => $sale->id, 'medicine_id' => 3, 'quantity' => 2, 'unit_price' => 40, 'total_price' => 80]);

        // Update stock for the sale
        Medicine::find(2)->decrement('stock_quantity', 5);
        Medicine::find(3)->decrement('stock_quantity', 2);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin login: admin@pharmacy.com / password');
    }
}
