<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Helpers\Formatacao;
use Config;

class Estabelecimento extends Model
{
    protected $table;
    protected $fillable;

    public function __construct(){
    	$this->table = "estabelecimentos";
    	$this->fillable = ['user_id', 'nome', 'email', 'escola', 'telefone', 'ativo'];
    }

	public function cadastrar($arrInsert){
		$model = new Estabelecimento();

		$model->user_id = $this->cadastrarUsuario($arrInsert);
		$model->nome = $arrInsert['nome'];
		$model->email = $arrInsert['email'];
		$model->escola = $arrInsert['escola'];
		$model->telefone = Formatacao::somenteNumeros($arrInsert['telefone']);
		$model->ativo = true;

		$model->save();

		return $model;
	}

	private function cadastrarUsuario($arrInsert){
		$model = new User();
		$model->name = $arrInsert['nome'];
		$model->email = $arrInsert['email'];
		$model->password = Hash::make($arrInsert['senha']);
		$model->perfil = Config::get('constants.PERFIL_USUARIO.CANTINEIRO', 1);

		$model->save();

		return $model->id;
	}

	public static function getByUserId($idUser){
		return Estabelecimento::where('user_id', $idUser)->firstOrFail();
	}
}
