<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt-auth');
    }

    //REGISTRO DE USUARIOS
    public function register(Request $req){

        //VALIDAR DATOS
        Validator::make($req->all(), [
            'name' => ['required', 'string', 'max:200'],
            'surname' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:300', 'unique:App\User,email'],
            'password' => ['required', 'string', 'max:1000', 'confirmed'],
            'description' => ['string']
        ])->validate();
        $user = new User();
        $user->role_id = Role::where('name', 'usuario')->first()->id;
        $user->name = $req->input('name');
        $user->surname = $req->input('surname');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->description = $req->input('description');
        $user->image = '';
        $user->save();      //GUARDAR EL USUSARIO
        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user
        ], 200);
    }

    //LOGIN DE USUARIOS
    public function login(Request $req){
        $req->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        $jwtAuth = new JwtAuth();
        $data = $jwtAuth->signup($req->input('email'), $req->input('password'));
        if(!is_null($data)){
            $user = $jwtAuth->checkToken($data['data'], true);
            $user->token = $data['data'];
            return response()->json(['user' => $user], 200);
        }
        return response()->json([
            'message' => 'Error en los credenciales'
        ], 400);
    }

    //ACTUALIZAR USUARIO
    public function update(Request $req){
        $jwt = new JwtAuth();
        $auth = $jwt->checkToken($req->header('Authorization'), true);
        $req->validate([
            'name' => ['required', 'string', 'max:200'],
            'surname' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:300', 'unique:App\User,email,' . $auth->sub],
            'description' => ['string'],
            'file0' => ['image']
        ]);
        $user = User::find($auth->sub);
        $file = $req->file('file0');
        if($file){        //VERIFICAMOS QUE EL USUARIO HAYA CARGADO UNA IMAGEN PARA SUBIRLA
            $imageSplit = Storage::putFile('users', $file, 'public');       //SUBE LA IMAGEN DENTRO DE LA CARPETA USERS Y DEVUELDE UN ID UNICO
            if($user->image != ''){         //VERIFICAR SI EL USUARIO TINE UNA IMAGEN SUBIDA PARA BORRAR LA VIEJA
                Storage::disk('users')->delete($user->image);
            }
            $imageSplit = explode('/', $imageSplit);
            $user->image = $imageSplit[1];
        }
        $user->name = $req->input('name');
        $user->surname = $req->input('surname');
        $user->email = $req->input('email');
        $user->description = $req->input('description');
        $user->save();
        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user], 200);
    }

    //TRAER IMAGENES DE LOS USUARIOS
    public function getImage(Request $req){
        $jwt = new JwtAuth();
        $auth = $jwt->checkToken($req->header('Authorization'), true);
        if($auth->image != ''){
            $file = Storage::disk('users')->get($auth->image);
        }else{
            //IMAGEN POR DEFECTO
            $file = 'imagen por defecto';
        }
        return response($file, 200);
    }

    //TRAER INFORMACION DE UN USUARIO
    public function getUser($id){
        $user = User::find($id)->load('role');
        if(!is_null($user)){
            $data = ['code' => '200', 'user' => $user];
        }else{
            $data = ['code' => '404', 'message' => 'No se encontro el usuario solicitado'];
        }
        return response()->json($data, $data['code']);
    }

}
