// database/migrations/xxxx_xx_xx_create_consumables_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Consumables Categories Table
        Schema::create('consumable_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Consumables Table
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('category_id')->constrained('consumable_categories');
            $table->text('description')->nullable();
            $table->string('unit')->default('pieces'); // pieces, liters, meters, etc.
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Inventory Table (Stock Levels)
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained('consumables');
            $table->integer('quantity')->default(0);
            $table->string('location')->nullable(); // warehouse, store, site
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Stock Requests Table
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // requester
            $table->string('project_type')->nullable(); // borehole, piped_scheme, irrigation, dam
            $table->string('project_name')->nullable();
            $table->string('village')->nullable();
            $table->string('ward')->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'partially_fulfilled', 'fulfilled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Stock Request Items Table
        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_request_id')->constrained('stock_requests')->onDelete('cascade');
            $table->foreignId('consumable_id')->constrained('consumables');
            $table->integer('requested_quantity');
            $table->integer('approved_quantity')->default(0);
            $table->integer('issued_quantity')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Usage Reports Table
        Schema::create('usage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // reporter
            $table->string('project_type'); // borehole, piped_scheme, irrigation, dam
            $table->string('project_name');
            $table->string('village');
            $table->string('ward');
            $table->text('work_description');
            $table->enum('maintenance_type', ['repair', 'maintenance', 'rehabilitation', 'installation']);
            $table->date('date_of_work');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Usage Report Items Table
        Schema::create('usage_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usage_report_id')->constrained('usage_reports')->onDelete('cascade');
            $table->foreignId('consumable_id')->constrained('consumables');
            $table->integer('quantity_used');
            $table->string('batch_number')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamps();
        });

        // Purchase Orders Table
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users'); // created by
            $table->enum('status', ['draft', 'sent', 'confirmed', 'delivered', 'cancelled'])->default('draft');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Purchase Order Items Table
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('consumable_id')->constrained('consumables');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('usage_report_items');
        Schema::dropIfExists('usage_reports');
        Schema::dropIfExists('stock_request_items');
        Schema::dropIfExists('stock_requests');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('consumables');
        Schema::dropIfExists('consumable_categories');
        Schema::dropIfExists('suppliers');
    }
};