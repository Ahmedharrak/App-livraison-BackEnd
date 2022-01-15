<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelDemande;
use App\Models\ModelLivreur;

class DemandeController extends Controller
{
    public $successStatus = 200;


    /// Publier une demande (Publique)
    public function publierDemande(Request $request) { 
        $validator = Validator::make($request->all(), 
        [   'idClient' => 'required|string|max:255', 
            'dateLivraison' => 'required|string|max:255',
            'heureLivraison' => 'required|string|max:255',  
            'villeDeDepart' => 'required|string|max:255',
            'adresseRecuperationLivraison' => 'required|string|max:255',
            'adresseDestinationLivraison'  => 'required|string|max:255',
            'contenu' => 'required|string|max:255',
            'nature'  => 'required|string|max:255',
            'taille'  => 'required|string|max:255',
            'poids'   => 'required|string|max:255',
            
         
        ]);
        if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }else {
                $demandePulique = ModelDemande::create([
                    'idClient' => $request->idClient,
                    'statutDemande' => "ouvert",
                    'type' => "Public",
                    'dateLivraison' => $request->dateLivraison,
                    'heureLivraison' => $request->heureLivraison,
                    'adresseRecuperationLivraison' => $request->adresseRecuperationLivraison,
                    'villeDeDepart' => $request->villeDeDepart,
                    'regionRecuperation' => $request->regionRecuperation,
                    'adresseDestinationLivraison'  => $request->adresseDestinationLivraison,
                    'villeDeLivraison' => $request->villeDeLivraison,
                    'regionLivraison' => $request->regionLivraison,
                    'moyens_transport'=> [
                              "Voiture"=>  $request->Voiture=== 'true'? true:false,
                              "Moto"=> $request->Moto=== 'true'? true:false,
                              "Camion"=> $request->Camion=== 'true'? true:false,
                              "Pickup"=> $request->Pickup=== 'true'? true:false,
                              "Taxi"=>$request->Taxi=== 'true'? true:false,
                              "Velo"=>$request->Velo=== 'true'? true:false,
                        ],
                    'contenu' => $request->contenu,
                    'nature'  => $request->nature,
                    'taille'  => $request->taille,
                    'poids'   => $request->poids,
                    'description' => $request->description
                ]);
                }
        
         return response()->json("La demande de livraison est publié avec succès", $this-> successStatus);        
    }

    /// Obtenir les demandes client par Id 
    public function obtenirDemandesClientParId (Request $request,$idc) {
      $demande = ModelDemande::all();
      $demande = $demande->where('idClient', $idc)
      ->where('statutDemande', 'ouvert')->values();
    
      ///// Filtres Mes demandes Client
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
        $demande =$demande->where('dateLivraison',$request->input('dateLivraison'))->values();
      }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande =$demande->where('contenu',$request->input('contenu'))->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande =$demande->where('nature',$request->input('nature'))->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande =$demande->where('taille',$request->input('taille'))->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande =$demande->where('poids',$request->input('poids'))->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande =$demande->where('regionRecuperation',$request->input('regionRecuperation'))->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande =$demande->where('regionLivraison',$request->input('regionLivraison'))->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande =$demande->where('villeDeDepart',$request->input('villeDeDepart'))->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande =$demande->where('villeDeLivraison',$request->input('villeDeLivraison'))->values();
        }
        //// trie Mes demandes Client
        if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
          if ($request->triePar == "Date de livraison (croissant)") {
              $demande= $demande->sortBy('heureLivraison')->sortBy('Livraison')->sortBy('dateLivraison')->values();
            }
            if ($request->triePar == "Date de livraison (decroissant)") {
              $demande= $demande->sortByDesc('heureLivraison')->sortByDesc('dateLivraison')->values();
            }
          }
        return response()->json($demande);

    }

    /// Obtenir les demandes publiques voir par livreur
    public function obtenirTousDemandesPublic(Request $request) {

      $demande = ModelDemande::all();
      $demande = $demande->where('type', 'Public')
      ->where('statutDemande', 'ouvert')->values();
    
      ///// Filtres Mes demandes Client en espace livreur
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
          $demande =$demande->where('dateLivraison',$request->input('dateLivraison'))->values();
        }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande =$demande->where('contenu',$request->input('contenu'))->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande =$demande->where('nature',$request->input('nature'))->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande =$demande->where('taille',$request->input('taille'))->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande =$demande->where('poids',$request->input('poids'))->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande =$demande->where('regionRecuperation',$request->input('regionRecuperation'))->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande =$demande->where('regionLivraison',$request->input('regionLivraison'))->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande =$demande->where('villeDeDepart',$request->input('villeDeDepart'))->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande =$demande->where('villeDeLivraison',$request->input('villeDeLivraison'))->values();
        }
        //// trie Mes demandes Client
        if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
          if ($request->triePar == "Date de livraison (croissant)") {
              $demande= $demande->sortBy('heureLivraison')->sortBy('dateLivraison')->values();
            }
            if ($request->triePar == "Date de livraison (decroissant)") {
              $demande= $demande->sortByDesc('heureLivraison')->sortByDesc('dateLivraison')->values();
            }
          }
        return response()->json($demande);
    }

    /// Ajouter livreur interessee
    public function ajouterLivreurInteresse(Request $request,$idDemande){
        $damande = ModelDemande::find($idDemande);
        $validator = Validator::make($request->all(), 
        [   'prix' => 'required|string|max:255',
        'livreurs_interesses.*.idLivreur' => 'required|string|unique:livreurs'] );
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }else {
            $damande->push('livreurs_interesses', [[
                'idLivreur'=>$request->idLivreur,
                'statut'=>"en attente",
                'prix'=>$request->prix,
                'causeAnnulation_client'=>null,
                'causeAnnulation_livreur'=>null
            
        ]]);
        $damande->save();
        return response()->json("un livreur intéréssé a ajouté a cette demande.", $this-> successStatus);    
        }
    }

    /// Refuser un livreur intéréssé
    public function refuserLivreurInteresse($idDemande,$idLivreur){

      $demande=ModelDemande::find($idDemande);
      $livInt= $demande->livreurs_interesses;
      $livreurs_interesses = [];

      foreach ($livInt as $livreur) {
          if ($livreur['idLivreur'] == $idLivreur) {
              $livreur['statut']= "refusé";
          }
          array_push($livreurs_interesses, $livreur);
      }
      $demande->livreurs_interesses = $livreurs_interesses;
      $demande->save();
        return response()->json("un livreur intéréssé a refusé pour cette demande.", $this-> successStatus);    

    }


    /// Modificateur statut Demande
    public function modifierStatutDemande(Request $request,$idDemande){
            $demande = ModelDemande::find($idDemande);
            $demande->statutDemande = $request->statutDemande;
            $demande->save();
            return response()->json("la statut de demande est devenu fermé avec sucées", $this-> successStatus);    
    }

    /// Envoyer une demande privée
    public function envoyerDemandePrive(Request $request) { 
      $validator = Validator::make($request->all(), 
      [   'idClient' => 'required|string|max:255', 
          'dateLivraison' => 'required|string|max:255', 
          'heureLivraison' => 'required|string|max:255', 
          'villeDeDepart' => 'required|string|max:255',
          'adresseRecuperationLivraison' => 'required|string|max:255',
          'adresseDestinationLivraison'  => 'required|string|max:255',
          'contenu' => 'required|string|max:255',
          'nature'  => 'required|string|max:255',
          'taille'  => 'required|string|max:255',
          'poids'   => 'required|string|max:255',
          'prix'   => 'required|string|max:255',
      ]);
      if ($validator->fails()) { 
                  return response()->json(['error'=>$validator->errors()], 401);            
              }else {
              $demandePrive = ModelDemande::create([
                  'idClient' => $request->idClient,
                  'type' => "Privé",
                  'statutDemande' => "ouvert",
                  'dateLivraison' => $request->dateLivraison,
                  'heureLivraison' => $request->heureLivraison,
                  'villeDeDepart' => $request->villeDeDepart,
                  'villeDeLivraison' => $request->villeDeLivraison,
                  'adresseRecuperationLivraison' => $request->adresseRecuperationLivraison,
                  'adresseDestinationLivraison'  => $request->adresseDestinationLivraison,
                  'regionRecuperation' => $request->regionRecuperation,
                  'regionLivraison' => $request->regionLivraison,
                  'moyens_transport'=> [
                    "Voiture"=>  $request->Voiture=== 'true'? true:false,
                    "Moto"=> $request->Moto=== 'true'? true:false,
                    "Camion"=> $request->Camion=== 'true'? true:false,
                    "Pickup"=> $request->Pickup=== 'true'? true:false,
                    "Taxi"=>$request->Taxi=== 'true'? true:false,
                    "Velo"=>$request->Velo=== 'true'? true:false,
                    ],
                  'contenu' => $request->contenu,
                  'nature'  => $request->nature,
                  'taille'  => $request->taille,
                  'poids'   => $request->poids,
                  'prix'   => $request->prix,
                  'description'   => $request->description,
                  'livreur_choisi'=> [
                  'idLivreur'   => $request->idLivreur,
                  'statut'   => "en attente",
                  'prix'   => $request->prix,
                  'causeAnnulation_client'   => null,
                  'causeAnnulation_livreur'   =>null
                  ]    

              ]);
              }
      
       return response()->json("la demande de livraison privé est envoyée avec succès", $this-> successStatus);        
    }

    /// Obtenir une demande de récus par sa pièce d'identité
    public function obtenirDemandesRecusParId (Request $request,$idl) {
          
      $demande = ModelDemande::where('livreur_choisi.idLivreur', $idl)
      ->where('type','Privé')
      ->where('statutDemande', 'ouvert')
      ->orderBy('_id', 'desc')->get();
      
    
      ///// Filtres les demandes reçu de livreur
      if( $request->has('dateLivraison') &&  $request->dateLivraison!="Tout" && $request->dateLivraison!=""){
        $demande =$demande->where('dateLivraison',$request->input('dateLivraison'))->values();
      }
        if( $request->has('contenu') && $request->contenu!="null" && $request->contenu!="Tout"){
          $demande =$demande->where('contenu',$request->input('contenu'))->values();
        }
        if( $request->has('nature') && $request->nature!="null" && $request->nature!="Tout"){
          $demande =$demande->where('nature',$request->input('nature'))->values();
        }
        if( $request->has('taille') && $request->taille!="null" && $request->taille!="Tout"){
          $demande =$demande->where('taille',$request->input('taille'))->values();
        }
        if( $request->has('poids') && $request->poids!="null" && $request->poids!="Tout"){
          $demande =$demande->where('poids',$request->input('poids'))->values();
        }
        if( $request->has('regionRecuperation') && $request->regionRecuperation!="null" && $request->regionRecuperation!="Tout"){
          $demande =$demande->where('regionRecuperation',$request->input('regionRecuperation'))->values();
        }
        if( $request->has('regionLivraison') && $request->regionLivraison!="null" && $request->regionLivraison!="Tout"){
          $demande =$demande->where('regionLivraison',$request->input('regionLivraison'))->values();
        }
        if( $request->has('villeDeDepart') && $request->villeDeDepart!="null" && $request->villeDeDepart!="Tout"){
          $demande =$demande->where('villeDeDepart',$request->input('villeDeDepart'))->values();
        }
        if( $request->has('villeDeLivraison') && $request->villeDeLivraison!="null" && $request->villeDeLivraison!="Tout"){
          $demande =$demande->where('villeDeLivraison',$request->input('villeDeLivraison'))->values();
        }
        //// trie Mes demandes Client
        if(  $request->has('triePar')  && $request->triePar!="null" && $request->triePar!="Tout") {
          if ($request->triePar == "Date de livraison (croissant)") {
              $demande= $demande->sortBy('heureLivraison')->sortBy('dateLivraison')->values();
            }
            if ($request->triePar == "Date de livraison (decroissant)") {
              $demande= $demande->sortByDesc('heureLivraison')->sortByDesc('dateLivraison')->values();
            }
            if ($request->triePar == "Nombre d’étoiles (croissant)") {
              $client = ModelClient::find('noteTotal');
              $demande= $demande->sortBy($client)->values();
            }
            if ($request->triePar == "Nombre d’étoiles (decroissant)") {
              $client = ModelClient::find('noteTotal');
              $demande= $demande->sortByDesc($client)->values();
            }
            if ($request->triePar == "Prix (croissant)") {
              $demande= $demande->sortBy('prix')->values();
            }
            if ($request->triePar == "Prix (decroissant)") {
              $demande= $demande->sortByDesc('prix')->values();
            }

          }
        return response()->json($demande);
    }

    // Mise à jour demande 
    public function modifierDemande(Request $request,$idDemande) {
        $demande = ModelDemande::find($idDemande);  
        $validator = Validator::make($request->all(), 
          [    
              'dateLivraison' => 'required|string|max:255', 
              'villeDeDepart' => 'required|string|max:255',
              'adresseRecuperationLivraison' => 'required|string|max:255',
              'adresseDestinationLivraison'  => 'required|string|max:255',
              'contenu' => 'required|string|max:255',
              'nature'  => 'required|string|max:255',
              'taille'  => 'required|string|max:255',
              'poids'   => 'required|string|max:255',
          ]);
          if ($validator->fails()) { 
                      return response()->json(['error'=>$validator->errors()], 401);            
                  }else {  
        if($demande->type == "Public"){
                    $demande->dateLivraison=$request->input('dateLivraison');
                    $demande->villeDeDepart=$request->input('villeDeDepart');
                    $demande->villeDeLivraison=$request->input('villeDeLivraison');
                    $demande->adresseRecuperationLivraison=$request->input('adresseRecuperationLivraison');
                    $demande->adresseDestinationLivraison=$request->input('adresseDestinationLivraison');
                    $demande->regionRecuperation = $request->input('regionRecuperation');
                    $demande->regionLivraison = $request-> input('regionLivraison');
                    $demande->contenu   =  $request->input('contenu');
                    $demande->nature=$request->input('nature');
                    $demande->taille=$request->input('taille');
                    $demande->poids=$request->input('poids');
                    $demande->description=$request->input('description');
                    $demande->save();}
        if($demande->type == "Privé"){
                        $demande->dateLivraison=$request->input('dateLivraison');
                        $demande->villeDeDepart=$request->input('villeDeDepart');
                        $demande->villeDeLivraison=$request->input('villeDeLivraison');
                        $demande->adresseRecuperationLivraison=$request->input('adresseRecuperationLivraison');
                        $demande->adresseDestinationLivraison=$request->input('adresseDestinationLivraison');
                        $demande->regionRecuperation = $request->input('regionRecuperation');
                        $demande->regionLivraison = $request-> input('regionLivraison');
                        $demande->contenu   =  $request->input('contenu');
                        $demande->nature=$request->input('nature');
                        $demande->taille=$request->input('taille');
                        $demande->poids=$request->input('poids');
                        $demande->description=$request->input('description');
                        $demande->prix=$request->input('prix');
                        $demande->save();}                
        return response()->json("La demande  a été modifié avec succès");}

    }

    /// Supprimer la demande
    public function annulerDemande(Request $request,$idDemande){
        $demande = ModelDemande::find($idDemande);   
        $demande->delete();
        return response()->json("Supprimé avec succès");
    }

    /// Obtenir tous les livreurs intersses
    public  function obtenirLivreursInteresses(Request $request,$idDemande) {

        $demande = ModelDemande::find($idDemande);
        $livInt= $demande->livreurs_interesses;
        $livInt= collect($livInt);
        $livreur = ModelLivreur::all();
        ///// Filtres livreurs
            if( $request->has('ville') && $request->ville!="Tout" && $request->ville!="null"){
                $livreur = $livreur->where('ville',$request->input('ville'));
                $idLivreur = $livreur->pluck('_id');
                $livInt = $livInt->whereIn('idLivreur',$idLivreur)->values();
              }

            if( $request->has('region') && $request->region!="Tout" && $request->region!="null") {
              $livreur = $livreur->where('region',$request->input('region'));
              $idLivreur = $livreur->pluck('_id');
              $livInt = $livInt->whereIn('idLivreur',$idLivreur)->values();
            }  
            $note=(int) $request->noteTotal; 
            if(  $request->has('noteTotal') && $request->noteTotal!="Tout" && $request->noteTotal!="null") {
                $livreur = $livreur->where('noteTotal','>=', $note)->where('noteTotal','<', $note+1);
                $idLivreur = $livreur->pluck('_id');
                $livInt = $livInt->whereIn('idLivreur',$idLivreur)->values();
            }   
            if( $request->has('transport') && $request->transport!="Tout" && $request->transport!="null") {
                
                for($i=0;$i<1;$i++){
                    
                    $livreur = $livreur->where("moyenTransport.$i.type",$request->input('transport'));
                    $idLivreur = $livreur->pluck('_id');
                    $livInt = $livInt->whereIn('idLivreur',$idLivreur)->values();
                }}
          ///// trie livreurs
            if(  $request->has('triePar')  && $request->triePar!="null") {
                    if ($request->triePar == "Nombre d’étoiles (croissant)") {
                        $livreur = ModelLivreur::find('noteTotal');
                        $livInt = $livInt->sortBy($livreur)->values();
                      }
                    if ($request->triePar == "Nombre d’étoiles (decroissant)") {
                        $livreur = ModelLivreur::find('noteTotal');
                        $livInt = $livInt->sortByDesc($livreur)->values();
                      }
                    if ($request->triePar == "Nombre du livraison (croissant)") {
                        $livreur = ModelLivreur::find('nombreLivraisons');
                        $livInt = $livInt->sortBy($livreur)->values();
                      }
                    if ($request->triePar == "Nombre du livraison (decroissant)") {
                        $livreur = ModelLivreur::find('nombreLivraisons');
                        $livInt = $livInt->sortByDesc($livreur)->values();
                      }
                    if ($request->triePar == "Prix (croissant)") {
                      $livInt = collect($livInt)->sortBy('prix')->values();
                      }
                    if ($request->triePar == "Prix (decroissant)") {
                        $livInt = collect($livInt)->sortByDesc('prix')->values();
                      }
              
            } 
              return response()->json($livInt);
    }

}

