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
        Schema::create('role_positions', function (Blueprint $table) {
            $table->id();
            $table->string('position', 100)->unique(); // Nama posisi, misal "ADMINISTRATOR KOPERASI"
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete(); // Hubungkan ke tabel roles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_positions');
    }
};
