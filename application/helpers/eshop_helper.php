<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function parse_price($price = 0, $precision = '', $round = '', $write_symbol = TRUE, $symbol = '')
{
    if($precision == '' || $round == '' || $symbol == '')
    {
        $CI =& get_instance();
        $CI->load->driver('eshop');
        $currency = $CI->eshop->currencies->get_current();
        
        if($precision == '') $precision = @$currency->decimals;
        if($round == '') $round = @$currency->round;
        if($write_symbol && $symbol == '') $symbol = @$currency->symbol;
    }
    
    $price = number_format(round($price, $round), $precision, '.', ' ');
    if($write_symbol) $price .= ' ' . $symbol;
    return $price;
}

function parse_price_in_currency($price = 0, $currency_id = '', $write_symbol = TRUE)
{
    $CI =& get_instance();
    $CI->load->driver('eshop');
    
    if($CI->eshop->currencies->currency_exists($currency_id))
    {
        $currency = $CI->eshop->currencies->get_current();
        return parse_price($price, $currency->decimals, $currency->round, $write_symbol, $currency->symbol);
    }
    else
    {
        return parse_price($price, '', '', $write_symbol);
    }
}