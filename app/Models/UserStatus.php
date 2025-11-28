<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    //
    protected $table = 'user_status';
    protected $fillable = [
        'user_id',
        'total_points',
        'total_puzzles_solved',
        'best_endurance_streak',
        'best_endurance_score',
        'riddles_solved',
        'logic_solved',
        'last_played',
    ];
    protected $primaryKey = 'stat_id';

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public $timestamps = false;
}
