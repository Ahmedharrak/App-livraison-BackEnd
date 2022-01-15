<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelDemande extends Eloquent
{  
    protected $connection = 'mongodb';
    protected $collection = 'demandes_livraison';
    protected $fillable = [
    "idClient" , 
    "type", 
    "statutDemande",
    "dateLivraison", 
    "heureLivraison",
    "villeDeDepart",
    "villeDeLivraison",
    "regionRecuperation",
    "regionLivraison",
    "adresseRecuperationLivraison",
    "adresseDestinationLivraison",
    "prix_max",//===> privé
    "contenu",
    "nature",
    "taille",
    "poids",
    "description",
     // table moyen transport
          "moyens_transport",
              "Voiture",
              "Moto",
              "Camion",
              "Pickup",
              "Taxi",
              "Velo",
     //fin     

     //table object livreurs intéressés
          "livreurs_interesses",// =====>PUBLIC
              "idLivreur",
              "prixLivreur",
              "statut",// =======> "accepté | refusé | annulé par client | annulé par livreur "
              "causeAnnulation_client",// ======>champs obligatoire si annulé
              "causeAnnulation_livreur",// ======>champs obligatoire si annulé
    //fin

    //table object livreur choisi
          "livreur_choisi",// =====>  PRIVE
              "idLivreur",
              "statut",// ======> "accepté | refusé | annulé par client | annulé par livreur "
              "prix",
              "causeAnnulation_client",// =======> champs obligatoire si annulé
              "causeAnnulation_livreur"// ======> champs obligatoire si annulé
    //fin
];

}
?>