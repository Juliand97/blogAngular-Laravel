<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    //al retornar una vista con algun nombre en especial 
    // se usa el punto
    public function index()
    {
    	$titulo="Lenguajes de programación";
    	$mostrar=array("Python"
    				  ,"Php"
    				  ,"Java"
    				  ,"Js"
    				  ,"C#"
    				);
    	return view("pruebas.index",
    				array('language'=>$mostrar,
    					  'title'=>$titulo)
    	);

    }

    public function testorm()
    {
    	$post=Post::All();
    	#var_dump($post);
    	foreach ($post as $post) {
			echo $post->categoria->nom_cat;
			echo $post->nombre_pub;
    	}
    	
    	die();
    }
}
