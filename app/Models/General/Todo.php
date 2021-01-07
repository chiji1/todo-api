<?php

namespace App\Models\General;

use App\Helpers\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use SoftDeletes, HasFactory, Uuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['type', 'name', 'description', 'pop', 'mail'];
}
