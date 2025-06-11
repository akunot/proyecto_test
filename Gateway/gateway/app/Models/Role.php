<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',        // nombre del rol (ej: 'admin', 'user')
        'label',  // Añade este campo
        'description', // descripción opcional del rol
    ];

    /**
     * The attributes that should be guarded.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'  // protegemos el ID de asignación masiva
    ];

    /**
     * Relación con los usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
