<?php

namespace App\Models\General;

use App\Helpers\Uuids;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use SoftDeletes, HasFactory, Uuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'ip_address',
        'type',
        'name',
        'description',
        'date',
        'color',
        'pop',
        'mail',
        'completed'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
