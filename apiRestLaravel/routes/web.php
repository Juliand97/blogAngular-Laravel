<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Carga de clases */
USE \App\Http\Middleware\ApiAuthMiddleware;
//Inicio Rutas de prueba 

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', function () {
    return '<p>hola </p>';
});

#en las siguiente funcion se setea la ruta con parametros 
Route::get('/prueba/{nombre?}', function ($nombre=null) {


    return view('prueba',array(
    	"txt"=>$nombre
    ));
});

/*
Para cada nueva pagina creadad se debe crear una ruta para que sea visible
- la palabra despues del @ es la funcion dentro del controlador a la cual va a ir 
*/
Route::get('/pruebas','PruebasController@index' );
Route::get('/test','PruebasController@testorm' );
Route::get('/pruebaU','UserController@prueba' );
Route::get('/pruebaP','PostController@prueba' );
Route::get('/pruebaC','CategoryController@prueba' );
// fin rutas de prueba
#-----------------------------------------------------------------------------------------------------#
/*Rutas de api */

	#Rutas de usercontroller
	Route::post('/usuario/registro','UserController@registro' );
	Route::post('/usuario/login','UserController@login' );
	Route::put('/usuario/update','UserController@update' );
		#metodo para subir img de usuario
	Route::post('/usuario/uploadimg','UserController@uploadimg')->middleware(ApiAuthMiddleware::class);
	Route::get('/usuario/avatarUser/{imguser}','UserController@mostrarImg');
	Route::get('/usuario/info/{info}','UserController@userInfo');

	/*
		Las rutas que lleven el llamado a la metodo resource 
		son rutas las cuales se van a generar de manera automatica 
		por laravel. Tambien esta creara los metodos propios para 
		despues ser utilizados en base a la necesidad de los mismos 
	*/
	#Rutas de categoryController
	Route::resource("/api/category","CategoryController");

	// Rutas para PostController
	Route::resource("/api/post","PostController");
	#rutas de metodos para subir y mostrar imagen del post
	Route::post("/post/uploadimg","PostController@uploadimg");
	Route::get('/post/imgpost/{imgpost}','PostController@MostrarImgPost');
	#rutas para ver el listado de los post por categoria y por usuario
	Route::get('/post/listaPostCategoria/{id_categoria}','PostController@listaPostCategoria');
	Route::get('/post/listaPostUsuario/{iduser}','PostController@listaPostUsuario');

