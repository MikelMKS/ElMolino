<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ProductosController extends Controller
{
    public function __construct(){
        $this->tittle = "PRODUCTOS";
    }

    public function index() {
        $productos = DB::select("SELECT * 
        FROM productos AS p
        LEFT JOIN(SELECT idProducto,nombreGrupo
                            FROM grupos_productos AS gp
                            LEFT JOIN(SELECT id,nombre AS nombreGrupo FROM grupos) AS g ON gp.idGrupo = g.id
        ) AS g ON p.id = g.idProducto
        ");

        return view('Productos.index',compact('productos'))->with(['tittle' => $this->tittle]);
    }

    public function agregarProducto() {
        $grupos = DB::select("SELECT id,nombre,CASE WHEN suma = 0 THEN 'PAPÁ' ELSE 'FANNY' END AS suma FROM grupos");

        return view('Productos.agregarProducto',compact('grupos'));
    }

    public function guardarProducto(Request $request) {
        $response = array('sta' => 0,'msg' => ''); 

        $nombre = $request->nombreProducto;
        $grupo = $request->gruposProducto;

        $response = noVacio($nombre,'NOMBRE',$response);
        $response = noVacio($grupo,'GRUPO',$response);

        if($response['sta'] == 0){
            $consultar = DB::select("SELECT id FROM productos WHERE nombre LIKE '$nombre'");

            if($consultar != null){
                $response['sta'] = '1';
                $response['msg'] = "YA EXISTE UN PRODUCTO CON ESE NOMBRE";
            }

            if($response['sta'] == 0){
                DB::table('productos')->insert([
                    'nombre' => $nombre,
                    'estatus' => 0
                ]);

                $nextid = DB::getPdo()->lastInsertId();

                DB::table('grupos_productos')->insert([
                    'idGrupo' => $grupo,
                    'idProducto' => $nextid
                ]);
            }
        }

        echo json_encode($response);
    }

    public function verProducto() {
        $id = $_REQUEST['id'];

        $producto = DB::select("SELECT * 
        FROM productos AS p
        LEFT JOIN(
                            SELECT idGrupo,idProducto
                            FROM grupos_productos
        ) AS gp ON gp.idProducto = p.id 
        WHERE id = $id
        ");
        $grupos = DB::select("SELECT id,nombre,CASE WHEN suma = 0 THEN 'PAPÁ' ELSE 'FANNY' END AS suma FROM grupos");

        return view('Productos.verProducto',compact('producto','grupos'));
    }

    public function updateProducto(Request $request) {
        $response = array('sta' => 0,'msg' => ''); 

        $id = $request->idProductoEdit;
        $nombre = $request->nombreProductoEdit;
        $grupo = $request->gruposProductoEdit;

        $response = noVacio($nombre,'NOMBRE',$response);
        $response = noVacio($grupo,'GRUPO',$response);

        if($response['sta'] == 0){
            $consultar = DB::select("SELECT id FROM productos WHERE nombre LIKE '$nombre' AND id != $id");

            if($consultar != null){
                $response['sta'] = '1';
                $response['msg'] = "YA EXISTE UN PRODUCTO CON ESE NOMBRE";
            }

            if($response['sta'] == 0){
                DB::table('productos')->where('id','=',$id)->update([
                    'nombre' => $nombre
                ]);

                DB::table('grupos_productos')->where('idProducto','=',$id)->update([
                    'idGrupo' => $grupo
                ]);
            }
        }

        echo json_encode($response);
    }

    public function operacionesProducto(){
        $id = $_GET['id'];
        $t = $_GET['t'];

        if($t == 1){
            DB::update("UPDATE productos SET estatus = 1 WHERE id = '$id'");
        }elseif($t == 2){
            DB::update("UPDATE productos SET estatus = 0 WHERE id = '$id'");
        }

        return 0;
    }
}
