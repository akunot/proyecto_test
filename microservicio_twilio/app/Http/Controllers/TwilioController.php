<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;
use app\Models\Twilio;
use GuzzleHttp\Promise\Create;

class TwilioController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $this->twilioService->sendSms($request->phone, $request->message);

            $twilio = Twilio::Create();
            return response()->json(['message' => 'SMS enviado con éxito'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error enviando SMS: ' . $e->getMessage()], 500);
        }


    }
}
