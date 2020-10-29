<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::where('status_id', 1)->get();

        return response()->json($documents, 202);
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
            'file' => ['required']
        ]);

        if ($validated->fails()) {
            $data = array(
                'message' => $validated->errors(),
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // Input
        $document_type = $request->input('document_type');
        $data = $request->input('file');

        // B64
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $file = base64_decode($data);
        $typeFile = explode(':', $type);
        $extension = explode('/', $typeFile[1]);
        $ext = $extension[1];

        // File Name
        $file_name = Str::upper($document_type) . '-' . time() . '.' . $ext;
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
        $document->file_name = $file_name;
        $document->ext = $ext;

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
        return response()->json($document, 202);
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
        $status_id = $request->get('status_id');

        if ($request->get('status_id') == null or $request->get('status_id') == '') {
            $data = array(
                'message' => [
                    'status_id' => [
                        'Registro con problemas, favor de validar status_id no se pudo actualizar.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        if ($request->get('status_id') != null and $request->get('status_id') != '') {
            $document->status_id = $status_id;
        }

        // Eliminar archivo
        Storage::disk('ctributarias')->delete($document->file_name);

        $document->update();

        $data = array(
            'document' => $document,
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        );

        return response()->json([
            'data' => $data
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
