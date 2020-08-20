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
use App\DataProvider\DataProviderInterface;

/**
 * Description of ApiController
 *
 * @author ramkumar
 */
class ApiController
{
    private $dataProvider;
    
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @Route(
     *     name="api_filters",
     *     path="/api/filters"
     * )
     */
    public function __invoke($data=null)
    {
        $filters = $this->dataProvider->getFilterParams();
        return new JsonResponse($filters);
    }
}
