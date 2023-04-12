<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nazwa', 
        'opis', 
        'szerokosc', 
        'dlugosc',
        'trasa_id'
        ]; //pola które mogą być uzupełniane
        public function trasa() //powiązanie z tabelą trasa
        {
            return $this->belongsTo(Trasa::class);
        }
}
