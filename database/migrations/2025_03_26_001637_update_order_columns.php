<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->decimal('sub_total', 10, 2)->change();
            $table->decimal('vat', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
            $table->decimal('pay', 10, 2)->change();
            $table->decimal('due', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->integer('sub_total')->change();
            $table->integer('vat')->change();
            $table->integer('total')->change();
            $table->integer('pay')->change();
            $table->integer('due')->change();
        });
    }
};
