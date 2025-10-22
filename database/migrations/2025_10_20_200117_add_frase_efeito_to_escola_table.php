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
                // 💬 Frase de efeito / lema da escola
                $table->string('frase_efeito', 255)
                    ->nullable()
                    ->after('telefone')
                    ->comment('Frase de efeito exibida em cabeçalhos e relatórios');;
            }

            if (!Schema::hasColumn(prefix('escola'), 'logo_path')) {
                // 🖼️ Caminho da logo da escola (armazenado em storage/app/public)
                $table->string('logo_path', 255)
                    ->nullable()
                    ->after('frase_efeito')
                    ->comment('Caminho relativo para a logo da escola');
            }
        });
    }

    public function down()
    {
        Schema::table(prefix('escola'), function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'frase_efeito']);
        });
    }

}
