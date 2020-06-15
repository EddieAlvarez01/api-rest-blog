<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['index', 'show']]);
    }

    //TRAER LAS CATEGORIAS
    public function index(){
        $categories = Category::all();
        return response()->json(['code' => 200, 'categories' => $categories], 200);
    }

    //DEVOLVER 1 CATEGORIA
    public function show($id){
        $category = Category::find($id);
        if(!is_null($category)){
            $data = ['code' => 200, 'category' => $category];
        }else{
            $data = ['code' => 404, 'message' => 'No existe la categoría'];
        }
        return response()->json($data, $data['code']);
    }

    //CREAR UNA CATEGORIA
    public function store(Request $req){
        $req->validate([
            'name' => 'required|string|max:200'
        ]);
        $category = new Category();
        $category->name = $req->input('name');
        $category->save();
        if(!empty($category->id)){
            $data = ['code' => 200, 'category' => $category];
        }else{
            $data = ['code' => 500, 'message' => 'Error al crear la categoria'];
        }
        return response()->json($data, $data['code']);
    }

    //ACTUALIZAR UNA CATEGORIA
    public function update(Request $req, $id){
        $req->validate([
            'name' => 'required|string|max:200'
        ]);
        $category = Category::find($id);
        if(!is_null($category)){
            $category->name = $req->input('name');
            $category->save();
            $data = ['code' => 200, 'category' => $category];
        }else{
            $data = ['code' => 400, 'message' => 'Error: la categoría a actualizar no existe'];
        }
        return response()->json($data, $data['code']);
    }

}
