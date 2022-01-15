<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelClient;
use App\Models\ModelLivreur;
use App\Models\ModelDemande;
use App\Models\ModelLivraison;



class LivraisonController extends Controller{

    public $successStatus = 200;

    /// Accepter livreur (la demande devient livraison)
    public function ajouterLivraiosn(Request $request)  { 
        $validator = Validator::make($request->all(), 
        [   'idLivreur' => 'required|string|max:255',
            'idClient' => 'required|string|max:255', 
            'idDemande' => 'required|string|max:255',
            'prix' => 'required|string|max:255',  
        ]);
        if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);                      
        }
        else {
          $livraison = ModelLivraison::create([
                    'idLivreur' => $request->idLivreur,
                    'idClient' => $request->idClient,
                    'idDemande' => $request->idDemande,
                    'prix'=> $request->prix,
                    'statutLivraison' => 'en attente',
                ]);
                }
         return response()->json("la demande de livraison est devient livraison avec succès", $this-> successStatus);        
    }

    /// Obtenir les livraisons par id client
    public function obtenirLivraisonsParIdClient(Request $request,$idc) {
      
      $livraison =  ModelLivraison::where('idClient', $idc)
      ->whereIn('statutLivraison',['En cours','en attente'])
      ->where('feedback_client.note', null)
      ->orderBy('id', 'desc')->get();
      $demande = ModelDemande::all();

      ///// Filtres Mes demandes Client
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
          $demande = $demande->where('dateLivraison',$request->input('dateLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande = $demande->where('contenu',$request->input('contenu'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande = $demande->where('nature',$request->input('nature'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande = $demande->where('taille',$request->input('taille'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande = $demande->where('poids',$request->input('poids'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande = $demande->where('regionRecuperation',$request->input('regionRecuperation'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande = $demande->where('regionLivraison',$request->input('regionLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande = $demande->where('villeDeDepart',$request->input('villeDeDepart'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande = $demande->where('villeDeLivraison',$request->input('villeDeLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();}
        //// trie Mes demandes Client
        if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
          if ($request->triePar == "Date de livraison (croissant)") {
              $demande = ModelDemande::find('dateLivraison');
              $livraison= $livraison->sortBy($demande)->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
            if ($request->triePar == "Date de livraison (decroissant)") {
              $demande = ModelDemande::find('dateLivraison');
              $livraison= $livraison->sortByDesc($demande)->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
            if ($request->triePar == "Nombre d’étoiles (croissant)") {
              $livreur = ModelLivreur::find('noteTotal');
              $livraison= $livraison->sortBy($livreur)->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
            if ($request->triePar == "Nombre d’étoiles (decroissant)") {
              $livreur = ModelLivreur::find('noteTotal');
              $livraison= $livraison->sortByDesc($livreur)->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
            if ($request->triePar == "Prix (croissant)") {
              $livraison= $livraison->sortBy('prix')->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
            if ($request->triePar == "Prix (decroissant)") {
              $livraison= $livraison->sortByDesc('prix')->where('idClient', $idc)->where('statutLivraison','en attente')->values();
            }
          }
        return response()->json($livraison);
    }

    /// Obtenir les livraisons par id Livreur 
    public function obtenirLivraisonsParIdLivreur(Request $request,$idl) {
      
      $livraison = ModelLivraison::where('idLivreur', $idl)
      ->whereIn('statutLivraison',['En cours','en attente'])
      ->where('feedback_livreur.note', null)
      ->orderBy('id', 'desc')->get();
      $demande = ModelDemande::all();
      ///// Filtres Mes demandes livreur
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
          $demande = $demande->where('dateLivraison',$request->input('dateLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande = $demande->where('contenu',$request->input('contenu'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande = $demande->where('nature',$request->input('nature'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande = $demande->where('taille',$request->input('taille'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande = $demande->where('poids',$request->input('poids'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande = $demande->where('regionRecuperation',$request->input('regionRecuperation'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande = $demande->where('regionLivraison',$request->input('regionLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande = $demande->where('villeDeDepart',$request->input('villeDeDepart'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande = $demande->where('villeDeLivraison',$request->input('villeDeLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();}
        //// trie Mes demandes Client
        if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
          if ($request->triePar == "Date de livraison (croissant)") {
              $demande = ModelDemande::find('dateLivraison');
              $livraison= $livraison->sortBy($demande)->values();
            }
            if ($request->triePar == "Date de livraison (decroissant)") {
              $demande = ModelDemande::find('dateLivraison');
              $livraison= $livraison->sortByDesc($demande)->values();
            }
            if ($request->triePar == "Prix (croissant)") {
              $livraison= $livraison->sortBy('prix')->values();
            }
            if ($request->triePar == "Prix (decroissant)") {
              $livraison= $livraison->sortByDesc('prix')->values();
            }
          }
        return response()->json($livraison);
    }

    /// Obtenir des Commentaires livreur
    public function obtenirCommentairessParIdLivreur($idl) {
        return ModelLivraison::where('idLivreur', $idl)
        ->where('statutLivraison','Livré')
        ->whereNotNull('feedback_livreur.commentaire')
        ->orderBy('id', 'desc')->get();
    }

    /// Le client reçu la livraison
    public function livraisonReçu(Request $request,$idLivraison){
        $livraison = ModelLivraison::find($idLivraison);
        $client=ModelClient::find($livraison->idClient);
        $livreur=ModelLivreur::find($livraison->idLivreur);

            $livraison->feedback_client = [
                'confirmationLivraison'=>"reçu",
            ];  
        if(
            $livraison->feedback_livreur['confirmationLivraison'] == "livré" && 
            $livraison->feedback_client['confirmationLivraison'] == "reçu" ){
                $livraison->statutLivraison = "Livré";
                $client->nombreLivraisons++;
                $livreur->nombreLivraisons++;
            }
            $client->save();
        $livraison->save();
        return response()->json("le client reçu la livraison.", $this-> successStatus);    
    }

    /// Ajouter un client de retour
    public function ajouterRetroactionClient(Request $request,$idLivraison){
        $livraison = ModelLivraison::find($idLivraison);
        $livreur = ModelLivreur::find($livraison->idLivreur);
        $validator = Validator::make($request->all(), 
        [  
            'note'=> 'required',
            'commentaire' => 'required|string|max:255'
        ] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
            $livraison->feedback_client = [
                'confirmationLivraison'=>"reçu",
                'note'=>$request->note,
                'justification'=>$request->justification,
                'commentaire'=>$request->commentaire
            
        ];
        if(
          $livraison->feedback_livreur['confirmationLivraison'] == "livré" && 
          $livraison->feedback_client['confirmationLivraison'] == "reçu" ){
          $note = ( $livreur->noteTotal * ($livreur->nombreLivraisons-1)+$request->note)/$livreur->nombreLivraisons;
        }else{
          $note = ( ($livreur->noteTotal * $livreur->nombreLivraisons)+$request->note)/($livreur->nombreLivraisons+1);
        } 
          
        $livreur->noteTotal = $note;
        $livraison->save();
        $livreur->save();
        return response()->json("feedback client a ajouté a cette livraison avec succès.", $this-> successStatus);    
        }
    }

    /// Livraiosn livré
    public function livraisonLivré(Request $request,$idLivraison){  
      $livraison = ModelLivraison::find($idLivraison);
      $livreur=ModelLivreur::find($livraison->idLivreur);
      $client=ModelClient::find($livraison->idClient);
              $livraison->feedback_livreur = [
                  'confirmationLivraison'=>"livré",
        ];
        if(
          $livraison->feedback_livreur['confirmationLivraison'] == "livré" && 
          $livraison->feedback_client['confirmationLivraison'] == "reçu" ){
              $livraison->statutLivraison = "Livré";
              $livreur->nombreLivraisons++;
              $client->nombreLivraisons++;
          }
          $livreur->save();
        $livraison->save();
        return response()->json("le livreur livré la livraison.", $this-> successStatus);    
    }

    /// Ajouter un livreur de feed-back
    public function ajouterRetroactionLivreur(Request $request,$idLivraison){
      $livraison = ModelLivraison::find($idLivraison);
      $client = ModelClient::find($livraison->idClient);
      $validator = Validator::make($request->all(), 
      [  
          'note'=> 'required|string|max:255', 
          'commentaire' => 'required|string|max:255',
      ] );
      if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);            
      }else {
          $livraison->feedback_livreur = [
              'confirmationLivraison'=>"livré",
              'note'=>$request->note,
              'justification'=>$request->justification,
              'commentaire'=>$request->commentaire
          
          ];
      if(
        $livraison->feedback_livreur['confirmationLivraison'] == "livré" && 
        $livraison->feedback_client['confirmationLivraison'] == "reçu" ){
            
            $note = ( $client->noteTotal * ($client->nombreLivraisons-1)+$request->note)/$client->nombreLivraisons;
          }else{   
                    $note = ( ($client->noteTotal * $client->nombreLivraisons)+$request->note)/($client->nombreLivraisons+1);          
            }
        $client->noteTotal = $note;
        $client->save();
        $livraison->save();
        return response()->json("feedback livreur a ajouté a cette livraison avec succès.", $this-> successStatus);    
      }
    }

    /// Modifier le statut de livraison
    public function modifierStatutLivraison(Request $request,$idLivraison){
        $livraison = ModelLivraison::find($idLivraison);
        $livraison->statutLivraison = "$request->statutLivraison";
        $livraison->save();
        return response()->json("la statut de livraison est modifié avec sucées", $this-> successStatus);    
    }

    /// Obtenir les historiques par identifiant client
    public function obtenirHistoriquesParIdClient(Request $request,$idc) {
          
        $livraison = ModelLivraison::where('idClient', $idc)
        ->wherein('statutLivraison',['Livré','Annulé par livreur','Annulé par client'])
        ->orderBy('id', 'desc')->get();
        $demande = ModelDemande::all();
        ///// Filtres Mes demandes Client
        if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
            $demande = $demande->where('dateLivraison',$request->input('dateLivraison'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
            $demande = $demande->where('contenu',$request->input('contenu'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
            $demande = $demande->where('nature',$request->input('nature'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
            $demande = $demande->where('taille',$request->input('taille'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
            $demande = $demande->where('poids',$request->input('poids'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
            $demande = $demande->where('regionRecuperation',$request->input('regionRecuperation'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
            $demande = $demande->where('regionLivraison',$request->input('regionLivraison'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
            $demande = $demande->where('villeDeDepart',$request->input('villeDeDepart'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
          }
          if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
            $demande = $demande->where('villeDeLivraison',$request->input('villeDeLivraison'));
            $idDemande = $demande->pluck('_id');
            $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
          //// trie Mes demandes Client
          if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
            if ($request->triePar == "Date de livraison (croissant)") {
                $demande = ModelDemande::find('dateLivraison');
                $livraison= $livraison->sortBy($demande)->values();
              }
              if ($request->triePar == "Date de livraison (decroissant)") {
                $demande = ModelDemande::find('dateLivraison');
                $livraison= $livraison->sortByDesc($demande)->values();
              }
              if ($request->triePar == "Prix (croissant)") {
                $livraison= $livraison->sortBy('prix')->values();
              }
              if ($request->triePar == "Prix (decroissant)") {
                $livraison= $livraison->sortByDesc('prix')->values();
              }
            }
          return response()->json($livraison);
    }

    /// Obtenir les Historiques par id livreur
    public function obtenirHistoriquesParIdLivreur(Request $request,$idl) {
      $livraison =  ModelLivraison::where('idLivreur', $idl)
      ->wherein('statutLivraison',['Livré','Annulé par livreur','Annulé par client'])
      ->orderBy('id', 'desc')->get();
      $demande = ModelDemande::all();

      ///// Filtres Mes demandes Client
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
          $demande = $demande->where('dateLivraison',$request->input('dateLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande = $demande->where('contenu',$request->input('contenu'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande = $demande->where('nature',$request->input('nature'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande = $demande->where('taille',$request->input('taille'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande = $demande->where('poids',$request->input('poids'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande = $demande->where('regionRecuperation',$request->input('regionRecuperation'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande = $demande->where('regionLivraison',$request->input('regionLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande = $demande->where('villeDeDepart',$request->input('villeDeDepart'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande = $demande->where('villeDeLivraison',$request->input('villeDeLivraison'));
          $idDemande = $demande->pluck('_id');
          $livraison =$livraison->whereIn('idDemande',$idDemande)->values();
      }
      //trier
      if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
        if ($request->triePar == "Date de livraison (croissant)") {
            $demande = ModelDemande::find('dateLivraison');
            $livraison= $livraison->sortBy($demande)->values();
          }
          if ($request->triePar == "Date de livraison (decroissant)") {
            $demande = ModelDemande::find('dateLivraison');
            $livraison= $livraison->sortByDesc($demande)->values();
          }
          if ($request->triePar == "Prix (croissant)") {
            $livraison= $livraison->sortBy('prix')->values();
          }
          if ($request->triePar == "Prix (decroissant)") {
            $livraison= $livraison->sortByDesc('prix')->values();
          }
        }
            return response()->json($livraison);  
      
    }

}
