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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreignId('service_id')->references('id')->on('car_services')->onDelete('cascade');
            $table->foreignId('transaction_id')->references('id')->on('transaction_histories')->onDelete('cascade');
            $table->enum('status',['ongoing','completed','cancelled']);
            $table->boolean('is_paid')->default(0);
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
