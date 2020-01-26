<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Lancamento extends Model
{
    protected $table = 'lancamentos';

    protected $fillable = [
        'estabelecimento_id',
        'conta_id',
        'total',
        'debito',
        'origem'
    ];
}