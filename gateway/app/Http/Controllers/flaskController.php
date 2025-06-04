<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\emailController;

class flaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $URL;

    public function __construct()
    {
        $this->URL = getenv('MICROSERVICE_FLASK_URL');
    }

    public function index()
    {
        $URL = $this->URL .'/obtener_predicciones';
        $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_FLASK_KEY')])->get($URL);
        return response()->json($response->json());
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
    $URL = $this->URL . '/predecir';
    $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_FLASK_KEY')])->post($URL, $request->all());

    if ($response->successful()) { // Equivalente a $response->status() == 200
        // Obtener la respuesta en formato JSON
        $responseData = $response->json();

        
        if ($responseData['sentimiento'] == "Negativo") {
            
            $datosCorreo = [
                "destinatario" => "juamontoyara@unal.edu.co",
                "asunto" => "Correo de Prueba",
                "titulo" => "nuevo comentario",
                "mensaje" => $request->input('texto') . "\n\n" . $responseData['sentimiento']
            ];
            $email = new emailController();
            $email->enviarCorreo(new Request($datosCorreo));

        }


        return response()->json($response->json());

    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $URL = $this->URL .'/prediccion/'.$id;
        $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_FLASK_KEY')])->get($URL);
        return response()->json($response->json());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $URL = $this->URL .'/prediccion/'.$id;
        $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_FLASK_KEY')])->put($URL, $request->all());
        
        if ($response->successful()) { // Equivalente a $response->status() == 200
        
            // Construir los datos del correo
            $datosCorreo = [
                "destinatario" => "juamontoyara@unal.edu.co",
                "asunto" => "Correo de Prueba",
                "titulo" => "nuevo comentario",
                "mensaje" => $request->input('texto') // Usar input() para evitar errores si "texto" no existe
            ];
            
            $email = new emailController();
            $email->enviarCorreo(new Request($datosCorreo));
        }
        
        return response()->json($response->json());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $URL = $this->URL .'/prediccion/'.$id;
        $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_FLASK_KEY')])->delete($URL);
        return response()->json($response->json());
    }
}
