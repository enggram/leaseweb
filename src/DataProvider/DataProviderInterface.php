<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DataProvider;

/**
 * Description of DataProviderInterface
 *
 * @author ramkumar
 */
interface DataProviderInterface
{
    public function getData();
    
    public function applyFilter($request);
    
    public function getFilterParams();
}
