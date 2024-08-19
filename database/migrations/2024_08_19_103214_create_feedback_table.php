<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('email_address')->nullable();
            $table->string('experiment');
            $table->longText('feedback');
            $table->string('php_version')->nullable();
            $table->string('vanguard_version')->nullable();
            $table->timestamps();
        });
    }
};
