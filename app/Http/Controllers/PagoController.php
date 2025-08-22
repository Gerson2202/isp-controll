<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
class PagoController extends Controller
{
    public function index()
    {
        return view('pagos.index');
    }

     public function indexx()
    {
        $receiverNumber = "RECEIVER_NUMBER";
        $message = "This is testing from ItSolutionStuff.com";
  
        try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
  
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message]);
  
            dd('SMS Sent Successfully.');
  
        } catch (\Exception $e) {
            dd("Error: ". $e->getMessage());
        }
    }
    // public function whaptsapp()
    // {
    //     $twilioSid= env('TWILIO_SID');
    //     $twilioToken= env('TWILIO_AUTH_TOKEN');
    //     $twilioWhatsAppNumber= env('TWILIO_WHATSAPP_NUMBER');
    //     $recipientNumber = 'whatsapp:+573215852059';
    //     $message= 'Hola desde twilio heloo';

    //     $twilio= new Client($twilioSid, $twilioToken);
    //     try{
    //         $twilio-> messages->create(
    //             $recipientNumber,
    //             [
    //                 "from" =>'whatsapp:'.$twilioWhatsAppNumber,
    //                 "body" =>$message,
    //             ]
    //             );
    //             return response()->Json(['message'=> 'enviado exitasamente']);
    //     }catch(\Exception $e){
    //         return response()->Json(['error'=> $e->getMessage()],500);
    //     }

    // }

    //  public function enviarWhatsApp()
    // {
    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');

    //     $from = env('TWILIO_WHATSAPP_FROM'); // whatsapp:+14155238886
    //     $to = 'whatsapp:+573215852059'; // Número destino
    //     $contentSid = env('TWILIO_CONTENT_SID');

    //     $response = Http::withBasicAuth($sid, $token)
    //         ->post("https://messaging.twilio.com/v1/Services/$sid/Messages", [
    //             "to" => $to,
    //             "from" => $from,
    //             "contentSid" => $contentSid,
    //             "contentVariables" => json_encode([
    //                 "1" => "12/1",
    //                 "2" => "3pm"
    //             ]),
    //         ]);

    //     if ($response->successful()) {
    //         return '✅ Mensaje enviado correctamente por WhatsApp.';
    //     } else {
    //         return '❌ Error: ' . $response->body();
    //     }
    // }
}
