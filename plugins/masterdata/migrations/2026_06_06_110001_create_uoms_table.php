<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('symbol', 20);
            $table->string('category', 20)->default('piece');
            $table->timestamps();
        });

        // Seed data umum
        $now = now();
        DB::table('uoms')->insert([
            ['name' => 'Piece',     'symbol' => 'pcs',  'category' => 'piece',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Unit',      'symbol' => 'unit', 'category' => 'piece',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Box',       'symbol' => 'box',  'category' => 'piece',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kilogram',  'symbol' => 'kg',   'category' => 'weight', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gram',      'symbol' => 'g',    'category' => 'weight', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Liter',     'symbol' => 'L',    'category' => 'volume', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Milliliter','symbol' => 'mL',   'category' => 'volume', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Meter',     'symbol' => 'm',    'category' => 'length', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Centimeter','symbol' => 'cm',   'category' => 'length', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};
