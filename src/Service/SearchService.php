<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Service;
/**
 * Description of SearchService
 *
 * @author ramkumar
 */
class SearchService
{
    /**
     * Search Filter used to filter excel sheet based on the filter
     * @param type $filter
     * @param type $value
     * @param type $servers
     * @return type
     */
    public function filter($filter, $value, $servers)
    {
        switch ($filter) {
            case "hddMin":
                return $servers->filter(
                    function($server) use ($value) {
                        return ($value != '') ? $server->getHddSize() >= $value : $server;
                    }
                )->getValues();
              break;
            case "hddMax":
                return $servers->filter(
                    function($server) use ($value) {
                        return ($value != '') ? $server->getHddSize() <= $value : $server;
                    }
                )->getValues();
              break;
            case "hddType":
                return $servers->filter(
                    function($server) use ($value) {
                        return ($value != '') ? $server->getHddType() == $value : $server;
                    }
                )->getValues();
              break;
            case "location":
                return $servers->filter(
                    function($server) use ($value) {
                        return ($value != '') ? $server->getLocation() == $value : $server;
                    }
                )->getValues();
                break;
            case "ram":
                return $servers->filter(
                    function($server) use ($value) {
                        $data = explode(',', $value);
                        return ($value != '') ? in_array($server->getRam(), $data) : $server;
                    }
                )->getValues();
                break;
            default:
              return $servers; 
        }
    }
}
