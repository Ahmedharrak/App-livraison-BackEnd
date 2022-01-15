<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelNotification extends Eloquent
{  
    protected $connection = 'mongodb';
    protected $collection = 'notifications';
    protected $fillable = [
    "idExpediteur",
    "idDestinataire" , 
    "idDemande",
    "idLivraison",
    "type",
    "message", 
    "dejaVu",
    "dateNotifications"
];

}
?>