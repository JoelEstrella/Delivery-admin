<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCctsTable extends Migration
{
    public function up()
    {
        Schema::create('ccts', function (Blueprint $table) {
            $table->id();
            $table->string('CLAVECCT', 20)->unique();
            $table->string('NOMBRECT');
            $table->unsignedSmallInteger('TURNO')->nullable();
            $table->unsignedSmallInteger('NIVEL')->nullable();
            $table->string('N_NIVEL', 100)->nullable();
            $table->string('TIPO', 100)->nullable();
            $table->unsignedTinyInteger('STATUS')->default(1);
            $table->string('ZONAESCOLA', 20)->nullable();
            $table->string('CCT_ZONA', 20)->nullable();
            $table->string('LOCALIDAD', 20)->nullable();
            $table->string('N_LOCALIDAD')->nullable();
            $table->string('MUNICIPIO', 20)->nullable();
            $table->string('N_MUNICIPIO')->nullable();
            $table->string('REGIONT', 100)->nullable();
            $table->string('REGIONOP', 100)->nullable();
            $table->string('CCT_SERREG', 20)->nullable();
            $table->string('CCT_INMUEBLE', 20)->nullable();
            $table->string('DOMICILIO')->nullable();
            $table->string('ENTRECALLE')->nullable();
            $table->string('YCALLE')->nullable();
            $table->string('CALLEPOST')->nullable();
            $table->string('NUMEXT', 20)->nullable();
            $table->string('ALFANUMEXT', 20)->nullable();
            $table->string('NUMINT', 20)->nullable();
            $table->string('ALFANUMINT', 20)->nullable();
            $table->string('COLONIA')->nullable();
            $table->string('ASENTAMIEN')->nullable();
            $table->string('CODPOST', 10)->nullable();
            $table->text('DES_UBIC')->nullable();
            $table->decimal('LONGITUD', 10, 6)->nullable();
            $table->decimal('LATITUD', 10, 6)->nullable();
            $table->string('DIRECTOR')->nullable();
            $table->string('APELLIDO1')->nullable();
            $table->string('APELLIDO2')->nullable();
            $table->string('CURP', 18)->nullable()->index();
            $table->string('RFC', 20)->nullable()->index();
            $table->string('TELEFONO', 20)->nullable();
            $table->string('TELEXTEN', 20)->nullable();
            $table->string('CELULAR1', 20)->nullable();
            $table->string('CORREOELE')->nullable()->index();
            $table->string('PAGINAWEB')->nullable();
            $table->string('SOSTENIMIE', 100)->nullable();
            $table->text('SERVICIO')->nullable();
            $table->string('FECHAFUNDA', 20)->nullable();
            $table->date('FECHAALTA')->nullable();
            $table->date('FECHACLAUS')->nullable();
            $table->date('FECHAACTUA')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('NOMBRECT');
            $table->index('LOCALIDAD');
            $table->index('MUNICIPIO');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccts');
    }
}
