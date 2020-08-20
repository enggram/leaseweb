<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DataProvider;

use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\Collections\ArrayCollection;
use App\Data\PrepareData;
use App\Service\SearchService;
use App\Entity\Server;

/**
 * Description of SpreadSheetData
 *
 * @author ramkumar
 */
class SpreadSheetData implements DataProviderInterface
{
    /** KernelInterface $appKernel */
    private $appKernel;
    private $prepareData;
    private $searchService;
    
    public function __construct(
            KernelInterface $appKernel,
            PrepareData $prepareData,
            SearchService $searchService
    )
    {
        $this->appKernel = $appKernel;
        $this->prepareData = $prepareData;
        $this->searchService = $searchService;
    }
    
    public function getData()
    {
        $projDirectory = $this->appKernel->getProjectDir();
        
        $serverData = new ArrayCollection();
        
        if ( $xlsx = \SimpleXLSX::parse($projDirectory.'/src/Data/a.xlsx') ) {
            foreach($xlsx->rows() as $i => $server){
                $hddData = $this->prepareData->prepareHddData($server[3]);
                $ramData = $this->prepareData->prepareRamData($server[2]);
                $price = $this->prepareData->preparePriceAndCurrency($server[5]);                
                $servers = new Server($i+1,$server[1],$ramData,$hddData,$server[4],$price);
                $serverData->add($servers);
            }
        }else{
            echo \SimpleXLSX::parseError();
        }
        
        return $serverData;
    }

    public function applyFilter($request)
    {   
        $serverData = $this->getData();
        foreach ($request->query->all() as $filter => $value) {
            $serverData = $this->searchService->filter($filter, $value, $serverData);
            if(is_array($serverData)){
                $serverData = new ArrayCollection($serverData);
            }
        }
        
        return $serverData;
    }
    
    public function getFilterParams()
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
        
        return $filters;
    }
}
