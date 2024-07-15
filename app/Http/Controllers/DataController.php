<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $data = Data::create($request->all());

        // Check if weight1 or weight2 is less than 300 grams
        if ($data->weight1 < 300 || $data->weight2 < 300) {
            $this->sendWhatsAppNotification($data);
        }

        return response()->json($data, 201);
    }

    private function sendWhatsAppNotification(Data $data)
    {
        $apiKey = 'oAHZV+j+jqkxFpn#stHF'; // Ganti dengan API key Fonnte Anda
        $phoneNumber = '085852406558'; // Nomor WhatsApp tujuan

        // Determine notification message based on weight1 or weight2 value
        $message = '';
        if ($data->weight1 <= 200 && $data->weight1 >= 0){
            $message = 'Infus 1, hampir habis';
            if($data->weight1 <= 100 && $data->weight1 >=0){
                $message = 'Infus 1 hampir habis, harap segera diganti.';
            }
        } elseif($data->weight2 <= 200 && $data->weight2 >= 0){
            $message = 'Infus 2, hampir habis';
            if($data->weight2 <= 100){
                $message = 'Infus 2 hampir habis, harap segera diganti.';
            }
        }
        elseif ($data->weight1 <= 100 && $data->weight2 <= 100) {
            $message = 'Kedua Infus hampir habis, harap segera diganti.';
        } elseif ($data->weight1 < 100) {
            $message = 'Infus sudah habis, lakukan penggantian segera.';
        }

        // You can customize messages based on your specific requirements

        // Prepare data for WhatsApp notification
        $postData = json_encode([
            'target' => $phoneNumber,
            'message' => $message,
            'countryCode' => '62' // Optional: adjust according to your needs
        ]);

        // Send WhatsApp notification using cURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Log::error('Error sending WhatsApp notification: ' . $err);
        } else {
            \Log::info('WhatsApp notification sent successfully. Response: ' . $response);
        }
    }
    public function index()
    {
        $data = Data::all(); // Mengambil semua data dari model Data
        return response()->json($data);
    }
}
