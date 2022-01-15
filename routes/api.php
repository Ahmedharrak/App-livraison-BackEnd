<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers;
use App\User;
use App\Models\ModelLivreur;
use App\Models\ModelDemande;
use App\Models\ModelClient;
use App\Models\ModelLivraison;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use Illuminate\Support\Facades\Crypt;

/////////////////////  UserController /////////////////////////////////

//Se connecter 
Route::post('connecter', 'UserController@connecter'); 

//S'inscrire
Route::post('inscrire', 'UserController@inscrire'); 

//Se déconnecter
Route::post('deconnecter','UserController@deconnecter');

//Mettre à jour le mot de passe
Route::post('modifierMotDePasse/{id}', 'UserController@modifierMotDePasse');

//Obtenir les détails de l'utilisateur connecté
Route::get('details', 'UserController@details'); 

//Obtenir tous les utilisateurs
Route::get('utilisateurs', function () {
    return User::all();
});

////Obtenir l'élément de l'utilisateur  par identifiant
Route::get('elementUtilisateur/{id}/{element}', 'UserController@obtenirElementUtilisateur'); 

//Obtenir toutes les informations utilisateur
Route::get('informationUtilisateur/{email}', 'UserController@obtenirInformationUtilisateur'); 

//Obtenir l'utilisateur par son identifiant
Route::get('utilisateur/{id}', function ($id) {
    return User::find($id);
})->middleware('auth:api'); 

////Obtenir le rôle
Route::get('roleUtilisateur/{id}', 'UserController@obtenirRoleUtilisateur'); 

//Supprimer l'utilisateur par son identifiant
Route::delete('supprimerUtilisateur/{id}','UserController@supprimerUtilisateur');

//Mettre à jour les données de l'utilisateur par son identifiant
Route::put('modifierUtilisateur/{id}','UserController@modifierUtilisateur');

//Mettre à jour les données du client par son identifiant
Route::put('modifierClient/{id}','UserController@modifierClient');

//Mettre à jour les données du livreur par son identifiant
Route::put('modifierLivreur/{id}','UserController@modifierLivreur');

/// Obtenir  le mot de passe utilisateur
Route::get('obtenirPassword/{id}', 'UserController@obtenirPassword'); 

//Mettre à jour le mot de passe oublie
Route::post('reinitialiseMotDePasse/{id}', 'UserController@reinitialiseMotDePasse');

////Obtenir le id de l'utilisateur
Route::get('idUtilisateur/{email}', 'UserController@obtenirIdUtilisateur'); 

////////////////////////  ClientController  /////////////////////////////

////Obtenir l'élément client par identifiant
Route::get('elementClient/{id}/{element}', 'ClientController@obtenirElementClient'); 

//Obtenir son identifiant client
Route::get('client/{id}', 'ClientController@obtenirClient');

//Obtenir le client par son identifiant utilisateur
Route::get('informationClient/{id}', 'ClientController@obtenirInformationClient');

//Ajouter un client de profil photo
Route::put('ajouterPhotoProfile/{userId}', 'ClientController@ajouterPhotoProfile'); 

//Signaler un client
Route::put('signalerClient/{idClient}', 'ClientController@signalerClient');

//Obtenir tous les clients
Route::get('clients', function () {
    return ModelClient::all();
});

//Obtenir des Commentaires dans client
Route::put('ajouterCommentaireClient/{idClient}','ClientController@ajouterCommentaireClient' );

/////////////////////////////  LivreurController  ////////////////////////////

//Obtenez tous les livreurs
Route::get('livreurs', function () {
    return ModelLivreur::all();
});

//Obtenez tous les livreurs
Route::post('livreurs', 'LivreurController@obtenirTousLivreurs');

////Obtenir l'élément livreur par identifiant
Route::get('elementLivreur/{id}/{element}', 'LivreurController@obtenirElementLivreur'); 

//Obtenir le client par ID utilisateur
Route::get('informationLivreur/{id}', 'LivreurController@obtenirInformationLivreur');

//Obtenez livreur sa pièce d'identité
Route::get('livreur/{id}', 'LivreurController@obtenirLivreur');

//ajouter photo profil livreur
Route::put('ajouterPhotoProfileLivreur/{userId}', 'LivreurController@ajouterPhotoProfileLivreur'); 

//Signaler un livreur
Route::put('signalerLivreur/{idLivreur}', 'LivreurController@signalerLivreur');

//Modifier enService un livreur
Route::put('modifierEnService/{idLivreur}', 'LivreurController@modifierEnService');

//Ajouter un Moyen de Transport
Route::put('ajouterMoyenTransport/{idLivreur}', 'LivreurController@ajouterMoyenTransport'); 


//Obtenir des Commentaires dans livreur
Route::put('ajouterCommentaireLivreur/{idLivreur}','LivreurController@ajouterCommentaireLivreur' );

///////////////////////  DemandeController  //////////////////////

//Publier une demande publique
Route::post('publierDemande', 'DemandeController@publierDemande'); 

//Obtenez toutes les demandes
Route::post('demandespubliques', 'DemandeController@obtenirTousDemandesPublic'); 

//Obtenez toutes les demandes privées
Route::get('demandesprivee', 'DemandeController@getAllDemandesPrivee'); 

//Obtenir des demandes par identifiant client
Route::post('demandesClient/{idc}','DemandeController@obtenirDemandesClientParId' ); 

//Obtenir une demande par sa pièce d'identité
Route::get('demande/{id}', function ($id) {
    return ModelDemande::find($id);
});

//Obtenir une demande de récus par sa pièce d'identité
Route::post('demandesReçus/{idl}', 'DemandeController@obtenirDemandesRecusParId' );//->middleware('auth:api');

//Ajouter livreur interessee
Route::put('ajouterLivreurInteresse/{idDemande}', 'DemandeController@ajouterLivreurInteresse'); 

//Modificateur statut Demande
Route::put('modifierStatutDemande/{DemandeId}', 'DemandeController@modifierStatutDemande'); 

//Refuser un livreur intéréssé
Route::put('refuserLivreurInteresse/{idDemade}/{idLivreur}','DemandeController@refuserLivreurInteresse');//->middleware('auth:api');

//Envoyer une demande privée
Route::post('envoyerDemandePrive', 'DemandeController@envoyerDemandePrive');

/////Mise à jour demande 
Route::put('modifierDemande/{idDemande}','DemandeController@modifierDemande');

////Supprimer la demande
Route::delete('annulerDemande/{idDemande}','DemandeController@annulerDemande');

////Obtenir tous les livreurs intersses
Route::post('obtenirLivreursInteresses/{id}', 'DemandeController@obtenirLivreursInteresses');


///////////////////////// LivraisonController  ////////////////////////////////

//Ajouter Livraison(la demande devient livraison)
Route::post('ajouterLivraiosn', 'LivraisonController@ajouterLivraiosn'); 

//Le client reçu la livraison
Route::post('livraisonReçu/{idLivraison}', 'LivraisonController@livraisonReçu'); 

//Modifier le statut de livraison
Route::post('modifierStatutLivraison/{idLivraison}/{statut}', 'LivraisonController@modifierStatutLivraison');

//Ajouter un client de retour
Route::post('ajouterRetroactionClient/{idLivraison}', 'LivraisonController@ajouterRetroactionClient'); 

//Le livreur livré la livraison
Route::post('livraisonLivré/{idLivraison}', 'LivraisonController@livraisonLivré'); 

//Ajouter un livreur de feed-back
Route::post('ajouterRetroactionLivreur/{idLivraison}', 'LivraisonController@ajouterRetroactionLivreur'); 

//Obtenez les livraisons par id client 
Route::post('livraisonsClient/{idc}','LivraisonController@obtenirLivraisonsParIdClient' ); 

//Obtenez les livraisons par id livreur
Route::post('livraisonsLivreur/{idl}','LivraisonController@obtenirLivraisonsParIdLivreur' ); 
    
//Obtenir les historiques par identifiant client
Route::post('HistoriquesClient/{idc}','LivraisonController@obtenirHistoriquesParIdClient' ); 

//Obtenir les Historiques par id livreur
Route::post('HistoriquesLivreur/{idl}','LivraisonController@obtenirHistoriquesParIdLivreur' ); 

//Obtenir des Commentaires livreur
Route::get('commentairesLivreur/{idLivreur}','LivraisonController@obtenirCommentairessParIdLivreur' );

///////////////////////// NotificationController ////////////////////////////////

//Recevoir des notifications par identifiant d'utilisateur
Route::get('notifications/{idUser}','NotificationController@obtenirNotificationsParId' );

//Ajouter une notification
Route::post('ajouterNotification', 'NotificationController@ajouterNotification');

//Recevoir des notifications par identifiant d'utilisateur
Route::get('notificationsPasVu/{idUser}','NotificationController@obtenirNotificationsPasVu' );

//Modifier le vu de notifications
Route::put('modifierVuNotificatios/{idUser}','NotificationController@modifierVuNotificatios' );


/////////Envoyer code de verification in email

///// Gmail : deliveryapplgv@gmail.com
///// Mot de passe de gmail : softcentrepfe

Route::get('sendmail/{to}/{subject}', function (Request $request, $to, $subject) {

    function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    $code = generateRandomString();

    Mail::raw($code, function ($message) use ($to, $subject) {
        $message->from('deliveryapplgv@gmail.com', 'DeliveryApp');
        $message->to($to)->subject($subject);
    });

    return response()->json("$code");
});


