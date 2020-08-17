<?php

namespace App\Controller;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Data\PrepareData;

/**
 * Description of ApiController
 *
 * @author ramkumar
 */
class ApiController
{
    private $prepareData;
    
    public function __construct(PrepareData $prepareData)
    {
        $this->prepareData = $prepareData;
    }

    /**
     * @Route(
     *     name="api_filters",
     *     path="/api/filters"
     * )
     */
    public function __invoke($data=null)
    {
        $filters = [];
        $location = [];
        $ram = [];
        $hddType = [];
        
        if ( $xlsx = \SimpleXLSX::parse('../src/Data/a.xlsx') ) {
            foreach($xlsx->rows() as $i => $server){
                if(!in_array($server[4], $location)){
                    array_push($location, $server[4]);
                }
                $ramData = $this->prepareData->prepareRamData($server[2]);
                
                if(!in_array($ramData['ram'], $ram)){
                    array_push($ram, $ramData['ram']);
                }
                
                $hddData = $this->prepareData->prepareHddData($server[3]);
                
                if(!in_array($hddData['hddType'], $hddType)){
                    array_push($hddType, $hddData['hddType']);
                }
            }
        }else{
            echo \SimpleXLSX::parseError();
        }
        sort($ram);
        $filters['location'] = $location;
        $filters['ram'] = $ram;
        $filters['hddType'] = $hddType;
        
        return new JsonResponse($filters);
    }
}
