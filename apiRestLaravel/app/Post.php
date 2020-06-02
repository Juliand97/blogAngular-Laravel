<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
	protected $publicacion="posts";

	/* The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'id'
                           ,'id_usuario'
                           ,'nom_publicacion'
                           ,'contenido'
                           ,'img_usuario'
                           ,'estado'
    ];

	public function usuario()
	{
		/*
			La siguiente linea permite sacar la informacion de una relacion 
			muchos a uno por medio del id del usuario 
		*/
		return $this->belongsTo("App\User","id_usuario");
	}

	public function categoria()
	{
		return $this->belongsTo("App\category","id_categoria");
	}
}
