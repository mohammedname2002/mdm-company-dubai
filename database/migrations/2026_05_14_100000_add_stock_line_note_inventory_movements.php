<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0)->after('quantity');
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->string('line_note', 500)->nullable()->after('company_discount_percent');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('credit_note_id')->constrained('credit_notes')->cascadeOnDelete();
            $table->unsignedBigInteger('credit_note_item_id')->nullable();
            $table->string('movement_type', 40);
            $table->integer('quantity_paid_delta');
            $table->integer('quantity_free_delta')->default(0);
            $table->string('note', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn('line_note');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
