<?php

namespace App\Http\Controllers;

use App\LiquidacionSueldo;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LiquidacionSueldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       // Input
       $status_id = $request->get('status_id', 1);

       // Objeto Status
       $status = Status::find($status_id);

       if ( !$status ) {
           $data = array(
               'message' => [
                   'status_id' => [
                       'El dato que intentas enviar no es el correcto.'
                   ]
               ],
               'type' => 'error'
           );

           return response()->json($data, 404);
       }

       // LiquidacionSueldo por Status
       $liquidacion_sueldos = LiquidacionSueldo::where('status_id', $status->id)->get();

       return response()->json($liquidacion_sueldos->load('status'), 202);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validacion
        $validated = Validator::make($request->all(), [
            'document_type' => ['required', 'string', 'max:100'],
            'afp' => ['required', 'string', 'max:50'],
            'mes' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'digits_between:4,4'],
            'impuesto' => ['required', 'numeric', 'digits_between:3,8'],
            'monto_bruto' => ['required', 'numeric', 'digits_between:3,8'],
            'apv' => ['numeric', 'digits_between:1,8'],
            'ajustes' => ['numeric', 'digits_between:1,8'],
            'prevision' => ['required', 'string', 'max:50'],
            'monto_salud_1' => ['required', 'numeric', 'digits_between:3,8'],
            'monto_salud_2' => ['numeric', 'digits_between:1,8'],
            'exento_seguro_cesantia' => ['required', 'string', 'max:2'],
            'seguro_cesantia' => ['numeric', 'digits_between:1,8'],
            'id_solicitud' => ['required', 'string', 'max:15'],
            'workitemid' => ['required', 'unique:liquidacion_sueldos', 'string', 'max:100'],
            'validation' => ['bool', 'max:50'],
        ]);

        if ($validated->fails()) {
            $data = array(
                'message' => $validated->errors(),
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // Input
        $document_type = Str::lower($request->get('document_type'));
        $afp = $request->get('afp');
        $mes = $request->get('mes');
        $anio = $request->get('anio');
        $impuesto = $request->get('impuesto');
        $monto_bruto = $request->get('monto_bruto');
        $apv = $request->get('apv', 0);
        $ajustes = $request->get('ajustes', 0);
        $prevision = $request->get('prevision');
        $monto_salud_1 = $request->get('monto_salud_1');
        $monto_salud_2 = $request->get('monto_salud_2', 0);
        $exento_seguro_cesantia = $request->get('exento_seguro_cesantia');
        $seguro_cesantia = $request->get('seguro_cesantia', 0);
        $id_solicitud = $request->get('id_solicitud');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'liquidacion sueldo' ) {
            $data = array(
                'message' => [
                    'document_type' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        $liquidacion_sueldo = new LiquidacionSueldo();
        $liquidacion_sueldo->document_type = ucwords($document_type);
        $liquidacion_sueldo->afp = Str::upper($afp);
        $liquidacion_sueldo->mes = Str::upper($mes);
        $liquidacion_sueldo->anio = (string) (int) str_replace('.', '', $anio);
        $liquidacion_sueldo->impuesto = (string) (int) str_replace('.', '', $impuesto);
        $liquidacion_sueldo->monto_bruto = (string) (int) str_replace('.', '', $monto_bruto);
        $liquidacion_sueldo->apv = (string) (int) str_replace('.', '', $apv);
        $liquidacion_sueldo->ajustes = (string) (int) str_replace('.', '', $ajustes);
        $liquidacion_sueldo->prevision = Str::upper($prevision);
        $liquidacion_sueldo->monto_salud_1 = (string) (int) str_replace('.', '', $monto_salud_1);
        $liquidacion_sueldo->monto_salud_2 = (string) (int) str_replace('.', '', $monto_salud_2);
        $liquidacion_sueldo->exento_seguro_cesantia = Str::upper($exento_seguro_cesantia);
        $liquidacion_sueldo->seguro_cesantia = (string) (int) str_replace('.', '', $seguro_cesantia);
        $liquidacion_sueldo->id_solicitud = $id_solicitud;
        $liquidacion_sueldo->workitemid = $workitemid;
        $liquidacion_sueldo->validation = $validation;

        $liquidacion_sueldo->save();

        return response()->json([
            'liquidacion_sueldo' => $liquidacion_sueldo,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LiquidacionSueldo  $liquidacionSueldo
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto LiquidacionPension
        $liquidacion_sueldo = LiquidacionSueldo::where('id_solicitud', $id_solicitud)->get();

        if ( $liquidacion_sueldo->count() == 0 ) {
            $data = array(
                'message' => [
                    'id_solicitud' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        return response()->json([
            'liquidacion_sueldo' => $liquidacion_sueldo->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LiquidacionSueldo  $liquidacionSueldo
     * @return \Illuminate\Http\Response
     */
    public function edit(LiquidacionSueldo $liquidacionSueldo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LiquidacionSueldo  $liquidacionSueldo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_solicitud, $workitemid)
    {
        // Validacion
        $validated = Validator::make($request->all(), [
            'status_id' => ['required', 'integer'],
            'validation' => ['required', 'bool', 'max:50'],
        ]);

        if ($validated->fails()) {
            $data = array(
                'message' => $validated->errors(),
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // Input
        $status_id = $request->get('status_id');
        $validation = $request->get('validation', false);

        // Objeto Status
        $status = Status::find($status_id);

        // Objeto LiquidacionSueldo
        $liquidacion_sueldo = LiquidacionSueldo::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $liquidacion_sueldo->count() == 0 ) {
            $data = array(
                'message' => [
                    'id_solicitud' => [
                        'El dato que intentas enviar no es el correcto.'
                    ],
                    'workitemid' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        if ( !$status or $status_id == 1 ) {
            $data = array(
                'message' => [
                    'status_id' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        $liquidacion_sueldo = $liquidacion_sueldo[0];

        if ( $status_id != '' ) {
            $liquidacion_sueldo->status_id = $status_id;
        }

        $liquidacion_sueldo->validation = $validation;

        $liquidacion_sueldo->update();

        return response()->json([
            'liquidacion_sueldo' => $liquidacion_sueldo->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LiquidacionSueldo  $liquidacionSueldo
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiquidacionSueldo $liquidacionSueldo)
    {
        //
    }
}
