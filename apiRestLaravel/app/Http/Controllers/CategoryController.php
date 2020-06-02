<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    /*
    	En los metodos que se crean por ruta resource
    	se debe tener en cuenta que generalmente los metodos 
    	por post deben ser autenticados. 

    	En este caso lo mas factible es usar la clase del 
    	middleware 
    */
        

    public function __construct()
    {
    	/*
    		Cuando el middelware se invoca en un constructor
    		junto con la propiedad except se especifican funciones
    		las cuales quedaran por fuera la validacion 
    	*/
    	$this->middleware('api.auth',['except'=>['index','show']]);
    }

    public function prueba(Request $request)
    {
    	return "desde un controlador categoria";
    }

    public function index()
    {
    	$categorias=Category::all();
        $response=parent::response('success',null,$categorias);
    	return $response;
    }

    public function show($id)
    {
    	$categorias=Category::find($id);
    	if (is_object($categorias)) {
    		 $response=parent::response('success',null,$categorias);
    	}
    	else
    	{
            $response=parent::response('Error','Categoria no encontrada',null);
    	}
    	return $response;
    }

    public function store(Request $data)
    {
    	$categorias= new Category;
    	$fecha=date("Y-m-d");
    	$estado="ACTI";
    	$datosRequest=$data->input('data',null);
    	$datajson=json_decode($datosRequest,true);
    	if (!empty($datajson)){
    		$validar=\Validator::make($datajson,['nomcat'=>'required|alpha']);
    		if (!$validar->fails()) {
    			$categorias->nom_cat=$datajson['nomcat'];
	    		$categorias->created_at=$fecha;
	    		$categorias->updated_at=$fecha;
	    		$categorias->estado=$estado;
	    		$categorias->save();
                $response=parent::response('success','Correcto',$datajson);
    		}
    		else
    		{
                $response=parent::response('Error',"Categoria  {$datajson["nomcat"]} ya existe.Intente nuevamente",null);
    		}
    	}
    	else
    	{
            $response=parent::response('Error','Problemas al ingresar.Intente nuevamente',null);
    	}
    	return  $response;
    }

    public function update($idCategoria,Request $data){
        $datosRequest=$data->input('data',null);    
        $datos=json_decode($datosRequest,true);
        if (!empty($datos)){
            $validar=\Validator::make($datos,[
                'nom_cat'=>'required'
            ]);
            /* datos a tener en cuenta para quitar en caso de que vengan 
             en la cadena json*/
            unset($datos['idcat']);
            unset($datos['created_at']);

            $actualizarCategoria=Category::where('id',$idCategoria)->update($datos);
            $response=parent::response('success','Categoria actualizada correctamente',null);
        }
        else{
            $response=parent::response('Error',"Categoria ya existe Intente nuevamente",null);
        }
        return $response;
    }
}