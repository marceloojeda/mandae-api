<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Dependente as DependenteRepo;;

class AuthController extends Controller
{
    
    private $dependenteRepo;

    public function __construct()
    {
        $this->dependenteRepo = new DependenteRepo();
    }

    public function autenticarPorTelefone(Request $request){

        // return response()->json($request->all());
        
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'perfil' => '',
			'id' => null
        );

        $params = json_decode(base64_decode($request->q));
        
        $aluno = $this->dependenteRepo->autenticarPeloCelular($params);

        if(!$aluno){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Aluno não cadastrado';

            return response()->json($resposta);
        }

        if( !Hash::check($params->senha, $aluno->senha) ){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Senha inválida';

            return response()->json($resposta);
        }

        $resposta['perfil'] = 'Dependente';
        $resposta['id'] = $aluno->id;
        $resposta['mensagem'] = 'Aluno atenticado com sucesso';

        return response()->json($resposta);
    }
}
