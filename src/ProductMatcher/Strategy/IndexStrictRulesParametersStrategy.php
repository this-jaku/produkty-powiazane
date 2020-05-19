<?php

namespace ProductMatcher\Strategy;

class IndexStrictRulesParametersStrategy implements ProductMatchStrategyInterface
{
    public function match(array $products, array $rules): array
    {
        $findRules = $rules['findProducts'];
        $findRules = $this->orderRulesByLessResourceDemanding($findRules);

        $matchRules = $rules['matchProducts'];
        $commonMatchRules = $this->getCommonMatchCriteriaRules($matchRules);

        $strictMatchRulesParameters = $this->getParametersOfStrictMatchCriteria($matchRules);

        $foundedProducts = [];
        $productsMappedByParameters = [];
        foreach ($products as $product) {
            if ($this->checkProductAgainstRules($product, $findRules)) {
                $foundedProducts[] = $product;
            }

            $productMeetCommonMatchCriteria = $this->checkProductAgainstRules($product, $commonMatchRules);
            if (!$productMeetCommonMatchCriteria) {
                continue;
            }

            if (!$this->isProductHasRequiredParameters($product, $strictMatchRulesParameters)) {
                continue;
            }

            foreach ($strictMatchRulesParameters as $matchParameter) {
                $parameterValue = $product['parameters'][$matchParameter];

                if (!isset($productsMappedByParameters[$matchParameter][$parameterValue])) {
                    $productsMappedByParameters[$matchParameter][$parameterValue] = [];
                }

                $productsMappedByParameters[$matchParameter][$parameterValue][] = $product['symbol'];
            }
        }

        $matchedProducts = [];
        foreach ($foundedProducts as $foundedProduct) {
            if (!$this->isProductHasRequiredParameters($foundedProduct, $strictMatchRulesParameters)) {
                $matchedProducts[$foundedProduct['symbol']] = [];
                continue;
            }

            $matchedByParameter = [];
            foreach ($strictMatchRulesParameters as $matchParameter) {
                $parameterValue = $foundedProduct['parameters'][$matchParameter];
                $isAnyProductMatchToParameterValue = isset($productsMappedByParameters[$matchParameter][$parameterValue]);
                if ($isAnyProductMatchToParameterValue) {
                    $matchedByParameter[] = $productsMappedByParameters[$matchParameter][$parameterValue];
                }
            }

            $allParametersHasProducts = count($strictMatchRulesParameters) === count($matchedByParameter);
            if (!$allParametersHasProducts) {
                $matchedProducts[$foundedProduct['symbol']] = [];
                continue;
            }

            $matchedSymbols = [];
            $matchedByParameterCount = count($matchedByParameter);
            if ($matchedByParameterCount > 1) {
                $matchedByParameter = array_intersect(...$matchedByParameter);
                $matchedSymbols = array_values($matchedByParameter);
            } elseif ($matchedByParameterCount === 1) {
                $matchedByParameter = $matchedByParameter[0];
                $matchedSymbols = array_values($matchedByParameter);
            }

            $matchedProducts[$foundedProduct['symbol']] = $matchedSymbols;
        }
        return $matchedProducts;
    }

    public function checkProductAgainstRules(&$product, &$rules): bool
    {
        foreach ($rules as $rule) {
            switch ($rule['equals']) {
                case 'any':
                case 'this':
                    $productPropertyName = $rule['parameter'];
                    if (!isset($product['parameters'][$productPropertyName])) {
                        return false;
                    }
                    break;
                case 'is empty':
                    $productPropertyName = $rule['parameter'];
                    if (isset($product['parameters'][$productPropertyName])) {
                        return false;
                    }
                    break;
                default:
                    $productPropertyName = $rule['parameter'];
                    $propertyValueIsCorrect = isset($product['parameters'][$productPropertyName])
                        && $product['parameters'][$productPropertyName] === $rule['equals'];

                    if (!$propertyValueIsCorrect) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    public function orderRulesByLessResourceDemanding(array $rules)
    {
        usort(
            $rules,
            function ($rule1, $rule2) {
                if ($rule1['equals'] === $rule2['equals']) {
                    return 0;
                }

                return ($rule1['equals'] === 'any') ? -1 : 1;
            }
        );

        return $rules;
    }

    public function getCommonMatchCriteriaRules(array $rules): array
    {
        return array_filter(
            $rules,
            function ($rule) {
                return $rule['equals'] !== 'this';
            }
        );
    }

    public function getStrictMatchCriteriaRules(array $rules): array
    {
        return array_filter(
            $rules,
            function ($rule) {
                return $rule['equals'] === 'this';
            }
        );
    }

    public function getParametersOfStrictMatchCriteria(array $rules): array
    {
        $strictMatchRules = $this->getStrictMatchCriteriaRules($rules);
        $parameters = [];
        foreach ($strictMatchRules as $matchRule) {
            $parameters[] = $matchRule['parameter'];
        }

        return array_unique($parameters);
    }

    public function isProductHasRequiredParameters(array $product, array $requiredParameters): bool
    {
        $productParametersKeys = array_keys($product['parameters']);

        return empty(array_diff($requiredParameters, $productParametersKeys));
    }
}