<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('usuario_role'), function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('school_id');

            $table->primary(['usuario_id', 'role_id', 'school_id']);

            $table->foreign('usuario_id')
                ->references('id')
                ->on(prefix('usuario'))
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on(prefix('role'))
                ->onDelete('cascade');

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('usuario_role'));
    }
};
