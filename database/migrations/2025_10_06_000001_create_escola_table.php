<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('escola'), function (Blueprint $table) {
            $table->id();
            $table->string('inep', 20)->nullable()->unique();
            $table->string('cnpj', 20)->nullable()->unique();
            $table->string('nome_e', 150);
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->timestamp('criado_em')->useCurrent()->nullable();
            $table->unsignedBigInteger('secretaria_id')->nullable();
            $table->boolean('is_master')->default(0);

            $table->foreign('secretaria_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('escola'));
    }
};
