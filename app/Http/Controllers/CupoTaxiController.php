<?php

namespace App\Http\Controllers;

use App\CupoTaxi;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CupoTaxiController extends Controller
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

        // CupoTaxi por Status
        $cupo_taxis = CupoTaxi::where('status_id', $status->id)->get();

        return response()->json($cupo_taxis->load('status'), 202);
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
            'id_solicitud' => ['required', 'string', 'max:15'],
            'patente' => ['required', 'string', 'max:10'],
            'workitemid' => ['required', 'unique:cupo_taxis', 'string', 'max:100'],
            'validation' => ['bool', 'max:50'],
        ]);

        if ( $validated->fails() ) {
            $data = array(
                'message' => $validated->errors(),
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // Input
        $document_type = Str::lower($request->get('document_type'));
        $id_solicitud = $request->get('id_solicitud');
        $patente = $request->get('patente');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'cupo taxi' ) {
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

        $cupo_taxi = new CupoTaxi();
        $cupo_taxi->document_type = ucwords($document_type);
        $cupo_taxi->id_solicitud = $id_solicitud;
        $cupo_taxi->patente = Str::upper($patente);
        $cupo_taxi->workitemid = $workitemid;
        $cupo_taxi->validation = $validation;
        
        $cupo_taxi->save();

        return response()->json([
            'cupo_taxi' => $cupo_taxi,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CupoTaxi  $cupoTaxi
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto CupoTaxi
        $cupo_taxi = CupoTaxi::where('id_solicitud', $id_solicitud)->get();

        if ( $cupo_taxi->count() == 0 ) {
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
            'cupo_taxi' => $cupo_taxi->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CupoTaxi  $cupoTaxi
     * @return \Illuminate\Http\Response
     */
    public function edit(CupoTaxi $cupoTaxi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CupoTaxi  $cupoTaxi
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

        // Objeto CupoTaxi
        $cupo_taxi = CupoTaxi::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $cupo_taxi->count() == 0 ) {
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

        $cupo_taxi = $cupo_taxi[0];

        if ( $status_id != '' ) {
            $cupo_taxi->status_id = $status_id;
        }

        $cupo_taxi->validation = $validation;

        $cupo_taxi->update();

        return response()->json([
            'cupo_taxi' => $cupo_taxi->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CupoTaxi  $cupoTaxi
     * @return \Illuminate\Http\Response
     */
    public function destroy(CupoTaxi $cupoTaxi)
    {
        //
    }
}
