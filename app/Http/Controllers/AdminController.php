<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\productos;
use App\Models\categorias;
use Session;
use DB;

class AdminController extends Controller
{


    // PRODUCTOS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function inicioAdmin(){
        $tabla = productos::leftJoinSub(categorias::select('id','nombre'),'c','productos.id_categoria','=','c.id')
                            ->selectRaw("productos.*,CASE WHEN estatus = 0 THEN 'ACTIVO' ELSE 'INACTIVO' END AS nombre_estatus,c.nombre AS categoria")
                            ->get();

        $categorias = categorias::select('id','nombre')->get();

        return view('Admin.index',compact('tabla','categorias'));
    }

    public function seccionPrecios(){
        $val = $_GET['val'];

        $categorias = categorias::where('id','=',$val)->select('presentacion_1','presentacion_2','presentacion_3')->get();

        if(!empty($categorias[0]->presentacion_1) && !empty($categorias[0]->presentacion_2) && !empty($categorias[0]->presentacion_3)){
            $response = "
            <label for='precio1'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1' name='precio1' autocomplete='off'>
            <label for='precio2'>PRECIO II:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio2' name='precio2' autocomplete='off'>
            <label for='precio3'>PRECIO III:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio3' name='precio3' autocomplete='off'>
            ";
        }elseif(!empty($categorias[0]->presentacion_1) && !empty($categorias[0]->presentacion_2)){
            $response = "
            <label for='precio1'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1' name='precio1' autocomplete='off'>
            <label for='precio2'>PRECIO II:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio2' name='precio2' autocomplete='off'>
            ";
        }else{
            $response = "
            <label for='precio1'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1' name='precio1' autocomplete='off'>
            ";
        }

        return $response;
    }

    public function seccionPreciosEdit(){
        $val = $_GET['val'];

        $categorias = categorias::where('id','=',$val)->select('presentacion_1','presentacion_2','presentacion_3')->get();

        if(!empty($categorias[0]->presentacion_1) && !empty($categorias[0]->presentacion_2) && !empty($categorias[0]->presentacion_3)){
            $response = "
            <label for='precio1Edit'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1Edit' name='precio1Edit' autocomplete='off'>
            <label for='precio2Edit'>PRECIO II:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio2Edit' name='precio2Edit' autocomplete='off'>
            <label for='precio3Edit'>PRECIO III:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio3Edit' name='precio3Edit' autocomplete='off'>
            ";
        }elseif(!empty($categorias[0]->presentacion_1) && !empty($categorias[0]->presentacion_2)){
            $response = "
            <label for='precio1Edit'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1Edit' name='precio1Edit' autocomplete='off'>
            <label for='precio2Edit'>PRECIO II:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio2Edit' name='precio2Edit' autocomplete='off'>
            ";
        }else{
            $response = "
            <label for='precio1Edit'>PRECIO I:</label>
            <input type='text' class='form-control inputtext pmask2' id='precio1Edit' name='precio1Edit' autocomplete='off'>
            ";
        }

        return $response;
    }

    public function saveProducto(Request $request){
        $response = array('status' => 0,'msg' => '');

        $producto = $request->nombre_producto;
        $descripcion = $request->descripcion;
        $presentacion = $request->presentacion;
        $categoria = $request->categoria;

        $precio1 = isset($request->precio1) ? str_replace(',','',$request->precio1) : null;
        $precio2 = isset($request->precio2) ? str_replace(',','',$request->precio2) : null;
        $precio3 = isset($request->precio3) ? str_replace(',','',$request->precio3) : null;

        $response = noVacio($producto,'NOMBRE',$response);
        $response = noVacio($categoria,'CATEGORIA',$response);

        $imagen = $request->file('imagen');

        if($response['status'] != '1'){
            $buscar = productos::where([['nombre','LIKE',$producto],['descripcion','LIKE',$descripcion],['id_categoria','=',$categoria]])->select('id')->first();

            if($buscar){
                $response['status'] = '1';
                $response['msg'] = 'YA EXISTE UN PRODUCTO CON ESE NOMBRE';
            }

            if($response['status'] != '1'){
                DB::connection('mysql')->table('productos')->insert([
                    'nombre' => $producto,
                    'descripcion' => $descripcion,
                    'presentacion' => $presentacion,
                    'estatus' => 0,
                    'precio1' => $precio1,
                    'precio2' => $precio2,
                    'precio3' => $precio3,
                    'id_categoria' => $categoria
                ]);

                if($imagen != null){
                    $nextid = DB::getPdo()->lastInsertId();

                    DB::connection('mysql')->update("UPDATE productos SET imagen = 1 WHERE id = '$nextid'");
                    $filename = $nextid.'.png';
                    \Storage::disk('carta')->put($filename, \File::get($imagen));
                }
            }
        }

        echo json_encode($response);
    }

    public function modificarProducto(){
        $id = $_GET['id'];

        $registro = productos::where('id','=',$id)->first();

        $cat = $registro->id_categoria;
        $categoria_uso = categorias::where('id','=',$cat)->select('presentacion_1','presentacion_2','presentacion_3')->get();

        $categoriasEdit = categorias::select('id','nombre')->get();

        return view('Admin.modificarProducto',compact('registro','categoriasEdit','categoria_uso'));
    }

    public function updateProducto(Request $request){
        $response = array('status' => 0,'msg' => '');

        $id = $request->id_reg;
        $producto = $request->nombre_productoEdit;
        $descripcion = $request->descripcionEdit;
        $presentacion = $request->presentacionEdit;
        $categoria = $request->categoriaEdit;

        $precio1 = isset($request->precio1Edit) ? str_replace(',','',"'".$request->precio1Edit."'") : "null";
        $precio2 = isset($request->precio2Edit) ? str_replace(',','',"'".$request->precio2Edit."'") : "null";
        $precio3 = isset($request->precio3Edit) ? str_replace(',','',"'".$request->precio3Edit."'") : "null";

        $response = noVacio($producto,'NOMBRE',$response);
        $response = noVacio($categoria,'CATEGORIA',$response);

        $imagen = $request->file('imagenEdit');

        if($response['status'] != '1'){
            $buscar = productos::where([['nombre','LIKE',$producto],['descripcion','LIKE',$descripcion],['id_categoria','=',$categoria],['id','!=',$id]])->select('id')->first();

            if($buscar){
                $response['status'] = '1';
                $response['msg'] = 'YA EXISTE UN PRODUCTO CON ESE NOMBRE';
            }

            if($response['status'] != '1'){
                DB::connection('mysql')->update("UPDATE productos SET
                    nombre = '$producto',
                    descripcion = '$descripcion',
                    presentacion = '$presentacion',
                    precio1 = $precio1,
                    precio2 = $precio2,
                    precio3 = $precio3,
                    id_categoria = '$categoria'
                    WHERE id = '$id'
                ");

                if($imagen != null){
                    DB::connection('mysql')->update("UPDATE productos SET imagen = 1 WHERE id = '$id'");
                    $filename = $id.'.png';
                    \Storage::disk('carta')->put($filename, \File::get($imagen));
                }
            }
        }

        echo json_encode($response);
    }

    public function operacionesProducto(){
        $id = $_GET['id'];
        $t = $_GET['t'];

        if($t == 1){
            DB::connection('mysql')->update("UPDATE productos SET estatus = 1 WHERE id = '$id'");
        }elseif($t == 2){
            DB::connection('mysql')->update("UPDATE productos SET estatus = 0 WHERE id = '$id'");
        }elseif($t == 3){
            DB::connection('mysql')->delete("DELETE FROM productos WHERE id = '$id'");
        }

        return 0;
    }

    public function showImagen(){
        $id = $_GET['id'].'.png';

            echo "
                    <br>
                    <div align='center'>
                        <span>
                                <image src='./../public/img/carta/{$id}' style='cursor: pointer; max-width:90%; max-height:580px;'>
                        </span>
                    </div><br>
                    <div class='col-md-12 col-xs-4 text-center'>
              <input type='button' class='btn btn-secondary btn-sm' onclick='hideimage()' value='CERRAR'>
            </div><br>
            ";
    }
    // /PRODUCTOS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    // CATEGORIAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function categorias(){
        $tabla = categorias::selectRaw("*,CASE WHEN estatus = 0 THEN 'ACTIVO' ELSE 'INACTIVO' END AS nombre_estatus")->get();

        return view('Categorias.index',compact('tabla'));
    }

    public function saveCategoria(Request $request){
        $response = array('status' => 0,'msg' => '');

        $categoria = $request->nombre_categoria;
        $presentacion1 = $request->presentacion1;
        $presentacion1D = $request->presentacion1_descr;
        $presentacion2 = $request->presentacion2;
        $presentacion2D = $request->presentacion2_descr;
        $presentacion3 = $request->presentacion3;
        $presentacion3D = $request->presentacion3_descr;

        $response = noVacio($categoria,'NOMBRE',$response);


        if($response['status'] != '1'){
            $buscar = categorias::where('nombre','LIKE',$categoria)->select('id')->first();

            if($buscar){
                $response['status'] = '1';
                $response['msg'] = 'YA EXISTE UNA CATEGORIA CON ESE NOMBRE';
            }

            if($response['status'] != '1'){
                DB::connection('mysql')->table('categorias')->insert([
                    'nombre' => $categoria,
                    'estatus' => 0,
                    'presentacion_1' => $presentacion1,
                    'presentacion_1_descr' => $presentacion1D,
                    'presentacion_2' => $presentacion2,
                    'presentacion_2_descr' => $presentacion2D,
                    'presentacion_3' => $presentacion3,
                    'presentacion_3_descr' => $presentacion3D
                ]);

                $imagen = $request->file('imagen');
                if($imagen != null){
                    $nextid = DB::getPdo()->lastInsertId();

                    $filename = $nextid.'.jpg';
                    \Storage::disk('categorias')->put($filename, \File::get($imagen));
                }
            }
        }

        echo json_encode($response);
    }

    public function modificarCategoria(){
        $id = $_GET['id'];

        $registro = categorias::where('id','=',$id)->get();

        return view('Categorias.modificarCategoria',compact('registro'));
    }

    public function updateCategoria(Request $request){
        $response = array('status' => 0,'msg' => '');

        $id = $request->id_reg;
        $categoria = $request->nombre_categoriaEditar;
        $presentacion1 = $request->presentacion1Editar;
        $presentacion1D = $request->presentacion1_descrEditar;
        $presentacion2 = $request->presentacion2Editar;
        $presentacion2D = $request->presentacion2_descrEditar;
        $presentacion3 = $request->presentacion3Editar;
        $presentacion3D = $request->presentacion3_descrEditar;

        $response = noVacio($categoria,'NOMBRE',$response);

        if($response['status'] != '1'){
            $buscar = categorias::where([['nombre','LIKE',$categoria],['id','!=',$id]])->select('id')->first();

            if($buscar){
                $response['status'] = '1';
                $response['msg'] = 'YA EXISTE UNA CATEGORIA CON ESE NOMBRE';
            }

            if($response['status'] != '1'){
                DB::connection('mysql')->update("UPDATE categorias SET
                    nombre = '$categoria',
                    presentacion_1 = '$presentacion1',
                    presentacion_1_descr = '$presentacion1D',
                    presentacion_2 = '$presentacion2',
                    presentacion_2_descr = '$presentacion2D',
                    presentacion_3 = '$presentacion3',
                    presentacion_3_descr = '$presentacion3D'
                    WHERE id = '$id'
                ");

                $imagen = $request->file('imagen');
                if($imagen != null){
                    $filename = $id.'.jpg';
                    \Storage::disk('categorias')->put($filename, \File::get($imagen));
                }
            }
        }

        echo json_encode($response);
    }

    public function operacionesCategoria(){
        $id = $_GET['id'];
        $t = $_GET['t'];

        if($t == 1){
            DB::connection('mysql')->update("UPDATE categorias SET estatus = 1 WHERE id = '$id'");
        }elseif($t == 2){
            DB::connection('mysql')->update("UPDATE categorias SET estatus = 0 WHERE id = '$id'");
        }elseif($t == 3){
            DB::connection('mysql')->delete("DELETE FROM categorias WHERE id = '$id'");
        }

        return 0;
    }
    // /CATEGORIAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // CATEGORIAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function promociones(){
        $tabla = DB::connection('mysql')->select("SELECT *,CASE WHEN estatus = 0 THEN 'ACTIVO' ELSE 'INACTIVO' END AS nombre_estatus FROM promociones");

        return view('Promociones.index',compact('tabla'));
    }

    public function savePromociones(Request $request){
        $response = array('status' => 0,'msg' => '');

        $date = Date('Y-m-d');
        $promo = $request->nombre_promo;

        $response = noVacio($promo,'NOMBRE',$response);

        $imagen = $request->file('imagen');

        if($imagen == null){
            $response['status'] = '1';
            $response['msg'] = 'NECESITAS SUBIR UN ARCHIVO';
        }

        if($response['status'] != '1'){
            $buscar = DB::connection('mysql')->select("SELECT id FROM promociones WHERE nombre LIKE '$promo'");

            if($buscar != null){
                $response['status'] = '1';
                $response['msg'] = 'YA EXISTE UN PRODUCTO CON ESE NOMBRE';
            }

            if($response['status'] != '1'){
                DB::connection('mysql')->table('promociones')->insert([
                    'nombre' => $promo,
                    'estatus' => 0,
                    'fecha_crea' => $date
                ]);

                if($imagen != null){
                    $nextid = DB::getPdo()->lastInsertId();

                    $filename = $nextid.'.png';
                    \Storage::disk('promociones')->put($filename, \File::get($imagen));

                    DB::connection('mysql')->update("UPDATE promociones SET archivo = '$filename' WHERE id = '$nextid'");
                }

            }
        }

        echo json_encode($response);
    }

    public function operacionesPromociones(){
        $id = $_GET['id'];
        $t = $_GET['t'];

        if($t == 1){
            DB::connection('mysql')->update("UPDATE promociones SET estatus = 1 WHERE id = '$id'");
        }elseif($t == 2){
            DB::connection('mysql')->update("UPDATE promociones SET estatus = 0 WHERE id = '$id'");
        }elseif($t == 3){
            DB::connection('mysql')->delete("DELETE FROM promociones WHERE id = '$id'");
        }

        return 0;
    }
    // /CATEGORIAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
