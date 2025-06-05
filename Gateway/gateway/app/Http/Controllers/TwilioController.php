<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TwilioController extends Controller
{   
    protected $URL;
    public function __construct()
    {
        $this->URL = getenv('MICROSERVICE_TWILIO_URL');
    }

    public function notification(Request $request)
    {
        $URL = $this->URL . '/send-sms';

        // Obtener el texto del mensaje desde la solicitud entrante
        $messageText = request('texto'); // Asume que el campo en la request se llama "texto"
        
        // Construir el cuerpo de la solicitud POST
        $response = Http::withBody(json_encode([
            "phone" => "+573002272909",
            "message" => $messageText // Usar el texto obtenido de la request
        ]), 'application/json')->post($URL);
        
        // Devolver la respuesta de la API como JSON
        return response()->json($response->json());
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
