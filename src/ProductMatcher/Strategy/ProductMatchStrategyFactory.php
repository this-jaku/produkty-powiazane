<?php

namespace ProductMatcher\Strategy;

class ProductMatchStrategyFactory
{
    public function create(string $className): ProductMatchStrategyInterface
    {
        switch ($className) {
            case FilterMatchedByGeneralRulesStrategy::class;
                return new FilterMatchedByGeneralRulesStrategy();
            default:
                throw new \RuntimeException("Strategy $className not exists");

        }
    }
}