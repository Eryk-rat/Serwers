<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersTrasas extends Model
{
    use HasFactory;
    protected $table = 'users_trasas';
    protected $fillable = [
        'users_id', 
        'trasa_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function trasa()
    {
        return $this->belongsTo(Trasa::class, 'trasa_id');
    }
}
