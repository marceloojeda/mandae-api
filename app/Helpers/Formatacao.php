<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

class Formatacao
{

    /**
     * passa uma string com números e retorna somente os números
     * @param $str
     * @return mixed
     */
    public static function somenteNumeros($str)
    {
        if(empty($str)) return '';
        
        return preg_replace("/[^0-9]/", "", $str);
    }
    
    public static function formataNumero(string $numero, int $decimais = 2, string $padrao = 'nacional')
    {
        if(empty($numero) && strlen($numero) == 0){
            return '';
        }
        
        $number = $numero;
        if($padrao == \Config::get('constants.DATABASE')){
            $number = str_replace('.', '', $numero);
            $number = str_replace(',', '.', $number);
            return $number;
        }

        $number = (float) $number;

        if($padrao == \Config::get('constants.NACIONAL')){
            return number_format($number, $decimais, ',', '.');
        }

        return number_format($number, $decimais, '.', ',');
    }

    public static function validaNumeroDecimal(string $numero){
        return \preg_match('^[+-]?(\d+|\d{1,3}(\.\d{3})*)(,\d*)?$', $numero);
    }

    public static function notificationMsg($type, $message){
        \Session::put($type, $message);
    }

    public static function getDescricaoSituacao($situacao)
    {
        switch ($situacao) {
            case Enums::LOTE_AUTORIZADO:
                return "Autorizado";
            case Enums::LOTE_PROCESSANDO:
                return "Processando";
            case Enums::LOTE_REJEITADO:
                return "Rejeitado";
        }
    }

    public static function preparaLike($val){
        return sprintf("%s%s%s", "%", $val, "%");
    }

    public static function toDate($data = '')
    {
        list($dia, $mes, $ano) = explode('/', @$data);
        return $ano . '-' . $mes . '-' . $dia;
    }

    public static function toDateTime($data = '')
    {
        if (!empty($data)) {
            $dataHora = explode(' ', @$data);
            $hora = '00:00:00';
            if (isset($dataHora[1])) {
                $hora = $dataHora[1];
                @$data = $dataHora[0];
            }
            list($dia, $mes, $ano) = explode('/', @$data);
            return $ano . "-" . $mes . "-" . $dia . " $hora";
        }
        return null;
    }

    public static function toDateBr($data = '')
    {
        if (!empty($data) AND $data != '0000-00-00') {

            list($ano, $mes, $dia) = explode('-', substr(@$data, 0, 10));
            return $dia . '/' . $mes . '/' . $ano;
        }
        return 0;

    }

    public static function toHourBr($hora = '')
    {
        list($a, $b) = explode(':', $hora);
        return $a . ':' . $b;

    }

    public static function toDateBrHr($data = '')
    {
        $hora = substr(@$data, 10, 18);
        list($ano, $mes, $dia) = explode('-', substr(@$data, 0, 10));
        return $dia . '/' . $mes . '/' . $ano . $hora;
    }

    /**
     * Método responsável em formatar uma STRING para o padrão de telefones normais.
     * @static
     * @param  String $valor
     * @return String $formatado
     */
    public static function formataTelefone($valor)
    {
        $formatado = '';
        if ($valor) {

            $formatado = '(' . substr($valor, 0, 2) . ') ';
            $formatado .= substr($valor, 2, 4) . '-';
            $formatado .= substr($valor, 6);
        }

        return $formatado;
    }


    /**
     * Método responsável em formatar uma STRING para o padrão de celulares.
     * @static
     * @param  String $valor
     * @return String $formatado
     */
    public static function formataCelular($valor)
    {
        $formatado = '';
        if ($valor) {

            $qtde = strlen(trim($valor));
            if ($qtde === 11) {

                $formatado = '(' . substr($valor, 0, 2) . ') ';
                $formatado .= substr($valor, 2, 5) . '-';
                $formatado .= substr($valor, 7);

            } else {

                $formatado = '(' . substr($valor, 0, 2) . ') ';
                $formatado .= substr($valor, 2, 4) . '-';
                $formatado .= substr($valor, 6);
            }
        }

        return $formatado;
    }

    public static function getEmailFake($n = 20) { 
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 
    
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
    
        return $randomString . "@teste.com"; 
    } 
}