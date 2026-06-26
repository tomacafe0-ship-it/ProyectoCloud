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
        Schema::create('movimientos_inventario', function (Blueprint $table) {

            $table->id();

            // producto relacionado
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // entrada / salida / ajuste
            $table->enum(
                'tipo_movimiento',
                [
                    'entrada',
                    'salida',
                    'ajuste'
                ]
            );

            // cantidad movida
            $table->integer('cantidad');

            // stock antes del cambio
            $table->integer('stock_anterior');

            // stock después del cambio
            $table->integer('stock_nuevo');

            // motivo opcional
            $table->string('motivo')
                ->nullable();

            // referencia opcional (ej: order_id)
            $table->unsignedBigInteger(
                'referencia_id'
            )->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'movimientos_inventario'
        );
    }
};