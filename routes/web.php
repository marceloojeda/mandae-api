<?php


// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

// Route::get('/', 'ExampleController@index');
Route::post('auth/login-por-telefone', 'AuthController@autenticarPorTelefone');

Route::get('dependentes/{id}', 'DependenteController@show');
Route::get('dependentes/historico-pedidos/{idAluno}', 'DependenteController@getHistoricoPedidos');
Route::get('dependentes/lista-categorias/{idAluno}', 'DependenteController@getCategorias');
Route::get('dependentes/pedido-aberto/{idAluno}', 'DependenteController@verPedidoAberto');
Route::get('dependentes/ver-saldo/{idAluno}', 'DependenteController@getSaldo');
Route::get('dependentes/produtos-por-categoria/{idCategoria}', 'DependenteController@getProdutosByCategoria');

Route::get('pedido/pedido-aberto/{idAluno}', 'DependenteController@verPedidoAberto');
Route::post('pedido/finalizar-pedido/{id}', 'DependenteController@finalizarPedido');