<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelLivreur extends Eloquent
{  
    protected $connection = 'mongodb';
    protected $collection = 'livreurs';
    protected $fillable = [
        "nom",
        "prenom",
        "adresse",
        "ville",
        "region",
        "telephone",
        "numeroCIN",
        "email",
        "enService",
        "imageProfile",
        "joursDeService",
        //jours de semaines
        "lundi",
        "mardi",
        "mercredi",
        "jeudi",
        "vendredi",
        "samedi",
        "dimanche",
        //fin

        "imageCIN",

        //table objects MoyenTransport
           "moyenTransport",
              "type",       
              "immatriculation",
              "imagePermis",   
              "imageCarteGrise",
        //fin

        "nombreSignale",
        "noteTotal",
        "nombreLivraisons",

        //table object commentaires
           "commentaires",
              "id_client",
              "commentaire",
              "note"
        //fin
];
}



?>