<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class Dependente extends Model
{
    protected $table = 'dependentes';

    protected $fillable = [
        'estabelecimento_id',
        'responsavel_id',
        'user_id',
        'nome',
        'serie',
        'telefone',
        'imagem',
        'sexo',
        'senha',
        'data_nascimento',
        'ativo'
    ];

    public function estabelecimento(){
        return $this->hasOne('App\Models\Estabelecimento', 'id', 'estabelecimento_id');
    }

    public function conta(){
        return $this->hasOne('App\Models\Conta', 'dependente_id', 'id' );
    }

    public function autenticarPeloCelular($request){
        $celular = $request->telefone;

        $aluno = Dependente::where('telefone', $celular)
            ->where('ativo', true)
            ->first();

        return $aluno;
    }

    public function getById($id){
        return Dependente::where('id', $id)->first();
    }
}