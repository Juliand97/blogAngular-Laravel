<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\helpers\JwtAuth;
class PostController extends Controller
{

	/*
		Cuando el middelware se invoca en un constructor
		junto con la propiedad except se especifican funciones
		las cuales quedaran por fuera la validacion
	*/
	public function __construct()
	{
		$this->middleware('api.auth'
										 ,['except'=>['index'
																 ,'show'
																 ,'MostrarImgPost'
																 ,'listaPostCategoria'
																 ,'listaPostUsuario']
											]);
	}

	public function index()
	{
		$publicaciones=Post::all();
		$response=parent::response('success',null,$publicaciones);
		return $response;
	}

	public function show($id)
	{
		$publicaciones=Post::find($id);
		if (is_object($publicaciones)) {
			$response=parent::response('success',null,$publicaciones);
		}
		else
		{
			$response=parent::response('Error','Publicación no encontrada',null);
		}
		return $response;
	}

	public function store(Request $data)
	{
		$publicaciones= new Post;
		$datosRequest=$data->input('data',null);
		$datajson=json_decode($datosRequest,true);
		$fecha=date("Y-m-d");
		$estado="ACTI";
		if (!empty($datajson)){
			$datosUsuario=$this->getIdentity($data);
			$validar=\Validator::make($datajson,[
												 'nom_pub'=>'required'
								     			,'contenido'=>'required'
												,'categoria'=>'required'
												,'img_usuario'=>'required']
									);

			if (!$validar->fails()) {
				$publicaciones->id_usuario=$datosUsuario->sub;
				$publicaciones->id_categoria=$datajson['categoria'];
				$publicaciones->nombre_pub=$datajson['nom_pub'];
				$publicaciones->contenido=$datajson['contenido'];
				$publicaciones->img_usuario=$datajson['img_usuario'];
				$publicaciones->estado=$estado;
				$publicaciones->save();
				$response=parent::response('success','Correcto',$datajson);
			}
			else
			{
				$response=parent::response('Error',"Datos incompletos.Intente nuevamente",null);
			}
		}
		else
		{
			$response=parent::response('Error','Problemas al ingresar.Intente nuevamente',null);
		}
		return $response;
	}

	public function update($idPost,Request $data){
		$datosRequest=$data->input('data',null);
		$datos=json_decode($datosRequest,true);
		if (!empty($datos)){
			$validar=\Validator::make($datos,[
					 'nombre_pub'=>'required'
					,'contenido'=>'required'
					,'id_categoria'=>'required'
					,'img_usuario'
					,'estado'
				]);
			/* datos a tener en cuenta para quitar en caso de que vengan
			en la cadena json*/
			if (!$validar->fails()) {
				unset($datos['id']);
				unset($datos['created_at']);
				unset($datos['id_usuario']);
				$datosUsuario=$this->getIdentity($data);
				$where=[
								'id'=>$id
								,'id_usuario'=>$datosUsuario->sub
				];
				/* a diferencia de los metodos select y delete el metodo update 
				 no permite mas de una funcion where por ende lo recomendado es hacer un array y 
				 pasarlo por identificador de columnas*/
				$actualizarPost=Post::updateOrCreate($where,$datos);
				$response=parent::response('success','Publicacion actualizada correctamente',$actualizarPost);
			}
			else{
				$response=parent::response('Error','Publicacion presenta inconsistencias, Intente nuevamente',null);
			}
		}
		else{
			$response=parent::response('Error','Datos incompletos o campos requeridos vacios, Intente nuevamente',null);
		}
		return $response;
	}
	/*El metodo destroy es para eliminar */
	public function destroy($idPost,Request $data){
		$datosRequest=$data->input('data',null);
		$datosUsuario=$this->getIdentity($data);
		$datos=json_decode($datosRequest,true);
		$publicacion=Post::where('id',$idPost)
										 ->where('id_usuario',$datosUsuario->sub)
										 ->first();

		if ($publicacion!=false){
			$eliminarPost=$publicacion->delete($idPost);
			$response=parent::response('success','Publicacion eliminada correctamente',$publicacion);
		}
		else
		{
			$response=parent::response('Error','La publicacion no existe',null);
		}
		return $response;
	}

	/*Muestra los datos del token es decir la informacion 
		del usuario que inicio sesion*/
	private function getIdentity($request)
	{
		$jwt= new JwtAuth();
		$usuario=$request->header("authorization",null);
		$datosUsuario=$jwt->checkToken($usuario,true);
		return $datosUsuario;
	}

	public function uploadimg(Request $data)
    {
     
      $img=$data->file('img0');
      $validar=\Validator::make($data->all(),[
            'img0'=>'required|image|mimes:jpg,jpeg,gif,png,ico'
          ]);

      if (!$img || $validar->fails()) {
       $response=parent::response('Error','error al subir archivo, intente nuevamente',null);
      }
      else
      {
        $imgName=time().$img->getClientOriginalName();
        $guardado= \Storage::disk('img')->put($imgName,\File::get($img));
        $response=parent::response('success','Archivo subido correctamente',$imgName);
      }
       return $response;
    }

  public function MostrarImgPost($nombreImg)
  {
  	$buscarImg=\Storage::disk('img')->exists($nombreImg);
  	if(!$buscarImg){
  		$response=parent::response('Error','Imagen no encontrada',null);
  	}else{
  		$imagen=\Storage::disk('img')->get($nombreImg);
  		$response=new Response($imagen,200);
  	}
  	return $response;
  }

  public function listaPostCategoria($categoria)
  {
  	$post=Post::where('id_categoria',$categoria);
  	// if ($post) {
  		$response=parent::response('success',null,$post);
  	// }else{
  	// 	$message='No se encontraron publicaciones con esta categoria, verifique e intente nuevamente';
  	// 	$response=parent::response('success',$message,null);
  	// }
  	return $response;
  }

  public function listaPostUsuario($usuario)
  {
  	$post=Post::where('id_usuario',$usuario);
  	if ($post) {
  		$response=parent::response('success',null,$post);
  	}else{
  		$message='No se encontraron publicaciones con este usuario, verifique e intente nuevamente';
  		$response=parent::response('success',$message,null);
  	}
  	return $response;
  }
}
?>