<?php

namespace App\Http\Controllers;

use App\LiquidacionCarabinero;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class LiquidacionCarabineroController extends Controller
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

        // LiquidacionCarabinero por status
        $liquidacion_carabineros = LiquidacionCarabinero::where('status_id', $status->id)->get();

        return response()->json($liquidacion_carabineros->load('status'), 202);
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
            'file' => ['required'],
            'nro_liquidacion' => ['required', 'string', 'max:50'],
            'nombre_cliente' => ['required', 'string', 'max:200'],
            'rut_cliente' => ['required', 'string', 'max:10'],
            'carga_familiar' => ['required', 'numeric', 'digits_between:1,2'],
            'total_haber' => ['required', 'numeric', 'digits_between:1,8'],
            'descuentos_legales' => ['required', 'numeric', 'digits_between:1,8'],
            'monto_liquido' => ['required', 'numeric', 'digits_between:1,8'],
            'periodo' => ['required', 'string', 'min:6', 'max:6'],
            'id_solicitud' => ['required', 'string', 'max:15'],
            'workitemid' => ['required', 'unique:liquidacion_carabineros', 'string', 'max:100'],
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
        $data = $request->get('file');
        $nro_liquidacion = $request->get('nro_liquidacion');
        $nombre_cliente = $request->get('nombre_cliente');
        $rut_cliente = $request->get('rut_cliente');
        $carga_familiar = $request->get('carga_familiar');
        $total_haber = $request->get('total_haber');
        $descuentos_legales = $request->get('descuentos_legales');
        $monto_liquido = $request->get('monto_liquido');
        $periodo = $request->get('periodo');
        $id_solicitud = $request->get('id_solicitud');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type or Str::lower($document_type) != 'liquidacion carabinero' ) {
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
        $file_name = Str::upper($document_type) . '_' . Carbon::now()->format('Ymd_His_v') . '.' . $ext;
        $file_name = str_replace(' ', '-', $file_name);

        $storage = Storage::disk('liquidacion_carabineros')->put($file_name, $file);

        if ( !$storage ) {
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

        $liquidacion_carabinero = new LiquidacionCarabinero();
        $liquidacion_carabinero->document_type = ucwords($document_type);
        $liquidacion_carabinero->file_name = $file_name;
        $liquidacion_carabinero->ext = $ext;
        $liquidacion_carabinero->nro_liquidacion = Str::upper($nro_liquidacion);
        $liquidacion_carabinero->nombre_cliente = Str::upper($nombre_cliente);
        $liquidacion_carabinero->rut_cliente = Str::upper($rut_cliente);
        $liquidacion_carabinero->carga_familiar = (string) (int) str_replace('.', '', $carga_familiar);
        $liquidacion_carabinero->total_haber = (string) (int) str_replace('.', '', $total_haber);
        $liquidacion_carabinero->descuentos_legales = (string) (int) str_replace('.', '', $descuentos_legales);
        $liquidacion_carabinero->monto_liquido = (string) (int) str_replace('.', '', $monto_liquido);
        $liquidacion_carabinero->periodo = (string) (int) str_replace('.', '', $periodo);
        $liquidacion_carabinero->id_solicitud = $id_solicitud;
        $liquidacion_carabinero->workitemid = $workitemid;
        $liquidacion_carabinero->validation = $validation;

        $liquidacion_carabinero->save();

        return response()->json([
            'liquidacion_carabinero' => $liquidacion_carabinero,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LiquidacionCarabinero  $liquidacionCarabinero
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto LiquidacionCarabinero
        $liquidacion_carabinero = LiquidacionCarabinero::where('id_solicitud', $id_solicitud)->get();
 
        if ( $liquidacion_carabinero->count() == 0 ) {
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
            'liquidacion_carabineros' => $liquidacion_carabinero->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LiquidacionCarabinero  $liquidacionCarabinero
     * @return \Illuminate\Http\Response
     */
    public function edit(LiquidacionCarabinero $liquidacionCarabinero)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LiquidacionCarabinero  $liquidacionCarabinero
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

        // Objeto LiquidacionCarabinero
        $liquidacion_carabinero = LiquidacionCarabinero::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $liquidacion_carabinero->count() == 0 ) {
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

        $liquidacion_carabinero = $liquidacion_carabinero[0];

        if ( $status_id != '' ) {
            $liquidacion_carabinero->status_id = $status_id;
        }

        $liquidacion_carabinero->validation = $validation;

        $liquidacion_carabinero->update();

        // Eliminar archivo
        Storage::disk('liquidacion_carabineros')->delete($liquidacion_carabinero->file_name);

        return response()->json([
            'liquidacion_carabinero' => $liquidacion_carabinero->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LiquidacionCarabinero  $liquidacionCarabinero
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiquidacionCarabinero $liquidacionCarabinero)
    {
        //
    }

    public function showb64($file_name)
    {
        $is_file = Storage::disk('liquidacion_carabineros')->has($file_name);
        if ( !$is_file ) {
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

        $file = Storage::disk('liquidacion_carabineros')->get($file_name);
        $fileb64 = base64_encode($file);

        return response()->json([
            'fileb64' => $fileb64
        ], 202);
    }
}
