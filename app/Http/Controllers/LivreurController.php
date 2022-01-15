<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelClient;
use App\Models\ModelLivreur;
use App\Models\ModelLivraison;

class LivreurController extends Controller
{    public $successStatus = 200;

    /// Récupérer les informations de livreur par id user
    function obtenirInformationLivreur($id){
      $user = User::find($id);
      $iduser=$user->idUser;
      $livreur=ModelLivreur::find($iduser);
      return response()->json($livreur);
    }

    /// Récupérer les informations de livreur par id
    function obtenirLivreur($id){
      $livreur=ModelLivreur::find($id);
      return response()->json($livreur);
    }

     /// Obtenir l'élément Livreur
    function obtenirElementLivreur($id,$elemnt){
      $livreur = ModelLivreur::find($id);
      $monElementLivreur = $livreur->$elemnt;
      return response()->json("$monElementLivreur");
    }

    /// Ajouter photo de profile
    public function ajouterPhotoProfileLivreur(Request $request,$userId){
        $user = User::find($userId);
        $livreur = ModelLivreur::find($user->idUser);
        $validator = Validator::make($request->all(), 
        [   'imageProfile' => 'required|string|max:255',] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        else {        
            $livreur->imageProfile=$request->imageProfile;
        $livreur->save();
        return response()->json("la photo de profile a ajouté avec sucées", $this-> successStatus);    
        }
    }
 
    /// Signaler livreur 
    public function signalerLivreur(Request $request,$idlivreur){
        $livreur = ModelLivreur::find($idlivreur);
        $validator = Validator::make($request->all(), 
        [   
          'causeDeSignale' => 'required|string|max:255',
          'idClient' => 'required|string'
          ] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
        $livreur->nombreSignale++;
        $livreur->push('signales', [[
            'idClient'=>$request->idClient,
            'causeDeSignale'=>$request->causeDeSignale,      
        ]]);
        $livreur->save();
        return response()->json("vous aves signaler ce livreur avec succés", $this-> successStatus);
        }
    }

    /// Obtenir la liste des livreurs
    public function obtenirTousLivreurs(Request $request) {
        $user = ModelLivreur::all();
      ///// Filtrer
              if( $request->has('ville') && $request->ville!="Tout" && $request->ville!="null"){
                $user =$user->where('ville',$request->input('ville'))->values();
              }
              if( $request->has('region') && $request->region!="Tout" && $request->region!="null") {
                $user= $user->where('region', $request->input('region'))->values();
              }
              $note=(int) $request->noteTotal; 
              if(  $request->has('noteTotal') && $request->noteTotal!="Tout" && $request->noteTotal!="null") {
                $user= $user->where('noteTotal','>=', $note)
                ->where('noteTotal','<', $note+1)->values();
              }   
              if( $request->has('transport') && $request->transport!="Tout" && $request->transport!="null") {
                for($i=0;$i<1;$i++){
                    $user=$user->where("moyenTransport.$i.type",$request->input('transport'))->values();
                }
              }
      ///// Trier 
              if(  $request->has('triePar')  && $request->triePar!="null") {
                    if ($request->triePar == "Nombre d’étoiles (croissant)") {
                        $user= $user->sortBy('noteTotal')->values();
                    }
                    if ($request->triePar == "Nombre d’étoiles (decroissant)") {
                        $user= $user->sortByDesc('noteTotal')->values();
                    }
                    if ($request->triePar == "Nombre du livraison (croissant)") {
                        $user= $user->sortBy('nombreLivraisons')->values();
                    }
                    if ($request->triePar == "Nombre du livraison (decroissant)") {
                        $user= $user->sortByDesc('nombreLivraisons')->values();
                    }              
              } 
          return response()->json($user);
    }

    /// Modifier enSrevice Livreur (true ou false)
    public function modifierEnService(Request $request,$idLivreur){
      $livreur = ModelLivreur::find($idLivreur);
      if($request->enService=="true"){
        $livreur->enService = true;
      }
      if($request->enService=="false"){
        $livreur->enService = false;
      }
      $livreur->save();
      return response()->json("La disponabilité de livreur a été modifiée avec succès", $this-> successStatus);    
    }

    /// Ajouter autre Moyen de Transport
    public function ajouterMoyenTransport(Request $request,$idLivreur){
      $livreur = ModelLivreur::find($idLivreur);
      $validator = Validator::make($request->all(), 
        [   'immatriculation' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'imagePermis' => 'required|string',
            'imageCarteGrise' => 'required|string',
        ] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
            $livreur->push('moyenTransport', [[
                'immatriculation'=>$request->immatriculation,
                'type'=>$request->type,
                'imagePermis'=>$request->imagePermis,
                'imageCarteGrise'=>$request->imageCarteGrise,     
      ]]);
      $livreur->save();
      return response()->json("Un moyen de transport a été ajouté avec succès.", $this-> successStatus);    
      }
    }

    /// Obtenir des Commentaires livreur
    public function ajouterCommentaireLivreur(Request $request,$idLivreur){
      $livreur = ModelLivreur::find($idLivreur);
      $validator = Validator::make($request->all(), 
        [   'note'=> 'required',
            'commentaire' => 'required|string|max:255'
        ] );
        
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
          $livreur->push('commentaires', [[
            
            'id_client'=>$request->id_client,
            'note'=>$request->note,
            'commentaire'=>$request->commentaire    
      ]]);
      $livreur->save();
      return response()->json("Une commentaires a été ajouté avec succès.", $this-> successStatus);    
            
      }}
    }






