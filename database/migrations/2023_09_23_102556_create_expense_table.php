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
        Schema::create('expense', function (Blueprint $table) {
            $table->increments('id');
            $table->date('timestamp');
            $table->double('amount');
            $table->string('description', 300);
            $table->unsignedInteger('categoryID')->nullable();
            $table->unsignedInteger('bankAccountID');

            $table->foreign('categoryID')->references('id')->on('category')->onDelete("set null");
            $table->foreign('bankAccountID')->references('id')->on('bank_account')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense');
    }
};
