<?php

namespace App\Http\Controllers;

use App\AfpDocument;
use App\Status;
use App\Afp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AfpDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validacion
        $validated = Validator::make($request->all(), [
            'afp_id' => ['required', 'integer'],
        ]);

        if ( $validated->fails() ) {
            $data = array(
                'message' => $validated->errors(),
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // Input
        $status_id = $request->get('status_id', 1);
        $afp_id = $request->get('afp_id');

        // Objeto Status
        $status = Status::find($status_id);
        $afp = Afp::find($afp_id);

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

        if ( !$afp ) {
            $data = array(
                'message' => [
                    'afp_id' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // AfpDocument por Status
        $afp_document = AfpDocument::where('status_id', $status->id)->where('afp_id', $afp->id)->get();

        return response()->json($afp_document->load('status')->load('afp'), 202);
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
        $validated = Validator::make($request->all(), [
            'document_type' => ['required', 'string', 'max:100'],
            'id_solicitud' => ['required', 'string', 'max:15'],
            'afp_id' => ['required', 'integer'],
            'rut' => ['string', 'max:100'],
            'folio' => ['required', 'string', 'max:100'],
            'workitemid' => ['required', 'unique:afp_documents', 'string', 'max:100'],
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
        $afp_id = $request->get('afp_id');
        $rut = $request->get('rut');
        $folio = $request->get('folio');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        // Objeto Afp
        $afp = Afp::find($afp_id);

        if ( !$afp ) {
            $data = array(
                'message' => [
                    'afp_id' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        if ( !$document_type or Str::lower($document_type) != 'certificado afp' ) {
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

        $afp_document = new AfpDocument();
        $afp_document->document_type = ucwords($document_type);
        $afp_document->id_solicitud = $id_solicitud;
        $afp_document->afp_id = $afp_id;
        $afp_document->rut = $rut;
        $afp_document->folio = $folio;
        $afp_document->workitemid = $workitemid;
        $afp_document->validation = $validation;

        $afp_document->save();

        return response()->json([
            'certificado_afp' => $afp_document,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AfpDocument  $afpDocument
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto AfpDocument
        $afp_documents = AfpDocument::where('id_solicitud', $id_solicitud)->get();

        if ( $afp_documents->count() == 0 ) {
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
            'certificados_afps' => $afp_documents->load('status')->load('afp')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AfpDocument  $afpDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(AfpDocument $afpDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AfpDocument  $afpDocument
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

        // Objeto AfpDocument
        $afp_document = AfpDocument::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $afp_document->count() == 0 ) {
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

        $afp_document = $afp_document[0];

        if ( $status_id != '' ) {
            $afp_document->status_id = $status_id;
        }

        $afp_document->validation = $validation;

        $afp_document->update();

        return response()->json([
            'certificado_afp' => $afp_document->load('status')->load('afp'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AfpDocument  $afpDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(AfpDocument $afpDocument)
    {
        //
    }
}
