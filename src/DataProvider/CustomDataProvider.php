<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Server;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\DataProvider\ArrayPaginator;
use App\DataProvider\DataProviderInterface;

final class CustomDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $requestStack;
    private $resourceMetadataFactory;
    private $pageParameter;
    private $dataProvider;

    public function __construct(
            RequestStack $requestStack,
            ResourceMetadataFactoryInterface $resourceMetadataFactory,
            DataProviderInterface $dataProvider)
    {
        $this->requestStack = $requestStack;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->pageParameter = "_page";
        $this->dataProvider = $dataProvider;
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

        $serverData = $this->dataProvider->getData();
                
        if(!empty($request->query->all()))
        {
            $serverData = $this->dataProvider->applyFilter($request);
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
