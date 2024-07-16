<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $data = new Data;
        $data->weight1 = $request->weight1;
        $data->weight2 = $request->weight2;
        $data->percent_weight1 = $request->percent_weight1;
        $data->percent_weight2 = $request->percent_weight2;
        $data->save();

        // Mengirim notifikasi setiap i = 3000
        if ($data->weight1 < 300 || $data->weight2 < 300) {
            static $i = 0;
            if ($i == 0) {
                $this->sendWhatsAppNotification($data);
            } 
            $i++;
            
            if ($i % 30000 == 0) {
                $this->sendWhatsAppNotification($data);
                $i=0;
            } 
        }

        return response()->json([
            "message" => "Data telah ditambahkan."
        ], 201);
    }


    private function sendWhatsAppNotification(Data $data)
    {
        $apiKey = 'UD#yNu+x__gYSD2dtAqr'; // Ganti dengan API key Fonnte Anda
        $phoneNumber = '089515563894'; // Nomor WhatsApp tujuan

        // Determine notification message based on weight1 or weight2 value
        $message = '';
        if ($data->weight1 < 60 && $data->weight2 < 60) {
            $message = 'Kedua infus Habis, lakukan pergantian sekarang';
        } elseif ($data->weight1 < 60) {
            $message = 'Infus 1 Habis, lakukan pergantian sekarang';
        } elseif ($data->weight2 < 60) {
            $message = 'Infus 2 Habis, lakukan pergantian sekarang';
        } elseif ($data->weight1 < 100 && $data->weight2 < 100) {
            $message = 'Kedua infus Habis, harap segera diganti.';
        } elseif ($data->weight1 < 100) {
            $message = 'Infus 1 Habis, harap segera diganti.';
        } elseif ($data->weight2 < 100) {
            $message = 'Infus 2 Habis, harap segera diganti.';
        } elseif ($data->weight1 < 200 && $data->weight2 < 200) {
            $message = 'Kedua infus Hampir Habis';
        } elseif ($data->weight1 < 200) {
            $message = 'Infus 1 Hampir Habis';
        } elseif ($data->weight2 < 200) {
            $message = 'Infus 2 Hampir Habis';
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
        )
        );

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
        $data = Data::latest()->first(); // Adjust this to return the latest data if needed

        return response()->json([
            'weight1' => $data->weight1,
            'weight2' => $data->weight2,
            'percent_weight1' => $data->percent_weight1, // Assuming 1000 is the max weight
            'percent_weight2' => $data->percent_weight2, // Adjust as per your logic
        ]);
    }

}
