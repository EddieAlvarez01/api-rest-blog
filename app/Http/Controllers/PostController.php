<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['index', 'show', 'getImage', 'getPostByCategory', 'getPostByUser']]);
    }

    //OBTENER TODOS LOS POSTS
    public function index(){
        $posts = Post::all()->load('category', 'user');
        return response()->json(['code' => 200, 'posts' => $posts], 200);
    }

    //OBTENER SOLO UN POST
    public function show($id){
        $post = Post::find($id);
        if(!is_null($post)){
            $post->load('category', 'user');
            $data = ['code' => 200, 'post' => $post];
        }else{
            $data = ['code' => 404, 'message' => 'El post no existe'];
        }
        return response()->json($data, $data['code']);
    }

    //CREAR UN POST
    public function store(Request $req){
        $req->validate([
            'category_id' => 'required|numeric',
            'title' => 'required|string|max:300',
            'content' => 'required|string',
            'file0' => 'image'
        ]);
        $post = new Post();
        $jwt = new JwtAuth();
        $auth = $jwt->checkToken($req->header('Authorization'), true);      //SE TRAEN LOS DATOS DEL USUARIO POR MEDIO DEL TOKEN
        $img = $req->file('file0');
        if($img){       //SI TRAE IMAGEN
            $image_path = Storage::putFile('images', $img, 'public');       //SUBE LA IMAGEN DEVOLVIENDO UN ID UNICO
            $image_path = explode('/', $image_path);            //SPLIT PARA SACAR EL NOMBRE DE LA IMAGEN
            $post->image = $image_path[1];
        }else{
            $post->image = '';
        }
        $post->user_id = $auth->sub;
        $post->category_id = $req->input('category_id');
        $post->title = $req->input('title');
        $post->content = $req->input('content');
        $post->save();                                                              //GUARDAR EL POST EN LA BD
        if(!empty($post->id)){
            $post->load('category', 'user');
            $data = ['code' => 200, 'post' => $post];
        }else{
            $data = ['code' => 500, 'message' => 'Error al crear el post'];
        }
        return response()->json($data, $data['code']);
    }

    //ACTUALIZAR UN POST
    public function update(Request $req, $id){
        $post = Post::find($id);
        if(!is_null($post)){
            $jwt = new JwtAuth();
            $auth = $jwt->checkToken($req->header('Authorization'), true);
            if($post->user_id == $auth->sub){
                $req->validate([
                    'category_id' => 'required|numeric',
                    'title' => 'required|string|max:300',
                    'content' => 'required|string',
                    'file0' => 'image'
                ]);
                $img = $req->file('file0');
                if($img){       //SI TRAE IMAGEN
                    if($post->image != ''){     //SI YA TENIA LA IMAGEN EL POST
                        Storage::disk('images')->delete($post->image);
                    }
                    $image_path = Storage::putFile('images', $img, 'public');       //SUBE LA IMAGEN DEVOLVIENDO UN ID UNICO
                    $image_path = explode('/', $image_path);            //SPLIT PARA SACAR EL NOMBRE DE LA IMAGEN
                    $post->image = $image_path[1];
                }else{
                    $post->image = '';
                }
                $post->category_id = $req->input('category_id');
                $post->title = $req->input('title');
                $post->content = $req->input('content');
                $post->save();                                                              //GUARDAR EL POST EN LA BD
                $data = ['code' => 200, 'post' => $post];
            }else{
                $data = ['code' => 403, 'message' => 'El usuario intenta editar un post que no ha creado'];
            }
        }else{
            $data = ['code' => 400, 'message' => 'Error: el post que desea actualizar no existe'];
        }
        return response()->json($data, $data['code']);
    }

    //ELIMINAR UN POST
    public function destroy(Request $req, $id){
        $post = Post::find($id);
        if(!is_null($post)){
            $jwt = new JwtAuth();
            $auth = $jwt->checkToken($req->header('Authorization'), true);
            if($post->user_id == $auth->sub){
                if($post->image != ''){         //ELIMINAR LA IMAGEN DEL POST SI LA TUVIERA
                    Storage::disk('images')->delete($post->image);
                }
                $post->delete();
                $data = ['code' => 200, 'post' => $post];
            }else{
                $data = ['code' => 403, 'message' => 'El usuario intenta eliminar un post que no ha creado'];
            }
        }else{
            $data = ['code' => 400, 'message' => 'El post ha eliminar no existe'];
        }
        return response()->json($data, $data['code']);
    }

    //SACAR LAS IMAGENES DE LOS POST
    public function getImage($filename){
        if($filename != ''){
            if(Storage::disk('images')->exists($filename)){
                return response(Storage::disk('images')->get($filename), 200);
            }
            return response(['code' => 404, 'message' => 'No existe la imagen'], 404);
        }
        return response(Storage::disk('images')->get('default-post.svg'), 200);
    }

    //POST POR CATEGORIA
    public function getPostByCategory($id){
        $posts = Post::where('category_id', $id)->get()->load('category', 'user');
        return response()->json(['code' => 200, 'posts' => $posts], 200);
    }

    //POST POR USUARIO
    public function getPostByUser($id){
        $posts = Post::where('user_id', $id)->get()->load('category', 'user');
        return response()->json(['code' => 200, 'posts' => $posts], 200);
    }

}
