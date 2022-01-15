<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelClient;
use App\Models\ModelLivreur;
use App\Models\ModelNotification;

class NotificationController extends Controller {
    public $successStatus = 200;

    /// Obtenir une notification par identifiant utilisateur
    public function obtenirNotificationsParId ($idDestinataire) {
        return ModelNotification::where('idDestinataire', $idDestinataire)
        ->orderBy('created_at', 'desc')->get();
    }

    /// Ajouter une notification
    public function ajouterNotification(Request $request) { 
        $validator = Validator::make($request->all(), 
        [   'idDestinataire' => 'required|string', 
                'message' => 'required|string', 
        ]);
        if ($validator->fails()) { 
                        return response()->json(['error'=>$validator->errors()], 401);            
        }
        else {
                $notification = ModelNotification::create([
                        'idDestinataire' => $request->idDestinataire,
                        'idExpediteur' => $request->idExpediteur,
                        'idDemande'=> $request->idDemande,
                        'idLivraison'=> $request->idLivraison,
                        'message' => $request->message, 
                        'type' => $request->type,
                        'dejaVu' => false,
                        'dateNotifications' => $request->dateNotifications
                ]);
        }   
            return response()->json("une notification à été ajouté avec succées", $this-> successStatus);        
    }

    /// Obtenir les notifications qui pas encore vu 
    public function obtenirNotificationsPasVu($id) {
        $usr = User::find($id);
        $iduser =  $usr->idUser;
        $notific = ModelNotification::where('idDestinataire',$iduser)
        ->where('dejaVu', false)->get();
       return response()->json($notific);
       
   }

    /// Modificateur le status de notifications
    public function modifierVuNotificatios(Request $request,$iduser){
        $notific = ModelNotification::where('idDestinataire',$iduser)->where('dejaVu', false)->get();
        foreach($notific as $result){
            $result->dejaVu =true;
            $result->save();    
        }
        return response()->json("la vu de notifications est devenu true avec sucées", $this-> successStatus);       
    }

}
