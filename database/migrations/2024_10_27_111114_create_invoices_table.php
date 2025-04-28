<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // Foreign key column
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade'); // Define foreign key
            $table->string('invoice_number'); // Invoice item name
            $table->enum('status',['paid','unpaid']);
            $table->dateTime('date_of_create'); // Date and time of creation'
            $table->dateTime('from'); // Date and time of creation
            $table->dateTime('to'); // Date and time of creation
            $table->text('notes')->nullable(); // Invoice item name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
