<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Server;
use Symfony\Component\HttpFoundation\RequestStack;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\Data\PrepareData;
use App\Service\SearchService;

final class SpreadSheetDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $requestStack;
    private $resourceMetadataFactory;
    private $itemsPerPageParameter;
    private $globalItemsPerPage;
    private $pageParameter;
    private $prepareData;
    private $searchService;

    public function __construct(
            SearchService $searchService,
            RequestStack $requestStack,
            ResourceMetadataFactoryInterface $resourceMetadataFactory,
            int $itemsPerPage = 10,
            int $globalItemsPerPage = 10,
            PrepareData $prepareData)
    {
        $this->requestStack = $requestStack;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->itemsPerPageParameter = $itemsPerPage;
        $this->globalItemsPerPage = $globalItemsPerPage;
        $this->pageParameter = "_page";
        $this->prepareData = $prepareData;
        $this->searchService = $searchService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Server::class === $resourceClass;
    }

    /**
     * Return the server collection response api from excel sheet
     * @param string $resourceClass
     * @param string $operationName
     * @return type
     * @throws ResourceClassNotSupportedException
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        if(!$this->supports($resourceClass)) {
            throw new ResourceClassNotSupportedException();
        }

        $request = $this->requestStack->getCurrentRequest();
        
        $serverData = new ArrayCollection();

        if ( $xlsx = \SimpleXLSX::parse('../src/Data/a.xlsx') ) {
            foreach($xlsx->rows() as $i => $server){
                $hddData = $this->prepareData->prepareHddData($server[3]);
                $ramData = $this->prepareData->prepareRamData($server[2]);
                $price = $this->prepareData->preparePriceAndCurrency($server[5]);                
                $servers = new Server($i+1,$server[1],$ramData,$hddData,$server[4],$price);
                $serverData->add($servers);
            }
            
            if(!empty($request->query->all()))
            {
                foreach ($request->query->all() as $filter => $value) {
                    $serverData = $this->searchService->filter($filter, $value, $serverData);
                    if(is_array($serverData)){
                        $serverData = new ArrayCollection($serverData);
                    }
                }
            }

        }else{
            echo \SimpleXLSX::parseError();
        }
        
        $adapter = new CollectionAdapter($serverData);
        $pagerfanta = new \App\Pagination\Pagerfanta($adapter);

        $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

        $pagerfanta->setMaxPerPage(
            (int) $request->get(
                $this->itemsPerPageParameter,
                $resourceMetadata->getCollectionOperationAttribute(
                    $operationName,
                    'pagination_items_per_page',
                    $this->globalItemsPerPage,
                    true
                )
            )
        );

        $pagerfanta->setCurrentPage((int) $request->get($this->pageParameter, 1));
        
        $response = [
            'total' => $pagerfanta->getNbResults(),
            'currentPage' => $pagerfanta->getCurrentPage(),
            'itemsPerPage' => $this->globalItemsPerPage,
            'servers' => $this->toArray($pagerfanta),
        ];
        return $response;
    }
    
    public function toArray($data) {
        $array = array ();
        foreach ( $data as $key => $value ) {
            $array [ltrim ( $key, '_' )] = $value;
        }
        return array_values($array);
    }
}
