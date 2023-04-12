<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trasa extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'id',
    'nazwa', 
    'id_tworcyOnSerwer', 
    'przewidywany_czas', 
    'przewidywana_dlugosc', 
    'czy_zakonczona', 
    'tworca'
    ]; //pola które mogą być uzupełniane
    public function lokalizacje() //powiązanie z tabelą lokalizacje
    {
        return $this->hasMany(Location::class);
    }

    public function users()
    {
    return $this->belongsToMany(User::class, 'users_trasas','trasa_id', 'users_id');
    }
}
