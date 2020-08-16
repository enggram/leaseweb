<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Filter;

use ApiPlatform\Core\Api\FilterInterface;

/**
 * Description of SearchFilter
 *
 * @author ramkumar
 */
class SearchFilter implements FilterInterface
{
    /**
     * @var string Exact matching
     */
    const STRATEGY_EXACT = 'exact';

    /**
     * @var string The value must be contained in the field
     */
    const STRATEGY_PARTIAL = 'partial';

    /**
     * @var string Finds fields that are starting with the value
     */
    const STRATEGY_START = 'start';

    /**
     * @var string Finds fields that are ending with the value
     */
    const STRATEGY_END = 'end';

    /**
     * @var string Finds fields that are starting with the word
     */
    const STRATEGY_WORD_START = 'word_start';

    protected $properties;

    /**
     * SearchFilter constructor.
     * @param array|null $properties
     */
    public function __construct(array $properties = null)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = $this->properties;

        foreach ($properties as $property => $strategy) {

                $filterParameterNames = [
                    $property,
                ];

                foreach ($filterParameterNames as $filterParameterName) {
                    $description[$filterParameterName] = [
                        'property' => $property,
                        'type' => 'string',
                        'required' => false,
                        'strategy' => self::STRATEGY_EXACT,
                    ];
                }
            }

        return $description;
    }

}
