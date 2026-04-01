<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CctRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->has('CLAVECCT')) {
            $this->merge([
                'CLAVECCT' => strtoupper(trim((string) $this->input('CLAVECCT'))),
            ]);
        }
    }

    public function rules()
    {
        $cct = $this->route('cct');
        $cctId = $cct ? $cct->id : null;

        $short = ['nullable', 'string', 'max:20'];
        $medium = ['nullable', 'string', 'max:100'];
        $long = ['nullable', 'string', 'max:255'];

        return [
            'CLAVECCT' => ['required', 'string', 'max:20', Rule::unique('ccts', 'CLAVECCT')->ignore($cctId)],
            'NOMBRECT' => ['required', 'string', 'max:255'],
            'TURNO' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'NIVEL' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'N_NIVEL' => $medium,
            'TIPO' => $medium,
            'STATUS' => ['nullable', 'integer', 'min:0', 'max:9'],
            'ZONAESCOLA' => $short,
            'CCT_ZONA' => $short,
            'LOCALIDAD' => $short,
            'N_LOCALIDAD' => $long,
            'MUNICIPIO' => $short,
            'N_MUNICIPIO' => $long,
            'REGIONT' => $medium,
            'REGIONOP' => $medium,
            'CCT_SERREG' => $short,
            'CCT_INMUEBLE' => $short,
            'DOMICILIO' => $long,
            'ENTRECALLE' => $long,
            'YCALLE' => $long,
            'CALLEPOST' => $long,
            'NUMEXT' => $short,
            'ALFANUMEXT' => $short,
            'NUMINT' => $short,
            'ALFANUMINT' => $short,
            'COLONIA' => $long,
            'ASENTAMIEN' => $long,
            'CODPOST' => ['nullable', 'string', 'max:10'],
            'DES_UBIC' => ['nullable', 'string'],
            'LONGITUD' => ['nullable', 'numeric', 'between:-180,180'],
            'LATITUD' => ['nullable', 'numeric', 'between:-90,90'],
            'DIRECTOR' => $long,
            'APELLIDO1' => $long,
            'APELLIDO2' => $long,
            'CURP' => ['nullable', 'string', 'max:18'],
            'RFC' => ['nullable', 'string', 'max:20'],
            'TELEFONO' => ['nullable', 'string', 'max:20'],
            'TELEXTEN' => ['nullable', 'string', 'max:20'],
            'CELULAR1' => ['nullable', 'string', 'max:20'],
            'CORREOELE' => ['nullable', 'email', 'max:255'],
            'PAGINAWEB' => ['nullable', 'string', 'max:255'],
            'SOSTENIMIE' => $medium,
            'SERVICIO' => ['nullable', 'string'],
            'FECHAFUNDA' => ['nullable', 'string', 'max:20'],
            'FECHAALTA' => ['nullable', 'date'],
            'FECHACLAUS' => ['nullable', 'date'],
            'FECHAACTUA' => ['nullable', 'date'],
        ];
    }
}
