<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name', 100);
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Seed IDR sebagai default
        DB::table('currencies')->insert([
            'code'          => 'IDR',
            'name'          => 'Indonesian Rupiah',
            'symbol'        => 'Rp',
            'exchange_rate' => 1,
            'is_default'    => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
