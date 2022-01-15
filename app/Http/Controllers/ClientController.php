<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelClient;
use App\Models\ModelLivreur;

class ClientController extends Controller
{    public $successStatus = 200;

    /// Obtenir le client par son identifiant utilisateur
    function obtenirInformationClient($id){
        $user = User::find($id);
        $iduser=$user->idUser;
        $client=ModelClient::find($iduser);
        return response()->json($client);
    }

    /// Obtenir l'élément client par identifiant
    function obtenirElementClient($id,$elemnt){
        $client = ModelClient::find($id);
        $monElementClient = $client->$elemnt;
        return response()->json("$monElementClient");
    }

    /// Obtenir son identifiant client
    function obtenirClient($id){
        $client=ModelClient::find($id);
        return response()->json($client);
    }

    /// Ajouter un client de profil photo
    public function ajouterPhotoProfile(Request $request,$userId){
        $user = User::find($userId);
        $client = ModelClient::find($user->idUser);
        $validator = Validator::make($request->all(), 
        [   'imageProfile' => 'required|string|max:255',] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
            $client->imageProfile=$request->imageProfile;
        $client->save();
        return response()->json("la photo de profile a ajouté avec sucées", $this-> successStatus);    
        }
    }

    //Signaler client 
    public function signalerClient(Request $request,$idClient){
        $client = Modelclient::find($idClient);
        $validator = Validator::make($request->all(), 
        [   'causeDeSignale' => 'required|string|max:255',
        'idLivreur' => 'required|string'] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
            $client->nombreSignale++;
        $client->push('signales', [[
            'idLivreur'=>$request->idLivreur,
            'causeDeSignale'=>$request->causeDeSignale,   
        ]]);
        $client->save();
        return response()->json("vous aves signaler ce client avec succés", $this-> successStatus);
        }
    }

    /// Obtenir des Commentaires client
    public function ajouterCommentaireClient(Request $request,$idClient){
            $client = Modelclient::find($idClient);
            $validator = Validator::make($request->all(), 
              [   'note'=> 'required',
                  'commentaire' => 'required|string|max:255'
              ] );
              
              if ($validator->fails()) { 
                  return response()->json(['error'=>$validator->errors()], 401);            
              }else {
                $client->push('commentaires', [[
                  
                  'id_livreur'=>$request->id_livreur,
                  'note'=>$request->note,
                  'commentaire'=>$request->commentaire    
            ]]);
            $client->save();
            return response()->json("Une commentaires a été ajouté dans client avec succès.", $this-> successStatus);    
                  
            }
    }
    

}