<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_change');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->enum('reason', ['sale', 'restock', 'adjustment']);
            $table->string('reference_type')->nullable(); // order, manual, bulk
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
};
