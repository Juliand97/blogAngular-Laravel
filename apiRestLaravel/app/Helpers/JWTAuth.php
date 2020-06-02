<?php 
namespace  App\Helpers;
/*Firebase sirve como un sistema de autenticacion para los inicios de sesion generando tokens
para ello utilizaremos la libreria de comporser jwt de firebase*/
Use Firebase\JWT\JWT;
/*Iluminate permite que usarse como query builder (base de datos)*/
Use Iluminate\Support\Facades\DB;
Use App\User; 	
Class JWTAuth
{
	/*
		A traves de las siguientes propiedades construiremos un token 
		lo cual es un sistema de autenticar por la entrada de una serie 
		de datos teniendo en cuenta los datos de entrada ya que estos 
		deben coincidir para entrar a las diferentes paginas en las 
		cuales se inicia sesion 

		Estos estaran dados en el metodo constructor 
	*/

	Public $keyToken;

	public function __construct()
	{
		$this->keyToken='Clave_proyecto_Blog:1997:';
	}

	public function signUp($email,$password,$tokenExistente=null)
	{
		try
		{
			/*
			 Consulta para encontrar el registro perteneciente al usuario
			 esta variable si el resultado es positivo se convertira en una 
			 variable de tipo obj 
			 */
			$user= User::where([
				'correo'=>$email,
				'contrasena'=>$password
			])->first();
			#se tomara esta variable base para generar el token de autenticacion 
			#este se iniciara en false lo cual dice que hubo errores en iniciar sesion
			$signUp=false;
		
			if (is_object($user))
			{
				$signUp=true;
			}

			if ($signUp) 
			{
				/*
					En la siguiente variable se presenta una serie de datos llamados token 
					en esta se ve representada la informacion general de usuario en la cual 
					se hara uso posterior para las autenticaciones
						- sub=para indicar el id del usuario 
						- iat=esto indica el tiempo en el que fue creado el token 
						- exp=cuando vencera el toquen 
				*/
				$fechaActual=time();
				$caduca=time()+(1*24*60*60);
				

				$token=array(
					"sub" => $user->id
				   ,"nombre" =>$user->nombre
				   ,"apellido"=>$user->apellido
				   ,"correo" =>$user->correo
				   ,"iat"=> $fechaActual
				   ,"exp"=>$caduca
				);

				#Cifrado Token 
				#				 array token  key de token    metodo de cifrado para el token  
				$tokenjwt=JWT::encode($token,$this->keyToken,'HS256');
				#descifrado Token
				/*En la siguiente linea utilizamos el metodo decode 
				  para decifrar la informacion dada en el token codificado 
				  en caso de que el token ya haya sido generado este solo 
				  necesitara una decodificacion para saber los datos de este 
				 */
				$tokenjwtdecode=JWT::decode($tokenjwt,$this->keyToken,['HS256']);
				$responseToken=(!is_null($tokenExistente)) ? $tokenjwtdecode : $tokenjwt ;
				$response=array( 'code'=>200
		    				   	,'data'=>$responseToken
		    				   	,'status'=>'success');

			}
			else
			{
				$response=array( 'code'=>416
		    				   	,'message'=>'Error en las credenciales por favor intentar nuevamente'
		    				   	,'status'=>'Error');
			}
			return $response;
		}
		catch(Exception $e)
		{
			$response=array( 'code'=>416
		    				,'message'=>$e->getMessage()
		    				,'status'=>'Error');
		}

	}

	public function CheckToken($jwtoken, $visisbilidadToken=false)
	{
		$auth=false;

		try {

			$tokenjwtdecode=JWT::decode($jwtoken,$this->keyToken,['HS256']);

		} catch (\UnExpectedValueException $e) {
			$auth=false;
			
		}catch(\DomainException $e){
			$auth=false;
		}
		/*Para dar el aval de la autenticacion se verifica el token */
		if (!empty($tokenjwtdecode) && is_object($tokenjwtdecode) && isset($tokenjwtdecode->sub)){
			$auth=true;
		}
		/*De lo contrario se devuelve un token con los datos del usuario*/
		if ($visisbilidadToken) {
			return $tokenjwtdecode;
		}
		
		return $auth;
		
	}
}
 ?>