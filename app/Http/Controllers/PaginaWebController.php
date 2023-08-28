<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\categorias;
use App\Models\productos;
use DB;

class PaginaWebController extends Controller
{
    public function index(){
        return view('Pagina.index');
    }

    public function indexZar(){
        $categorias = categorias::where('estatus','=',0)->get();
        $imagenes = productos::select('id')->where([['imagen','=',1],['estatus','=',0]])->inRandomOrder()->limit(6)->get();

        return view('Zar.index',compact('categorias','imagenes'));
    }

    public function nueva(){
        $categorias = categorias::where('estatus','=',0)->get();
        $imagenes = productos::select('id')->where([['imagen','=',1],['estatus','=',0]])->inRandomOrder()->limit(3)->get();

        return view('Zar.nueva',compact('categorias','imagenes'));
    }

    public function menu(){
        $idCategoria = $_REQUEST['categoria'];

        $categorias = categorias::where('estatus','=',0)->get();

        $categoria = categorias::where('id','=',$idCategoria)->get();
        $productos = productos::where([['id_categoria','=',$idCategoria],['estatus','=',0]])->get();
        $imagenes = productos::where([['id_categoria','=',$idCategoria],['estatus','=',0],['imagen','=',1]])->get();

        return view('Zar.menu',compact('categorias','categoria','productos','imagenes'));
    }


    public function menuNueva(){
        $idCategoria = $_REQUEST['categoria'];

        $categorias = categorias::where('estatus','=',0)->get();
        $categoria = categorias::where('id','=',$idCategoria)->get();
        $productos = productos::where([['id_categoria','=',$idCategoria],['estatus','=',0]])->get();

        return view('Zar.menuNueva',compact('categorias','categoria','productos'));
    }


    public function distribucion(){
        $categorias = categorias::where('estatus','=',0)->get();

        return view('Zar.distribucion',compact('categorias'));
    }
}
