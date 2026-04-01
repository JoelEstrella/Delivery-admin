<?php

namespace App\Models;

use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ccts';

    protected $fillable = [
        'CLAVECCT',
        'NOMBRECT',
        'TURNO',
        'NIVEL',
        'N_NIVEL',
        'TIPO',
        'STATUS',
        'ZONAESCOLA',
        'CCT_ZONA',
        'LOCALIDAD',
        'N_LOCALIDAD',
        'MUNICIPIO',
        'N_MUNICIPIO',
        'REGIONT',
        'REGIONOP',
        'CCT_SERREG',
        'CCT_INMUEBLE',
        'DOMICILIO',
        'ENTRECALLE',
        'YCALLE',
        'CALLEPOST',
        'NUMEXT',
        'ALFANUMEXT',
        'NUMINT',
        'ALFANUMINT',
        'COLONIA',
        'ASENTAMIEN',
        'CODPOST',
        'DES_UBIC',
        'LONGITUD',
        'LATITUD',
        'DIRECTOR',
        'APELLIDO1',
        'APELLIDO2',
        'CURP',
        'RFC',
        'TELEFONO',
        'TELEXTEN',
        'CELULAR1',
        'CORREOELE',
        'PAGINAWEB',
        'SOSTENIMIE',
        'SERVICIO',
        'FECHAFUNDA',
        'FECHAALTA',
        'FECHACLAUS',
        'FECHAACTUA',
    ];

    protected $casts = [
        'STATUS' => 'integer',
        'TURNO' => 'integer',
        'NIVEL' => 'integer',
        'LONGITUD' => 'decimal:6',
        'LATITUD' => 'decimal:6',
        'FECHAALTA' => 'date',
        'FECHACLAUS' => 'date',
        'FECHAACTUA' => 'date',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
