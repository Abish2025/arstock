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
    // Creamos la tabla "categorias"
    Schema::create('categorias', function (Blueprint $table) {
        // Clave primaria autoincremental, pero con el nombre id_categoria
        // (en vez del "id" por defecto de Laravel, para respetar tu diagrama)
        $table->id('id_categoria');

        // Nombre de la categoría, máximo 30 caracteres (según el diagrama)
        $table->string('nombre', 30);

        // Crea automáticamente created_at y updated_at
        $table->timestamps();
    });
}

public function down(): void
{
    // Si se revierte la migración, borra la tabla
    Schema::dropIfExists('categorias');
}
};
