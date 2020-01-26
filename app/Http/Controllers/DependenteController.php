<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Formatacao;
use App\Models\Dependente;
use App\Models\Pedido;
use App\Models\Produto;

class DependenteController extends Controller
{
    private $dependenteRepo;
    private $pedidoRepo;
    private $produtoRepo;

    public function __construct(){
        $this->dependenteRepo = new Dependente();
        $this->pedidoRepo = new Pedido();
        $this->produtoRepo = new Produto();
    }

    public function show($id)
    {
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'dados' => null
        );
        
        $aluno = $this->dependenteRepo->getById($id);

        if(!$aluno){
            $resposta['erro'] = true;
            $resposta['mensagem'] = "Cadastro do denpendente não encontrado.";

            return response()->json($resposta);
        }

        $nomes = explode(' ', $aluno->nome);
        $dados = array(
            'id' => $aluno->Id,
            'saudacao' => $nomes[0],
            'nome' => $aluno->nome,
            'telefone' => $aluno->telefone,
            'dataNascimento' => Formatacao::toDateBr($aluno->data_nascimento),
            'serie' => $aluno->serie,
            'sexo' => $aluno->sexo,
            'escola' => $aluno->estabelecimento->escola,
            'saldo' => $aluno->conta->saldo,
            'limiteDiario' => 10.00
        );

        $resposta['mensagem'] = "Registro encontrado.";
        $resposta['dados'] = $dados;

        return response()->json($resposta);
    }

    public function getHistoricoPedidos($idAluno){
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'dados' => null
        );
        
        $pedidos = $this->pedidoRepo->getHistorico($idAluno);

        if(!count($pedidos)){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Aluno ainda não realizou nenhum pedido';

            return response()->json($resposta);
        }

        $resposta['dados'] = $pedidos;

        return response()->json($resposta);
    }

    public function getCategorias($idAluno){
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'dados' => []
        );
        
        $aluno = $this->dependenteRepo->getById($idAluno);
        $cardapio = $this->produtoRepo->getCardapio($aluno->estabelecimento_id);

        if(!$cardapio){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Aluno ainda não realizou nenhum pedido';

            return response()->json($resposta);
        }

        $categorias = [];
        $produtos = [];
        foreach ($cardapio as $item) {

            if( !$this->categoriaInserida($categorias, $item->idCategoria) ){
                $categorias[] = ['id' => $item->idCategoria, 'nome' => $item->categoria];
            }

            if( !$this->produtoInserido($produtos, $item->idProduto) ) {
                $produtos[] = array(
                    'id' => $item->idProduto,
                    'idCategoria' => $item->idCategoria,
                    'nome' => $item->produto,
                    'preco' => $item->preco
                );
            }
        }
        
        $resposta['dados']['categorias'] = $categorias;
        $resposta['dados']['produtos'] = $produtos;

        return response()->json($resposta);
    }

    private function categoriaInserida($arrCategorias, $idCategoria){
        for ($i=0; $i < sizeof($arrCategorias); $i++) { 
            if($arrCategorias[$i]['id'] == $idCategoria) return true;
        }

        return false;
    }

    private function produtoInserido($arrProdutos, $idProduto){
        for ($i=0; $i < sizeof($arrProdutos); $i++) { 
            if($arrProdutos[$i]['id'] == $idProduto) return true;
        }

        return false;
    }

    public function getProdutosByCategoria($idCategoria){
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'dados' => []
        );
        
        $produtos = $this->produtoRepo->getByCategoria($idCategoria);

        if(!$produtos){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Nenhum produto encontrado.';

            return response()->json($resposta);
        }

        foreach ($produtos as $produto) {
			$resposta['dados'][] = array(
				'id' => $produto->id, 
                'nome' => $produto->descricao,
                'preco' => $produto->preco_venda
			);
		}

        return response()->json($resposta);
    }

    public function verPedidoAberto($idDependente){
        $resposta = array(
			'erro' => false,
			'mensagem' => null,
			'numeroPedido' => null,
			'validoAte' => null
        );

        if(empty($idDependente)){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Parametro inválido';

            return response()->json($resposta);
        }
        
        $pedido = $this->pedidoRepo->getPedidoAberto($idDependente);

        if(!$pedido){
            $resposta['mensagem'] = 'Nenhum pedido aberto';

            return response()->json($resposta);
        }

        $resposta['mensagem'] = 'Pedido aberto encontrado';
		$resposta['numeroPedido'] = $pedido->numero_pedido;
        $resposta['validoAte'] = date('d/m/Y 19:00', strtotime($pedido->created_at));
        
        $url = "https://barcode.tec-it.com/barcode.ashx?data=";
		$url .= $pedido->numero_pedido;
		$url .= "&code=Code128&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0";
        $resposta['urlBarcode'] = $url;

        $resposta['itens'] = $this->pedidoRepo->getItens($pedido->id);
        
        return response()->json($resposta);
    }

    public function getSaldo($idAluno){
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'dados' => null
        );

        if(empty($idAluno)){
            $resposta['erro'] = true;
            $resposta['mensagem'] = 'Parametro inválido';

            return response()->json($resposta);
        }
        
        $aluno = $this->dependenteRepo->getById($idAluno);

        if(!$aluno){
            $resposta['erro'] = true;
            $resposta['mensagem'] = "Nenhum registro encontrado.";

            return response()->json($resposta);
        }

        $dados = array(
            'saldo' => $aluno->conta->saldo,
            'limiteDiario' => $aluno->conta->limite_diario
        );
        $resposta['mensagem'] = "Registro encontrado.";
        $resposta['dados'] = $dados;

        return response()->json($resposta);
    }

    public function finalizarPedido($id, Request $request){
        $resposta = array(
			'erro' => false,
			'mensagem' => '',
			'validoAte' => '',
			'numeroPedido' => 0
        );
        
        if(empty($request->carrinho)){
            $resposta['erro'] = true;
            $resposta['mensagem'] = "Dados do pedido vazio.";

            return response()->json($resposta);
        }

        $dependente = $this->dependenteRepo->getById($id);
        if(!$dependente){
            $resposta['erro'] = true;
            $resposta['mensagem'] = "Cadastro do dependente não encontrado.";
            
            return response()->json($resposta);
        }

        $carrinho = explode('|', $request->carrinho);

        $validacao = $this->pedidoRepo->validarPedido($carrinho);
        if($validacao) {
            $resposta['erro'] = true;
            $resposta['mensagem'] = $validacao;
            
            return response()->json($resposta);
        }

        $resposta['numeroPedido'] = $this->pedidoRepo->cadastrar($id, $carrinho);

        return response()->json($resposta);
    }
}
