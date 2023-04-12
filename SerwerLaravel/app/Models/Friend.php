<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'friend_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function addFriend($userId, $friendId)
    {
        return $this->create([
            'user_id' => $userId,
            'friend_id' => $friendId
        ]);
    }

    public function removeFriend($userId, $friendId)
    {
        return $this->where([
            'user_id' => $userId,
            'friend_id' => $friendId
        ])->delete();
    }

    public function updateStatus($userId, $friendId, $status)
    {
        return $this->where([
            'user_id' => $userId,
            'friend_id' => $friendId
        ])->update([
            'status' => $status
        ]);
    }
}
