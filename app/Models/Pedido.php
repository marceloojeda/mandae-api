<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use App\Helpers\Formatacao;
use App\Models\Produto;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'conta_id',
        'total',
        'status',
        'numero_pedido'
    ];


    public function getHistorico($idAluno){
        $query = DB::table('pedidos')
            ->join('contas', 'pedidos.conta_id', 'contas.id')
            ->join('pedido_itens', 'pedidos.id', 'pedido_itens.pedido_id')
            ->join('produtos', 'pedido_itens.produto_id', 'produtos.id')
            ->where('contas.dependente_id', $idAluno)
            ->select('pedidos.*', 'pedido_itens.*', 'produtos.descricao')
            ->orderBy('pedidos.created_at', 'desc');

        return $query->paginate();
    }

    public function getPedidoAberto($idDependente){
        $dataInicio = date('Y-m-d 00:00:00');
        $dataFim = date('Y-m-d 23:59:59');

        $query = DB::table('pedidos')
            ->join('contas', 'pedidos.conta_id', 'contas.id')
            ->where('contas.dependente_id', $idDependente)
            ->where('pedidos.status', 'Aberto')
            ->whereBetween('pedidos.created_at', [$dataInicio, $dataFim])
            ->select('pedidos.*')
            ->orderBy('pedidos.id', 'desc');

        return $query->first();
    }

    public function validarPedido($carrinho){
        if(!is_array($carrinho)){
            return "Parâmetros inválidos.";
        }

        foreach ($carrinho as $item) {
            list($idProduto, $qtde) = explode(':', $item);

            $produto = Produto::where('id', $idProduto)->first();
            if(!$produto || empty($qtde)){
                return "Parâmetros inválidos.";
            }
        }

        return '';
    }

    public function cadastrar($idDependente, $carrinho){
        
        $conta = DB::table('contas')
            ->where('dependente_id', '=', $idDependente)
            ->first();
        
        $dependente = DB::table('dependentes')
            ->where('id', '=', $idDependente)
            ->first();

        $total = $this->getTotalPedido($carrinho);
        $numeroPedido = $this->gerarNumeroPedido($dependente->estabelecimento_id);

        $model = new Pedido();
        $model->estabelecimento_id = $dependente->estabelecimento_id;
        $model->conta_id = $conta->id;
        $model->status = 'Aberto';
        $model->total = $total;
        $model->numero_pedido = $numeroPedido;
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');

        $model->save();

        $this->cadastrarItens($model->id, $carrinho);

        return $model->numero_pedido;
    }

    private function getTotalPedido($carrinho){

        $total = floatval(0);
        foreach ($carrinho as $item) {
            list($idProduto, $qtde) = explode(':', $item);
            
            $produto = Produto::where('id', $idProduto)->firstOrFail();
            $total += $produto->preco_venda * $qtde;
        }

        return $total;
    }

    private function cadastrarItens($idPedido, $carrinho){
        foreach ($carrinho as $item) {
            list($idProduto, $qtde) = explode(':', $item);

            $produto = Produto::where('id', $idProduto)->firstOrFail();

            $dados = array(
                'pedido_id' => $idPedido,
                'produto_id' => $idProduto,
                'valor_unitario' => $produto->preco_venda,
                'quantidade' => $qtde,
                'total' => $qtde * $produto->preco_venda,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            DB::table('pedido_itens')->insert($dados);
        }
    }

    public function gerarNumeroPedido($idEstabelecimento){
        $pin = $this->gerarPIN();
        while(!$this->numeroPedidoVago($pin, $idEstabelecimento)){
            $pin = $this->gerarPIN();
        }
        return $pin;
    }

    public function numeroPedidoVago($numeroPedido, $idEstabelecimento){
        $dataInicio = date('Y-m-d 00:00:00');
        $dataFim = date('Y-m-d 23:59:59');

        $pedidos = Pedido::where('estabelecimento_id', $idEstabelecimento)
            ->where('numero_pedido', $numeroPedido)
            ->where('created_at', '>=', $dataInicio)
            ->where('created_at', '<=', $dataFim)
            ->first();

        return !$pedidos;
    }

    private function gerarPIN($digitos = 4){
        $i = 0;
        $pin = "";
        while ($i < $digitos) {
            if($i > 0) {
                $pin .= mt_rand(0, 9);
            } else {
                $pin .= mt_rand(1, 9);
            }
            $i++;
        }
        return $pin;
    }

    public function getItens($idPedido){
        return DB::table('pedido_itens')
            ->join('produtos', 'pedido_itens.produto_id', 'produtos.id')
            ->where('pedido_itens.pedido_id', $idPedido)
            ->select('produtos.descricao', 'pedido_itens.valor_unitario', 'pedido_itens.quantidade', 'pedido_itens.total')
            ->get();
    }
}