<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionEmail;

class EmailController extends Controller
{
    public function enviarCorreo(Request $request)
    {
        $request->validate([
            'destinatario' => 'required|email',
            'asunto' => 'required|string',
            'titulo' => 'required|string',
            'mensaje' => 'required|string',
        ]);

        $datos = [
            'asunto' => $request->asunto,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje
        ];

        Mail::to($request->destinatario)->send(new NotificacionEmail($datos));

        return response()->json(['mensaje' => 'Correo enviado correctamente'], 200);
    }
}

