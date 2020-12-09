<?php

namespace App\Http\Controllers;

use App\Document;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

        $documents = Document::where('status_id', $status->id)->get();

        return response()->json($documents->load('status'), 202);
    }

    public function showb64($file_name)
    {
        $is_file = Storage::disk('ctributarias')->has($file_name);
        if (!$is_file) {
            $data = array(
                'message' => [
                    'fileb64' => [
                        'El archivo que estas buscando no existe en localstorage.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        $file = Storage::disk('ctributarias')->get($file_name);
        $fileb64 = base64_encode($file);

        return response()->json([
            'fileb64' => $fileb64
        ], 202);
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
        //Validacion
        $validated = Validator::make($request->all(), [
            'document_type' => ['required', 'string', 'max:100'],
            'id_solicitud' => ['required', 'string', 'max:15'],
            'file' => ['required'],
            'workitemid' => ['required', 'unique:documents', 'string', 'max:100'],
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
        $id_solicitud = $request->get('id_solicitud');
        $data = $request->get('file');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'carpeta tributaria' ) {
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
        
        // B64
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $file = base64_decode($data);
        $typeFile = explode(':', $type);
        $extension = explode('/', $typeFile[1]);
        $ext = $extension[1];

        // File Name
        $file_name = Str::upper('Carpeta Tributaria') . '_' . Carbon::now()->format('Ymd_His_v') . '.' . $ext;
        $file_name = str_replace(' ', '-', $file_name);

        $storage = Storage::disk('ctributarias')->put($file_name, $file);

        if (!$storage) {
            $data = array(
                'message' => [
                    'file' => [
                        'No se ha podido almecenar documento en localstorage.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        $document = new Document();
        $document->document_type = $document_type;
        $document->id_solicitud = $id_solicitud;
        $document->file_name = $file_name;
        $document->ext = $ext;
        $document->workitemid = $workitemid;        
        $document->validation = $validation;

        $document->save();

        return response()->json([
            'document' => $document,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        return response()->json($document->load('status'), 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
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

        if ( $status_id != '' ) {
            $document->status_id = $status_id;
        }

        $document->validation = $validation;

        // Eliminar archivo
        Storage::disk('ctributarias')->delete($document->file_name);

        $document->update();

        return response()->json([
            'document' => $document->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        //
    }
}
