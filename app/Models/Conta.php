<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class Conta extends Model
{
    protected $table = 'contas';

    protected $fillable = [
        'dependente_id',
        'saldo',
        'limite_diario'
    ];
}