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

        // Mengirim notifikasi berdasarkan kondisi berat
        $this->checkAndSendNotification($data);

        return response()->json([
            "message" => "Data telah ditambahkan."
        ], 201);
    }

    private function checkAndSendNotification(Data $data)
    {
        $weight1 = $data->weight1;
        $weight2 = $data->weight2;

        // Kombinasi notifikasi untuk weight1 dan weight2
        if ($weight1 < 60 && $weight2 > 60) {
            $this->sendNotificationMultipleTimes($data, 'Infus 1 Habis, Infus 2 Masih Tersedia', 4);
        } elseif ($weight1 > 60 && $weight2 < 60) {
            $this->sendNotificationMultipleTimes($data, 'Infus 2 Habis, Infus 1 Masih Tersedia', 4);
        } elseif ($weight1 < 60 && $weight2 < 60) {
            $this->sendNotificationMultipleTimes($data, 'Kedua Infus Habis, lakukan pergantian sekarang', 4);
        } elseif ($weight1 >= 60 && $weight1 < 100 && $weight2 >= 60 && $weight2 < 100) {
            $this->sendNotificationMultipleTimes($data, 'Infus 1 dan Infus 2 Hampir Habis', 2);
        } elseif ($weight1 >= 100 && $weight1 < 200 && $weight2 >= 100 && $weight2 < 200) {
            $this->sendWhatsAppNotification($data, 'Infus 1 dan Infus 2 Hampir Habis');
        }

        // Notifikasi individual untuk weight1
        if ($weight1 < 60) {
            $this->sendNotificationMultipleTimes($data, 'Infus 1 Habis, lakukan pergantian sekarang', 4);
        } elseif ($weight1 >= 60 && $weight1 < 100) {
            $this->sendNotificationMultipleTimes($data, 'Infus 1 Hampir habis, harap segera diganti', 2);
        } elseif ($weight1 >= 100 && $weight1 < 200) {
            $this->sendWhatsAppNotification($data, 'Infus 1 Hampir habis');
        }

        // Notifikasi individual untuk weight2
        if ($weight2 < 60) {
            $this->sendNotificationMultipleTimes($data, 'Infus 2 Habis, lakukan pergantian sekarang', 4);
        } elseif ($weight2 >= 60 && $weight2 < 100) {
            $this->sendNotificationMultipleTimes($data, 'Infus 2 Hampir habis, harap segera diganti', 2);
        } elseif ($weight2 >= 100 && $weight2 < 200) {
            $this->sendWhatsAppNotification($data, 'Infus 2 Hampir habis');
        }
    }

    private function sendNotificationMultipleTimes(Data $data, $message, $times)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->sendWhatsAppNotification($data, $message);
        }
    }

    private function sendWhatsAppNotification(Data $data, $message)
    {
        $apiKey = '+EKUbaJ-8Ha@kMvtmv76'; // Ganti dengan API key Fonnte Anda
        $phoneNumber = '085692429796'; // Nomor WhatsApp tujuan

        // Prepare data for WhatsApp notification
        $postData = json_encode([
            'target' => $phoneNumber,
            'message' => $message,
            'countryCode' => '62' // Optional: adjust according to your needs
        ]);

        // Send WhatsApp notification using cURL
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
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
