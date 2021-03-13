<?php

namespace App\Http\Controllers;

use App\ComprobanteDomicilio;
use App\Status;
use App\ProveedorServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComprobanteDomicilioController extends Controller
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
            'proveedor_servicio_id' => ['required', 'integer'],
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
        $proveedor_servicio_id = $request->get('proveedor_servicio_id');

        // Objeto Status, ProveedorServicio
        $status = Status::find($status_id);
        $proveedor_servicio = ProveedorServicio::find($proveedor_servicio_id);

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

        if ( !$proveedor_servicio_id ) {
            $data = array(
                'message' => [
                    'proveedor_servicio_id' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        // ComprobanteDomicilio por Status
        $comprobante_domicilio = ComprobanteDomicilio::where('status_id', $status->id)->where('proveedor_servicio_id', $proveedor_servicio->id)->get();

        return response()->json($comprobante_domicilio->load('status')->load('proveedor_servicio'), 202);
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
            'proveedor_servicio_id' => ['required', 'integer'],
            'nro_cliente' => ['required', 'string', 'max:50'],
            'nombre_cliente' => ['string', 'max:200'],
            'direccion_cliente' => ['required', 'string', 'max:200'],
            'comuna_cliente' => ['required', 'string', 'max:50'],
            'fecha_emision' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'digits_between:1,8'],
            'workitemid' => ['required', 'unique:comprobante_domicilios', 'string', 'max:100'],
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
        $proveedor_servicio_id = $request->get('proveedor_servicio_id');
        $nro_cliente = $request->get('nro_cliente');
        $nombre_cliente = $request->get('nombre_cliente');
        $direccion_cliente = $request->get('direccion_cliente');
        $comuna_cliente = $request->get('comuna_cliente');
        $fecha_emision = $request->get('fecha_emision');
        $monto = $request->get('monto', 0);
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        // Objeto ProveedorServicio
        $proveedor_servicio = ProveedorServicio::find($proveedor_servicio_id);

        if ( !$proveedor_servicio ) {
            $data = array(
                'message' => [
                    'proveedor_servicio_id' => [
                        'El dato que intentas enviar no es el correcto.'
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        if ( !$document_type or Str::lower($document_type) != 'comprobante domicilio' ) {
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

        $comprobante_domicilio = new ComprobanteDomicilio();
        $comprobante_domicilio->document_type = ucwords($document_type);
        $comprobante_domicilio->id_solicitud = $id_solicitud;
        $comprobante_domicilio->proveedor_servicio_id = $proveedor_servicio_id;
        $comprobante_domicilio->nro_cliente = $nro_cliente;
        $comprobante_domicilio->nombre_cliente = $nombre_cliente;
        $comprobante_domicilio->direccion_cliente = $direccion_cliente;
        $comprobante_domicilio->comuna_cliente = $comuna_cliente;
        $comprobante_domicilio->workitemid = $workitemid;
        $comprobante_domicilio->validation = $validation;
        $comprobante_domicilio->fecha_emision = Carbon::parse($fecha_emision);
        $comprobante_domicilio->monto = $monto;

        $comprobante_domicilio->save();

        return response()->json([
            'comprobante_domicilio' => $comprobante_domicilio,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ComprobanteDomicilio  $comprobanteDomicilio
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto ComprobanteDomicilio
        $comprobante_domicilios = ComprobanteDomicilio::where('id_solicitud', $id_solicitud)->get();

        if ( $comprobante_domicilios->count() == 0 ) {
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
            'comprobante_domicilios' => $comprobante_domicilios->load('status')->load('proveedor_servicio')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ComprobanteDomicilio  $comprobanteDomicilio
     * @return \Illuminate\Http\Response
     */
    public function edit(ComprobanteDomicilio $comprobanteDomicilio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ComprobanteDomicilio  $comprobanteDomicilio
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

        // Objeto ComprobanteDomicilio
        $comprobante_domicilio = ComprobanteDomicilio::where('id_solicitud', $id_solicitud)->where('workitemid', $workitemid)->get();

        if ( $comprobante_domicilio->count() == 0 ) {
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

        $comprobante_domicilio = $comprobante_domicilio[0];

        if ( $status_id != '' ) {
            $comprobante_domicilio->status_id = $status_id;
        }

        $comprobante_domicilio->validation = $validation;

        $comprobante_domicilio->update();

        return response()->json([
            'comprobante_domicilio' => $comprobante_domicilio->load('status')->load('proveedor_servicio'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ComprobanteDomicilio  $comprobanteDomicilio
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComprobanteDomicilio $comprobanteDomicilio)
    {
        //
    }
}
