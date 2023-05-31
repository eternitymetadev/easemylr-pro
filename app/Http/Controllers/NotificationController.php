<?php
 
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
  
class NotificationController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('pushNotification');
    } 
  
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendNotification(Request $request)
    {
        $firebaseToken =["eCcnJFOBReCr8mFfgPKopr:APA91bF8Kf7Uc9Evom3wFKN55hiTg4_yX66fZwQYE-OHHBjdDx62_0MTrQCqLP73uSjd4T6wCzWS9Kyi7aodtFXBfmC8_oqxRKkK8vIjASytNYKDuArKyFO6v0LUhYoWhXMVVn0wBmia"];
        //User::whereNotNull('device_token')->pluck('device_token')->all();
            

        $SERVER_API_KEY = env('FCM_SERVER_KEY');
    
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $dataString = json_encode($data);
      
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
      
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                 
        $response = curl_exec($ch);

        echo "<pre>"; print_r($response);die;
    
        return back()->with('success', 'Notification send successfully.');
    }
}