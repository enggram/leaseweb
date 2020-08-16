<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Server
 *
 * @author ramkumar
 */
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\SearchAnnotation as Searchable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"read"}},
 *      denormalizationContext={"groups"={"write"}},
 *      collectionOperations={
 *          "get"={"method"="GET"},
 *          "location"={
 *              "method"="GET",
 *             "route_name"="api_filters"
 *          },
 *      },
 *      itemOperations={"get"={"method"="GET"}},
 *      attributes={
 *          "pagination_items_per_page"=10
 *      }
 * )
 * @ApiFilter(SearchFilter::class, properties={"ram": "partial"})
 */
class Server
{
    /**
     * @var string
     * @ApiProperty(identifier=true)
     * @Groups({"write"})
     */
    protected $id;
    
    /**
     * @var string
     * @Groups({"read"})
     */
    protected $model;
    
    /**
     * @var string
     * @Groups({"read"})
     */
    protected $ram;
    
    /**
     * @var string
     * @Groups({"write"})
     */
    protected $ramType;
    
    /**
     * @var string
     * @Groups({"read"})
     */
    protected $hdd;
            
    /**
     * @Groups({"write"})
     * @var string
     */
    protected $hddSize;
    
    /**
     * @Groups({"write"})
     * @var string
     */
    protected $hddType;
    
    /**
     * @Groups({"read","write"})
     * @var string
     */
    protected $location;
    
    /**
     * @Groups({"read","write"})
     * @var string
     */
    protected $currency;
    
    /**
     * @var string
     * @Groups({"read","write"})
     */
    protected $price;
    
    public function __construct($id, $model, $ram, $hdd, $location, $price)
    {
        $this->id = $id;
        $this->model = $model;
        $this->setHddData($hdd);
        $this->setRamData($ram);
        $this->setPriceData($price);
        $this->location = $location;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getRam()
    {
        return $this->ram;
    }

    public function getHdd()
    {
        return $this->hdd;
    }

    public function getHddSize()
    {
        return $this->hddSize;
    }
    
    public function getHddType()
    {
        return $this->hddType;
    }

    public function getLocation()
    {
        return $this->location;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function setHddData($hdd)
    {
        $this->hdd = $hdd['hdd'];
        $this->hddSize = $hdd['hddSize'];
        $this->hddType = $hdd['hddType'];
    }
    
    public function setRamData($ram)
    {
        $this->ramType = $ram['ramType'];
        $this->ram = $ram['ram'];
    }
    
    public function setPriceData($price)
    {
        $this->price = $price['price'];
        $this->currency = $price['currency'];
    }
}
