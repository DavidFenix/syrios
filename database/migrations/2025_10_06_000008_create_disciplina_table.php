<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('disciplina'), function (Blueprint $table) {
            $table->id();
            $table->string('abr', 10);
            $table->string('descr_d', 100);
            $table->unsignedBigInteger('school_id');

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('disciplina'));
    }
};
