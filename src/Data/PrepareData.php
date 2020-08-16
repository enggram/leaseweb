<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Data;
/**
 * Description of PrepareData
 *
 * @author ramkumar
 */
class PrepareData
{
    
    /**
     * Prepare Hard disk data
     * Return hdd type, hdd size, hdd code
     * @param type $hdd
     * 
     */
    public function prepareHddData($hdd)
    {
        if (strpos($hdd, 'GB') !== false) {
            list($size, $type) = explode('GB', $hdd);
            list($limit, $count) = explode('x', $size);
            $totalSize = $limit * $count / 1000;
        }else{
            list($size, $type) = explode('TB', $hdd);
            list($limit, $count) = explode('x', $size);
            $totalSize = $limit * $count;
        }
        
        return ['hdd' => $hdd, 'hddSize' => $totalSize, 'hddType' => $type];
    }
    
    /**
     * Prepare and send RAM size and type
     * @param type $ram
     * @return type
     */
    public function prepareRamData($ram)
    {
        list($size, $type) = explode('GB', $ram);
        return ['ramType' => $type, 'ram' => $size];
    }
    
    /**
     * Prepare and send price value and currency used.
     * @param type $price
     * @return type
     */
    public function preparePriceAndCurrency($price)
    {
        preg_match("/[0-9\.]+/", $price, $matches);
        $priceValue = $matches[0];
        preg_match("/[0-9\.]+/", $price, $matches);
        list($currency) = explode($matches[0], $price);
        
        return ['price' => $priceValue, 'currency' => $currency];
    }
}
