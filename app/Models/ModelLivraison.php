<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelLivraison extends Eloquent
{  
    protected $connection = 'mongodb';
    protected $collection = 'livraisons';
    protected $fillable = [
    "idLivreur" , 
    "idClient" , 
    "idDemande", 
    "prix",
    "statutLivraison", // ======> en attente | en cours

     //table feedback_livreur
          "feedbackLivreur",
              "confirmationLivraison", // ====> livré
              "note",
              "justification", // =====> Si note <= 2
              "commentaire",
    //fin

     //table feedback_client
     "feedbackClient",
     "confirmationLivraison", // ====> reçu
     "note",
     "justification", // =====> Si note <= 2
     "commentaire",
//fin
];

}
?>