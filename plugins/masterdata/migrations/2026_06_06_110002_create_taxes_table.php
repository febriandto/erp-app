<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->decimal('rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed pajak umum Indonesia
        $now = now();
        DB::table('taxes')->insert([
            ['name' => 'PPN 11%',  'code' => 'PPN11',  'rate' => 11.00, 'is_active' => true,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PPh 23',   'code' => 'PPH23',  'rate' => 2.00,  'is_active' => true,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tax Free', 'code' => 'EXEMPT',  'rate' => 0.00,  'is_active' => true,  'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
