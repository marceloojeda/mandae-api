<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class Produto extends Model
{
    protected $table = 'produtos';

    protected $fillable = [
        'estabelecimento_id',
        'categoria_id',
        'descricao',
        'preco_venda',
        'ativo'
    ];

    public function getCategorias($idAluno = 0){
        // DB::enableQueryLog();
        $query = DB::table('categorias')
            // ->join('dependentes', 'categorias.estabelecimento_id', 'dependentes.estabelecimento_id')
            // ->where('dependentes.id', $idAluno)
            ->select('categorias.*');

        return $query->get();

        // dd(DB::getQueryLog());
    }

    public function getCardapio($idEstabelecimento){
        $cardapio = Produto::where('categorias.estabelecimento_id', $idEstabelecimento)
            ->join('categorias', 'produtos.categoria_id', 'categorias.id')
            ->select('categorias.id as idCategoria', 'categorias.descricao as categoria', 
                'produtos.id as idProduto', 'produtos.descricao as produto', 'produtos.preco_venda as preco')
            ->orderBy('categorias.descricao')
            ->orderBy('produtos.descricao')
            ->get();

        return $cardapio;
    }

    public function getByCategoria($idCategoria){
        $produtos = Produto::where('categoria_id', $idCategoria)
            ->where('ativo', true)
            ->orderBy('descricao')
            ->get();

        return $produtos;
    }
}