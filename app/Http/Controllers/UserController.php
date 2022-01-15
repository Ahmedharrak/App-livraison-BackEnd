<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User;
use App\Models\ModelClient;
use App\Models\ModelLivreur;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller 
{
    public $successStatus = 200;

    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function connecter(){ 
        $dataAttempt = array(
            'email' => request('login'),
            'password' => request('password')
        );
        // $dataAttempt1 = array(
        //     'telephone' => request('login'),
        //     'password' => Crypt::encrypt(request('password'))
        // );


        if(Auth::attempt($dataAttempt)  ){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken($user->email."-".now()); 
            return response()->json(['success' => $success,'user' => $user], $this-> successStatus);
        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function inscrire(Request $request) { 
        $validator = Validator::make($request->all(), 
        [   'email' => 'required|string|email|unique:users', 
            'telephone'=> 'required|min:9|unique:users',
            'password' => 'required',
            'role' => 'required',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'numeroCIN' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'noteTotal' => 'double',
        ]);
        if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }
                if($request->role =="client"){
                $client = ModelClient::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'telephone' => $request->telephone,
                    'email' => $request->email,
                    'numeroCIN' => $request->numeroCIN,
                    'adresse' => $request->adresse,
                    'ville' => $request->ville,
                    'region' => $request->region,
                    'noteTotal' => 0.0,
                    'nombreSignale'=> 0,
                    'nombreLivraisons'=> 0,
                    'imageProfile'=>$request->imageProfile,
                ]);
                $user  = User::create([
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'password'=> bcrypt($request->password),
                    'role'=> "client",
                    'idUser'=>$client->id
                ]);}
                if($request->role =="livreur"){ 
                    $Livreur = ModelLivreur::create([
                        'nom' => $request->nom,
                        'prenom' => $request->prenom,
                        'telephone' => $request->telephone,
                        'email' => $request->email,
                        'numeroCIN' => $request->numeroCIN,
                        'adresse' => $request->adresse,
                        'ville' => $request->ville,
                        'region' => $request->region,
                        'enService' =>true,
                        'nombreLivraisons'=>0,
                        'joursDeService'=> [
                        'lundi'=>   $request->lundi=== 'true'? true:false,
                        'mardi'=> $request->mardi=== 'true'? true:false,
                        'mercredi'=>   $request->mercredi=== 'true'? true:false,
                        'jeudi'=>  $request->jeudi=== 'true'? true:false,
                        'vendredi'=>  $request->vendredi=== 'true'? true:false,
                        'samedi'=>  $request->samedi=== 'true'? true:false,
                        'dimanche'=> $request->dimanche=== 'true'? true:false,
                        ],
                        'nombreSignale'=> 0,
                        'noteTotal' => 0.0,
                        'imageCIN'=>$request->imageCIN=== "null"? Null:$request->imageCIN,
                        'imageProfile'=>$request->imageProfile,
                        'commentaires' => [[
                            'id_client'=>$request->id_client,
                            'note'=>$request->note,
                            'commentaire'=>$request->commentaire, ]],
                        'moyenTransport' => [[
                                           'immatriculation'=> $request->immatriculation, 
                                           'type'=> $request->type, 
                                           'imagePermis'    => $request->imagePermis=== "null"? Null:$request->imagePermis, 
                                           'imageCarteGrise'=> $request->imageCarteGrise=== "null"? Null:$request->imageCarteGrise]]
                        ]);
                    $user  = User::create([
                        'email' => $request->email,
                        'telephone' => $request->telephone,
                        'password'=> bcrypt($request->password),
                        'role'=> "livreur",
                        'idUser'=>$Livreur->id
                    ]);
                }
                $success['token'] =  $user->createToken($user->email."-".now());
                return response()->json(['success' => $success,'user' => $user], $this-> successStatus); 
    }

    
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function details() { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */

    public function deconnecter(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */

    public function utilisateur(Request $request){
        return response()->json($request->user());
    }

    /// Mettre à jour l'utilisateur
    function modifierUtilisateur(Request $request, $id) {
        $usr = User::find($id);
        if ($request->has('email')) {
            $validator = Validator::make($request->all(), [ 
                'email' => 'string|email|unique:users' ]);
             if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }
            else {  
                if($usr->role == "client"){
                    $client = ModelClient::find($usr->idUser);
                    $client->email=$request->input('email');
                    $client->save();
                   }else{
                    $livreur = ModelLivreur::find($usr->idUser);
                    $livreur->email=$request->input('email');
                    $livreur->save();
                } 
                $usr->email = $request->input('email');                    
            }
            
        }
        if ($request->has('telephone')) {
            $validator = Validator::make($request->all(), [ 
                'telephone' => 'unique:users' ]);
             if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);   
             }         
            else {
                if($usr->role == "client"){
                    $client = ModelClient::find($usr->idUser);
                    $client->telephone=$request->input('telephone');
                    $client->save();
                   }else{
                    $livreur = ModelLivreur::find($usr->idUser);
                    $livreur->telephone=$request->input('telephone');
                    $livreur->save();
                } 
                $usr->telephone = $request->input('telephone');}
        }
        if ($request->has('password')) {
            $usr->password = bcrypt($request->input('password'));
        }
        $usr->save();
            return response()->json("Modifié avec succès");
    }

    /// Mettre à jour l'utilisateur client
    function modifierClient(Request $request, $id) {
        $user = User::find($id);
        $iduser=$user->idUser;
        $client=ModelClient::find($iduser);
        $validator = Validator::make($request->all(), [ 
            'telephone'=> 'required|min:9',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'numeroCIN' => 'required|string|max:255',
            'adresse' => 'string|max:255',
            'ville' => 'required|string|max:255', ]);
        if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
        else { 
            $client->nom     =  $request->input('nom');
            $client->prenom  =  $request->input('prenom');
            $client->numeroCIN     =  $request->input('numeroCIN');
            $client->ville   =  $request->input('ville');
            $client->adresse =  $request->input('adresse');
            $client->region =  $request->input('region');
            $client->imageProfile   =  $request->input('imageProfile');
            $client->telephone   =  $request->input('telephone');
            $client->save();
            if($user->telephone!=$request->input('telephone')){
            $user->telephone=$request->input('telephone');}
            }
        $user->save();
        return response()->json("Le client a été modifié avec succès");
    } 

    /// Mettre à jour l'utilisateur livreur
    function modifierLivreur(Request $request, $id) {
        $user = User::find($id);
        $iduser=$user->idUser;
        $livreur = ModelLivreur::find($iduser);    
        $validator = Validator::make($request->all(), [ 
                'telephone'=> 'min:9|required',
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'numeroCIN' => 'required|string|max:255',
                'adresse' => 'string|max:255',
                'ville' => 'required|string|max:255', 
        ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
                }
            else {  
                $livreur->telephone="telephone";
                $user->telephone="telephone";
                $livreur->nom=$request->input('nom');
                $livreur->prenom=$request->input('prenom');
                $livreur->numeroCIN=$request->input('numeroCIN')=== 'null'? null:$request->input('numeroCIN');
                $livreur->adresse=$request->input('adresse');
                $livreur->region =   $request->input('region');
                $livreur->telephone   =  $request->input('telephone');
                $livreur->ville = $request->input('ville');
                $livreur->joursDeService = [
                    'lundi'=> $request->input('lundi')=== 'true'? true:false,
                    'mardi'=> $request->input('mardi')=== 'true'? true:false,
                    'mercredi'=> $request->input('mercredi')=== 'true'? true:false,
                    'jeudi'=> $request->input('jeudi')=== 'true'? true:false,
                    'vendredi'=> $request->input('vendredi')=== 'true'? true:false,
                    'samedi'=> $request->input('samedi')=== 'true'? true:false,
                    'dimanche'=> $request->input('dimanche')=== 'true'? true:false,
                ];
                $livreur->imageCIN=$request->input('imageCIN')=== "null"? null:$request->input('imageCIN');
                $livreur->imageProfile=$request->input('imageProfile');
                $livreur->moyenTransport = [[
                    'immatriculation'=>$request->input('immatriculation'),
                    'type'=>$request->input('type'),
                    'imagePermis'=>$request->input('imagePermis')=== 'null'? null:$request->input('imagePermis'),
                    'imageCarteGrise'=>$request->input('imageCarteGrise')=== 'null'? null:$request->input('imageCarteGrise')
                ]];
                $livreur->save();
                $user->telephone=$request->input('telephone');
                $user->save();
        return response()->json("Le livreur a été modifié avec succès");
        }
    }

    /// Supprimer l'utilisateur
    function supprimerUtilisateur($id) {
        $usr = User::find($id);
        $iduser=$usr->idUser;
        $role=$usr->role;
        $usr->delete();
        if($role=="client"){
            $client = ModelClient::find($iduser);
            $client->delete();
        }
        else{
            $livreur = ModelLivreur::find($iduser);
            $livreur->delete();
        }
        return response()->json("Supprimé avec succès");
    }

    /// Obtenir le rôle utilisateur
    function obtenirRoleUtilisateur($email){
        $usr = User::where('email',$email)->first();
        $role = $usr->role;
        return response()->json("$role");
    }

    /// Recevoir les informations d'un utilisateur par email
    function obtenirInformationUtilisateur($id){
        //  $usr = User::where('email',$email)->first();
        $usr = User::find($id);
        return response()->json($usr);
    }
    
    /// Obtenir l'utilisateur de l'élément
    function obtenirElementUtilisateur($id,$elemnt){
        $user = User::find($id);
        $monElementUser = $user->$elemnt;
        return response()->json("$monElementUser");
    }

    /// Mettre à jour le mot de passe utilisateur
    function modifierMotDePasse(Request $request, $id) {
        $usr = User::find($id);
        $validator = Validator::make($request->all(), [ 
            'old_password'     => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password', 
        ]);
         if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
        if(Hash::check($request->old_password,$usr->password)){
                if($request->input('new_password') ==  $request->input('confirm_password')){
                    $usr->password = bcrypt($request->input('new_password'));   
                    $usr->save();
                    return response()->json("Le mot de passe a été changé avec succès");
                }
            else{
                    return response()->json("Le nouveau mot de passe doit être le même confirmer le mot de passe !");
                } 
        }
        else{ 
            return response()->json(" Ce mot de passe est incorrect !");
        }       
    }

    /// Obtenir  le mot de passe utilisateur
    function obtenirPassword(Request $request, $id) {
        $usr = User::find($id);
        $password = $usr->password;   
        return response()->json($password);
                        
    }

    /// Obtenir le rôle utilisateur
    function obtenirIdUtilisateur($email){
            $usr = User::where('email',$email)->first();
        $idUtilisateur = $usr->_id;
        return response()->json("$idUtilisateur");
    }

    /// Mettre à jour le mot de passe oublie utilisateur
    function reinitialiseMotDePasse(Request $request, $id) {

        $usr = User::find($id);
        $validator = Validator::make($request->all(), [ 
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password', 
        ]);
         if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
        
                if($request->input('new_password') ==  $request->input('confirm_password')){
                    $usr->password = bcrypt($request->input('new_password'));   
                    $usr->save();
                    return response()->json("Le mot de passe a été changé avec succès");
                }
            else{
                    return response()->json("Le nouveau mot de passe doit être le même confirmer le mot de passe !");
                } 
        
              
    }

}