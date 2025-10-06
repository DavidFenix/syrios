<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('notificacao'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('reg_id', 200);
            $table->unsignedBigInteger('school_id');

            $table->foreign('usuario_id')->references('id')->on(prefix('usuario'))->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on(prefix('escola'))->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('notificacao'));
    }
};
