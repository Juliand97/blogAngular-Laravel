<?php

namespace App\Http\Controllers;
/*Al utilizar */
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    //
    public function prueba(Request $request)
    {
    	return "desde un controlador usuario";
    }

    public function registro(Request $data) 
    {
      /*Cuando se pasa una cadana json la funcion 
        json_decode hace dos posibilidades si solo tiene 
        un parametro esta vuelve la cadena de json en una clase 
        con metodos de lo contrario si lleva el parametro extra "true"
        esta se convierte en un array

        la particula unique:tabla de base de datos es una particula que colocada 
        en el validator de los datos hace que verifique si estos no estan registrados
      */
      $datosrequest=$data->input('data',null);
      $users= new User;
    	$datos=json_decode($datosrequest,true);

    	if (!empty($datos)) 
    	{
    		// $datos=array_map('trim',$datos);
	    	$validar=\Validator::make($datos,[
	    		'nombre'=>'required|alpha_spaces',
	    		'apellido'=>'required|alpha_spaces',
	    		'correo'=>'required|email|unique:users',
	    		'contrasena'=>'required',
          'descripcion'
	    	]);

	    	if (!$validar->fails())
	    	{
          /*el metodo de password_hash es propio de php este generalmente va acompañado 
          con la constante de password_bcript y junto a ellos esta una propiedad 
          llamada cost esta indicará las veces que se cifrará el dato 

          $cifrapass=password_hash($datos['contrasena'],PASSWORD_BCRYPT,['cost'=>4]);
          */
          $cifrapass=hash("sha256", $datos['contrasena']);

          $users->nombre=$datos['nombre'];
          $users->apellido=$datos['apellido'];
          $users->correo=$datos['correo'];
          $users->contrasena=$cifrapass;
          $users->rol='2';
          /*La siguiente instruccion sirve para guardar los datos en bd*/
          $users->save();
          /*Envio de la respuesta en json*/
          $response=parent::response('success','Correcto',$datos);
	    	}
	    	else
	    	{
          $response=parent::response('error','Este usuario ya existe. Por favor verifique los datos nuevamente',$validar->errors());
	    	}

    	}
    	else
    	{
        $response=parent::response('error','Problemas al registrar. Por favor intente mas tarde',$validar->errors());
    	}
    	return $response;

    }

    public function login(Request $data)
    {
        $jwt= new \JwtAuth();
        $datosrequest=$data->input('data',null);
        $datos=json_decode($datosrequest,true);
        if (!empty($datos))
        {
            $validar=\Validator::make($datos,[
                'correo'=>'required|email',
                'contrasena'=>'required',
            ]);
            if (!$validar->fails()) 
            {
                $email=$datos['correo'];
                $cifrapass=hash("sha256", $datos['contrasena']);
                if ($datos['token']) 
                {
                 $response=$jwt->signUp($email,$cifrapass,true);
                }
                else
                {
                 $response=$jwt->signUp($email,$cifrapass);
                }   
            }
            else
            {
              $response=parent::response('error','Error en usuario o contraseña, Verifique e intente nuevamente',$validar->errors());
            }
        }
        else
        {
          $response=parent::response('error','Problemas al iniciar sesion. Por favor intente mas tarde',$validar->errors());
        }
        /*Los datos codificados en un token no pueden devolverse en 
        forma de objeto para esto toca hacer una conversion en formato JSON*/
    	return $response;
    }

    public function update(Request $data)
    {
      $jwt= new \JwtAuth();
      $token=$data->header("authorization");
      $token=str_replace('"',"", $token);
      #se decodifica el token para realizar la actualizacion de los datos del usuario
      #esto se hace con el parametro booleano true: decodifica token 
      $checktoken=$jwt->checktoken($token);
      if ($checktoken)
      {
        $datosrequest=$data->input("data");
        $datos=json_decode($datosrequest,true);
        if (!empty($datos)) 
        {
          $checktoken=$jwt->checktoken($token,true);
          /*Decodificando el token se permite realizar el update de los datos antiguos 
          ya que sin este no se puede hacer la actualizacion del mismo*/
          $validar=\Validator::make($datos,[
            'nombre'=>'required|alpha_spaces',
            'apellido'=>'required|alpha_spaces',
            'correo'=>'required|email|unique:users,'.$checktoken->sub
            
          ]);
          
          /*unset se utilizara para remover los campos que no son necesarios 
          para una actualizacion en el json que entra */
          unset($datos['id']);
          unset($datos['rol']);
          unset($datos['contrasena']);
          unset($datos['created_at']);
          unset($datos['remember_token']);
          unset($datos['estado']);

          $update_user=User::where('id',$checktoken->sub)->update($datos);
          if ($update_user!=0) 
          {
  
            $response=parent::response('success','Actualizacion realizada correctamente',$datos);
          }
       
        }
        else
        {
          
          $response=parent::response('Error','Problemas al obtener información. Por favor intente mas tarde',$datos);
        }

        
      }
      else
      {
        $response=parent::response('Error','Problemas al actualizar. Por favor intente mas tarde',null);
      }
      return $response;
    }

    public function uploadimg(Request $data)
    {
      /*
        A traves de la funcion file podemos recoger
        los distintos archivos que sean enviados por medio de 
        la aplicacion.  esta funcion es semejante a la variable 
        global $_FILE[] la cual permite la subida de los archivos 
        los cuales almacena los detalles en un array.
      */
      $img=$data->file('img0');
      
      /*Validar si el archivo subido es una img */
      $validar=\Validator::make($data->all(),[
            'img0'=>'required|image|mimes:jpg,jpeg,gif,png'
          ]);

      if (!$img || $validar->fails()) {
       $response=parent::response('Error','error al subir archivo, intente nuevamente',null);
      }
      else
      {
        #con la fn getClientOriginalName se obtiene 
        #el nombre de la img
        $imgName=time().$img->getClientOriginalName();
        /* 
         En la siguiente linea se hace el proceso de guardado 
         de las img teniendo en cuenta las fn de la clase 
         storage y la clase file. siendo la primera para 
         establecer la carpeta de donde sera guardado el 
         archivo y la otra para lograr el proceso de copiado 
         y guardado.
        */
        $guardado= \Storage::disk('img')->put($imgName,\File::get($img));
        $response=parent::response('success','Archivo subido correctamente',$imgName);
      }
       return $response;
    }

    public function mostrarImg($nombreImg)
    {
      /*Para exportar un archivo en laravel se debe utilizar el obj 
      response ya que este archivo y su informacion es dividida a forma 
      de array*/
      $archivo=\Storage::disk('img')->exists($nombreImg);
      if (!$archivo) {
        $response=parent::response('Error','El archivo solicitado no existe',null);
      }
      else{
        $archivo=\Storage::disk('img')->get($nombreImg);
        $response= new Response($archivo,200);
      }
      return $response;
    }

    public function userInfo($iduser)
    {
      $user=User::find($iduser);
      if (is_object($user)) 
      {
        $response=parent::response('success',null,$user);
      }
      else
      {
        $response=parent::response('Error','Usuario no existe, por favor verifique la informacion',null);
      }
      return $response;
    }
}
