<?php

namespace ProductMatcher\Strategy;

class ProductMatchStrategyFactory
{
    public function create(string $className): ProductMatchStrategyInterface
    {
        switch ($className) {
            case FilterMatchedByGeneralRulesStrategy::class;
                return new FilterMatchedByGeneralRulesStrategy();
            case IndexStrictRulesParametersStrategy::class:
                return new IndexStrictRulesParametersStrategy();
                break;
            default:
                throw new \RuntimeException("Strategy $className not exists");

        }
    }
}