<?php
namespace Controllers;
use Models\UserModel;
use Models\ListingModel;

class UserController extends Controller
{
    /**
     * Afficher la page de connexion.
     */
    public function index()
    {
        $data = [
            "title" => "Connection",
            "h1" => "Connection",
        ];

        $this->render("connection.html.twig", $data);
    }

    /**
     * Afficher le formulaire d'inscription et traiter l'inscription.
     */
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($email) && !empty($password)) {
                try {
                    $userModel = new UserModel($this->db);
                    $issuccess = $userModel->register($email, $password, $username);

                    if ($issuccess) {
                        $_SESSION["mail"] = $email;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = 'user';
                        header('Location: /admin');
                        exit;
                    } else {
                        $error = 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.';
                    }
                } catch (\PDOException $e) {
                    error_log("Erreur d'inscription : " . $e->getMessage());
                    $error = 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.';
                }
            } else {
                $error = 'Tous les champs sont obligatoires.';
            }
        }

        $this->render('inscription.html.twig', [
            'error' => $error ?? null,
            'title' => 'Inscription',
            'h1' => 'Créer un compte'
        ]);
    }


    public function connection(){

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
           
            $email = $_POST['mail'] ?? '';
            $password = $_POST['pass'] ?? '';
            
           
           $userModel =  new UserModel($this->db);
           
            if(!empty($email) && !empty($password)){
                 try{

                    $connected = $userModel->connection($email,$password);
                   
                    if($connected == 1){
                        
                        $user = $userModel->get_user_by_mail($email);
                        $_SESSION['id'] =$user->id;
                        $_SESSION["mail"] = $user->mail;
                        $_SESSION["username"] = $user->username;
                        $_SESSION["role"] = $user->role;
                        $_SESSION["user_id"] = $user->id;
                        header('Location:/admin');
                    }
                    else{
                        
                        header('Location: /connection');
                    }

                 }catch(\PDOException $e){
                        $userModel = new UserModel($this->db);
                 }
            }
        }
        

    }

    public function deconnection(){
        session_unset();
        session_destroy();
        header('Location: /');
    }
    public function admin(){
      
           
            $data = [
                "mail" => $_SESSION["mail"],
                "username"=> $_SESSION["username"],
                "role" => $_SESSION["role"],
                "h1" => "Admin",
           
            ];
                
            $this->render("admin.html.twig",$data);
                
    }
}
