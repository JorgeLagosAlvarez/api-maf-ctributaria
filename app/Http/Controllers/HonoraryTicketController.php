<?php

namespace App\Http\Controllers;

use App\HonoraryTicket;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HonoraryTicketController extends Controller
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

        // Boletas de Honorarios por Status
        $honorary_tickets = HonoraryTicket::where('status_id', $status->id)->get();

        return response()->json($honorary_tickets->load('status'), 202);
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
            'id_solicitud' => ['required', 'string', 'max:15'],
            'document_type' => ['required', 'string', 'max:100'],
            'barcode' => ['required', 'string', 'max:100'],
            'workitemid' => ['string', 'max:100'],
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
        $id_solicitud = $request->get('id_solicitud');
        $document_type = $request->get('document_type');
        $barcode = $request->get('barcode');
        $workitemid = $request->get('workitemid');
        $validation = $request->get('validation', false);

        if ( !$document_type and Str::lower($document_type) != 'boleta honorario' ) {
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

        $honorary_ticket = new HonoraryTicket();
        $honorary_ticket->id_solicitud = $id_solicitud;
        $honorary_ticket->document_type = ucwords($document_type);
        $honorary_ticket->barcode = $barcode;
        $honorary_ticket->validation = $validation;
        $honorary_ticket->workitemid = $workitemid;

        $honorary_ticket->save();

        return response()->json([
            'honorary_ticket' => $honorary_ticket,
            'message' => 'Registro grabado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HonoraryTicket  $honoraryTicket
     * @return \Illuminate\Http\Response
     */
    public function show($id_solicitud)
    {
        // Objeto HonoraryTicket
        $honorary_ticket = HonoraryTicket::where('id_solicitud', $id_solicitud)->get();

        if ( $honorary_ticket->count() == 0 ) {
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

        $honorary_ticket = $honorary_ticket[0];

        return response()->json([
            'honorary_ticket' => $honorary_ticket->load('status')
        ], 202);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HonoraryTicket  $honoraryTicket
     * @return \Illuminate\Http\Response
     */
    public function edit(HonoraryTicket $honoraryTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HonoraryTicket  $honoraryTicket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_solicitud)
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

        // Objeto HonoraryTicket
        $honorary_ticket = HonoraryTicket::where('id_solicitud', $id_solicitud)->get();

        if ( $honorary_ticket->count() == 0 ) {
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
                    ]
                ],
                'type' => 'error'
            );

            return response()->json($data, 404);
        }

        $honorary_ticket = $honorary_ticket[0];

        if ( $status_id != null and $status_id != '' ) {
            $honorary_ticket->status_id = $status_id;
        }

        if ($validation != null and $validation != '') {
            $honorary_ticket->validation = $validation;
        }

        $honorary_ticket->update();

        return response()->json([
            'honorary_ticket' => $honorary_ticket->load('status'),
            'message' => 'Registro actualizado correctamente.',
            'type' => 'success'
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HonoraryTicket  $honoraryTicket
     * @return \Illuminate\Http\Response
     */
    public function destroy(HonoraryTicket $honoraryTicket)
    {
        //
    }
}
