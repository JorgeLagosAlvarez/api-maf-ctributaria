<?php

namespace App\Http\Controllers;

use App\LiquidacionPension;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LiquidacionPensionController extends Controller
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

       // LiquidacionPension por status
       $liquidacion_pensions = LiquidacionPension::where('status_id', $status->id)->get();

       return response()->json($liquidacion_pensions->load('status'), 202);
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
            'codigo_validacion' => ['required', 'string', 'max:50'],
            'nombre_cliente' => ['string', 'max:200'],
            'rut_cliente' => ['string', 'max:10'],
            'periodo' => ['string', 'max:8'],
            'subtotal_haberes' => ['numeric', 'digits_between:1,8'],
            'subtotal_descuentos' => ['numeric', 'digits_between:1,8'],
            'total_neto' => ['numeric', 'digits_between:1,8'],
            'id_solicitud' => ['required', 'string', 'max:15'],
            'workitemid' => ['required', 'unique:liquidacion_pensions', 'string', 'max:100'],
            'validation' => ['bool', 'max:50'],
            'mes' => ['required', 'string', 'max:20'],
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
        $codigo_validacion = $request->get('codigo_validacion');
        $nombre_cliente = $request->get('nombre_cliente', null);
        $rut_cliente = $request->get('rut_cliente', null);
        $periodo = $request->get('periodo', null);
        $subtotal_haberes = $request->get('subtotal_haberes', null);
        $subtotal_descuentos = $request->get('subtotal_descuentos', null);
        $total_neto = $request->get('total_neto', null);
        $id_solicitud = $request->get('id_solicitud');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);
        $mes = $request->get('mes');

        if ( !$document_type or Str::lower($document_type) != 'liquidacion pension' ) {
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

        $liquidacion_pension = new LiquidacionPension();
        $liquidacion_pension->document_type = ucwords($document_type);
        $liquidacion_pension->codigo_validacion = $codigo_validacion;
        $liquidacion_pension->nombre_cliente = $nombre_cliente;
        $liquidacion_pension->rut_cliente = $rut_cliente;
        $liquidacion_pension->periodo = $periodo;
        $liquidacion_pension->subtotal_haberes = $subtotal_haberes;
        $liquidacion_pension->subtotal_descuentos = $subtotal_descuentos;
        $liquidacion_pension->total_neto = $total_neto;
        $liquidacion_pension->id_solicitud = $id_solicitud;
        $liquidacion_pension->workitemid = $workitemid;
        $liquidacion_pension->validation = $validation;
        $liquidacion_pension->mes = $mes;

        $liquidacion_pension->save();

        return response()->json([
            'liquidacion_pension' => $liquidacion_pension,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LiquidacionPension  $liquidacionPension
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto LiquidacionPension
        $liquidacion_pension = LiquidacionPension::where('id_solicitud', $id_solicitud)->get();

        if ( $liquidacion_pension->count() == 0 ) {
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
            'liquidacion_pension' => $liquidacion_pension->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LiquidacionPension  $liquidacionPension
     * @return \Illuminate\Http\Response
     */
    public function edit(LiquidacionPension $liquidacionPension)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LiquidacionPension  $liquidacionPension
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

        // Objeto LiquidacionPension
        $liquidacion_pension = LiquidacionPension::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $liquidacion_pension->count() == 0 ) {
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

        $liquidacion_pension = $liquidacion_pension[0];

        if ( $status_id != '' ) {
            $liquidacion_pension->status_id = $status_id;
        }

        $liquidacion_pension->validation = $validation;

        $liquidacion_pension->update();

        return response()->json([
            'liquidacion_pension' => $liquidacion_pension->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LiquidacionPension  $liquidacionPension
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiquidacionPension $liquidacionPension)
    {
        //
    }
}
