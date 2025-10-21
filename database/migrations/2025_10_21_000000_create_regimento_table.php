<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('regimento'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->string('titulo')->default('Regimento Escolar');
            $table->string('arquivo')->nullable(); // caminho do PDF no storage
            $table->timestamps();

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('regimento'));
    }
};
