<?php

namespace Database\Seeders;

use App\Models\Cct;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CctSeeder extends Seeder
{
    public function run()
    {
        Cct::updateOrCreate(
            ['CLAVECCT' => '31ABJ0001X'],
            [
                'NOMBRECT' => 'OFICINA DE REPRESENTACION DE LA CNBBBJ EN EL ESTADO DE YUCATAN',
                'TURNO' => 400,
                'NIVEL' => 99,
                'N_NIVEL' => 'NO DOCENTE',
                'TIPO' => 'ADMINISTRATIVO',
                'STATUS' => 1,
                'LOCALIDAD' => '0001',
                'N_LOCALIDAD' => 'MÉRIDA',
                'MUNICIPIO' => '050',
                'N_MUNICIPIO' => 'MÉRIDA',
                'REGIONT' => 'MERIDA',
                'CCT_INMUEBLE' => '31INMBA81C',
                'DOMICILIO' => 'CALLE 19',
                'ENTRECALLE' => 'CALLE 24',
                'YCALLE' => 'CALLE 22',
                'CALLEPOST' => 'CALLE 17',
                'NUMEXT' => '109',
                'ALFANUMEXT' => 'NA',
                'NUMINT' => '0',
                'ALFANUMINT' => 'NA',
                'COLONIA' => '0595',
                'ASENTAMIEN' => 'MEXICO',
                'CODPOST' => '97125',
                'LONGITUD' => -89.611649,
                'LATITUD' => 21.003466,
                'DIRECTOR' => 'RODOLFO HECTOR HUGO',
                'APELLIDO1' => 'ARROYO',
                'APELLIDO2' => 'DEL MURO',
                'CURP' => 'AOMR690831HDFRRD07',
                'RFC' => 'AOMR690831247',
                'TELEFONO' => '5554820700',
                'TELEXTEN' => '84102',
                'CELULAR1' => '9999266369',
                'CORREOELE' => 'ce_yucatan@becasbenitojuarez.gob.mx',
                'SOSTENIMIE' => 'FEDERAL',
                'SERVICIO' => 'REPRESENTAR A LA COORDINACIÓN NACIONAL DE BECAS PARA EL BIENESTAR BENITO JUÁREZ',
                'FECHAFUNDA' => '5-319-20',
                'FECHAALTA' => Carbon::createFromFormat('Ymd', '20190531')->format('Y-m-d'),
                'FECHAACTUA' => Carbon::createFromFormat('Ymd', '20190531')->format('Y-m-d'),
            ]
        );
    }
}
