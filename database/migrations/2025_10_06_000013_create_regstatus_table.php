<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('regstatus'), function (Blueprint $table) {
            $table->id();
            $table->string('descr_s', 50);
            $table->timestamps(); // ✅ adiciona created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('regstatus'));
    }
};
