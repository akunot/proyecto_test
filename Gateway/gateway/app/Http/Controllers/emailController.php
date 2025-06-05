<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class emailController extends Controller
{

    protected $URL;

    public function __construct()
    {
        $this->URL = getenv('MICROSERVICE_EMAIL_URL');
    }

    public function enviarCorreo(Request $request)
    {
        $URL = $this->URL;
        $response = Http::withHeaders(['x-api-key' => getenv('MICROSERVICE_EMAIL_KEY')])->post($URL, $request->all());
        return response()->json($response->json());
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */


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
