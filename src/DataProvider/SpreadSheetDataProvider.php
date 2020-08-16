<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Server;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\Data\PrepareData;
use App\Service\SearchService;
use Symfony\Component\HttpKernel\KernelInterface;
use ApiPlatform\Core\DataProvider\ArrayPaginator;

final class SpreadSheetDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $requestStack;
    private $resourceMetadataFactory;
    private $pageParameter;
    private $prepareData;
    private $searchService;
    /** KernelInterface $appKernel */
    private $appKernel;

    public function __construct(
            SearchService $searchService,
            RequestStack $requestStack,
            ResourceMetadataFactoryInterface $resourceMetadataFactory,
            PrepareData $prepareData,
            KernelInterface $appKernel)
    {
        $this->requestStack = $requestStack;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->pageParameter = "_page";
        $this->prepareData = $prepareData;
        $this->searchService = $searchService;
        $this->appKernel = $appKernel;
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
        
        $projDirectory = $this->appKernel->getProjectDir();

        if ( $xlsx = \SimpleXLSX::parse($projDirectory.'/src/Data/a.xlsx') ) {
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
        
        $page = $request->get('_page');
        $firstResult = ($page && $page != 1) ? (($page-1)*10): 0;
        return new ArrayPaginator($this->toArray($serverData),$firstResult,10);
    }
    
    public function toArray($data) {
        $array = array ();
        foreach ( $data as $key => $value ) {
            $array [ltrim ( $key, '_' )] = $value;
        }
        return array_values($array);
    }
}
