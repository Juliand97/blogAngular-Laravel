<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'nombre'
                           ,'apellido'
                           ,'img_usuario'
                           ,'correo'
                           ,'contrasena'
                           ,'descripcion'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'contrasena', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function publicaciones()
    {
        /*
        Con la siguiente sentencia se utiliza un metodo de laravel 
        llamado hasmany el cual puede referenciar un modelo distinto 
        para obtener datos de ese. 
        */
        return $this->hasMany("App\publicacion");
    }
}