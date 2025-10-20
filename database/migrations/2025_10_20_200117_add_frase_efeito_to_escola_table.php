<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFraseEfeitoToEscolaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(prefix('escola'), function (Blueprint $table) {
            if (!Schema::hasColumn(prefix('escola'), 'frase_efeito')) {
                $table->string('frase_efeito', 255)->nullable()->after('telefone');
            }
        });
    }

    public function down()
    {
        Schema::table(prefix('escola'), function (Blueprint $table) {
            $table->dropColumn('frase_efeito');
        });
    }

}
