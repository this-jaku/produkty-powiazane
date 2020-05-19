<?php

namespace ProductMatcher\Strategy;

class FilterMatchedByGeneralRulesStrategy implements ProductMatchStrategyInterface
{
    public function match(array $products, array $rules): array
    {
        $findRules = $rules['findProducts'];
        $findRules = $this->orderRulesByLessResourceDemanding($findRules);

        $matchRules = $rules['matchProducts'];
        $matchGeneralRules = $this->getGeneralCriteriaRules($matchRules);

        $foundedProducts = [];
        $matchedGeneralRulesProducts = [];

        foreach ($products as $product) {
            if ($this->checkProductAgainstRules($product, $findRules)) {
                $foundedProducts[] = $product;
            }
            if ($this->checkProductAgainstRules($product, $matchGeneralRules)) {
                $matchedGeneralRulesProducts[] = $product;
            }
        }

        unset($products, $rules, $findRules);

        $matchStrictRules = $this->getStrictCriteriaRules($matchRules);

        $matchedProducts = [];
        foreach ($foundedProducts as $foundedProduct) {
            $matched = [];
            foreach ($matchedGeneralRulesProducts as $matchedProduct) {
                if ($this->checkProductAgainstStrictMatchRules($foundedProduct, $matchedProduct, $matchStrictRules)) {
                    $matched[] = $matchedProduct['symbol'];
                }
            }
            $matchedProducts[$foundedProduct['symbol']] = $matched;
        }

        return $matchedProducts;
    }

    public function checkProductAgainstStrictMatchRules(&$foundedProduct, &$matchedProduct, &$rules): bool
    {
        foreach ($rules as $rule) {
            $productParameter = $rule['parameter'];
            if ($foundedProduct['parameters'][$productParameter] !== $matchedProduct['parameters'][$productParameter]) {
                return false;
            }
        }

        return true;
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

    public function getGeneralCriteriaRules(array $rules): array
    {
        return array_filter(
            $rules,
            function ($rule) {
                return $rule['equals'] !== 'this';
            }
        );
    }

    public function getStrictCriteriaRules(array $rules): array
    {
        return array_filter(
            $rules,
            function ($rule) {
                return $rule['equals'] === 'this';
            }
        );
    }
}