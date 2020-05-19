<?php

namespace ProductMatcher\Strategy;

interface ProductMatchStrategyInterface
{
    public function match(array $products, array $rules): array;
}