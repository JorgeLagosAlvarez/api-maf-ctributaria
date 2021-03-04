<?php

namespace App\Http\Controllers;

use App\SituacionTributaria;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SituacionTributariaController extends Controller
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

        // Objeto status
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

        // SituacionTributaria por status
        $situacion_tributaria = SituacionTributaria::where('status_id', $status->id)->get();

        return response()->json($situacion_tributaria->load('status'), 202);
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
            'rut_contribuyente' => ['required', 'string', 'max:100'],
            'workitemid' => ['required', 'unique:situacion_tributarias', 'string', 'max:100'],
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
        $rut_contribuyente = Str::of($request->get('rut_contribuyente'))->replace(' ', '')->replace('.', '');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'situacion tributaria' ) {
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

        $situacion_tributaria = new SituacionTributaria();
        $situacion_tributaria->document_type = ucwords($document_type);
        $situacion_tributaria->id_solicitud = $id_solicitud;
        $situacion_tributaria->rut_contribuyente = $rut_contribuyente;
        $situacion_tributaria->workitemid = $workitemid;
        $situacion_tributaria->validation = $validation;
        
        $situacion_tributaria->save();

        return response()->json([
            'situacion_tributaria' => $situacion_tributaria,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SituacionTributaria  $situacionTributaria
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto HonoraryTicket
        $situacion_tributaria = SituacionTributaria::where('id_solicitud', $id_solicitud)->get();

        if ( $situacion_tributaria->count() == 0 ) {
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
            'situacion_tributaria' => $situacion_tributaria->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SituacionTributaria  $situacionTributaria
     * @return \Illuminate\Http\Response
     */
    public function edit(SituacionTributaria $situacionTributaria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SituacionTributaria  $situacionTributaria
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

        // Objeto SituacionTributaria
        $situacion_tributaria = SituacionTributaria::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();        
        
        if ( $situacion_tributaria->count() == 0 ) {
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

        $situacion_tributaria = $situacion_tributaria[0];

        if ( $status_id != '' ) {
            $situacion_tributaria->status_id = $status_id;
        }

        $situacion_tributaria->validation = $validation;

        $situacion_tributaria->update();

        return response()->json([
            'situacion_tributaria' => $situacion_tributaria->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SituacionTributaria  $situacionTributaria
     * @return \Illuminate\Http\Response
     */
    public function destroy(SituacionTributaria $situacionTributaria)
    {
        //
    }
}
