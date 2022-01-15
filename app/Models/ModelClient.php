<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelClient extends Eloquent
{  
    protected $connection = 'mongodb';
    protected $collection = 'clients';
    protected $fillable = [
    "nom" , 
    "prenom", 
    "telephone", 
    "numeroCIN",
    "adresse",
    "ville",
    "region",
    "email",
    "nombreSignale",
    "noteTotal",
    "nombreLivraisons",
    "imageProfile",
    //table object commentaires
        "commentaires",
            "id_livreur",
            "commentaire",
            "note"
    //fin
];

}
?>