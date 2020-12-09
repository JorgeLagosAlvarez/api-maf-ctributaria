<?php

namespace App\Http\Controllers;

use App\Cav;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CavController extends Controller
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

        // Cav por Status
        $cavs = Cav::where('status_id', $status->id)->get();

        return response()->json($cavs->load('status'), 202);
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
            'folio' => ['required', 'string', 'max:100'],
            'codigo_verificacion' => ['required', 'string', 'max:100'],
            'workitemid' => ['required', 'unique:cavs', 'string', 'max:100'],
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
        $folio = $request->get('folio');
        $codigo_verificacion = $request->get('codigo_verificacion');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'cav' ) {
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

        $cav = new Cav();
        $cav->document_type = Str::upper($document_type);
        $cav->id_solicitud = $id_solicitud;
        $cav->folio = $folio;
        $cav->codigo_verificacion = $codigo_verificacion;
        $cav->workitemid = $workitemid;
        $cav->validation = $validation;
        
        $cav->save();

        return response()->json([
            'cav' => $cav,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cav  $cav
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto Cav
        $cav = Cav::where('id_solicitud', $id_solicitud)->get();

        if ( $cav->count() == 0 ) {
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
            'cav' => $cav->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cav  $cav
     * @return \Illuminate\Http\Response
     */
    public function edit(Cav $cav)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cav  $cav
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

        // Objeto Cav
        $cav = Cav::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $cav->count() == 0 ) {
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

        if ( !$status or $status_id == 1 ) {
            $data = array(
                'message' => [
                    'status_id' => [
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

        $cav = $cav[0];

        if ( $status_id != '' ) {
            $cav->status_id = $status_id;
        }

        $cav->validation = $validation;

        $cav->update();

        return response()->json([
            'cav' => $cav->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cav  $cav
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cav $cav)
    {
        //
    }
}
