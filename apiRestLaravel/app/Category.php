<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /*
    	Estos documentos que permiten el mapeo de la tabla y 
    	consecucion de datos por medio de estos 
    */

    protected $tabla="categories";

    public function publicaciones()
    {
    	/*
    	Con la siguiente sentencia se utiliza un metodo de laravel 
    	llamado hasmany el cual puede referenciar un modelo distinto 
    	para obtener datos de ese. 
    	*/
    	return $this->hasMany("App\post");
    }
}
