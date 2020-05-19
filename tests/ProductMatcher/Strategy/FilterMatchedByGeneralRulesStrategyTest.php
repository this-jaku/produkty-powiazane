<?php

use PHPUnit\Framework\TestCase;

class FilterMatchedByGeneralRulesStrategyTest extends TestCase
{
    public function testPositiveMatch()
    {
        // given
        $products = [
            [
                "id" => "42",
                "symbol" => "0000.1101.0606",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "męski",
                    "Długość" => "23.2345",
                    "Szerokość" => "23.2345"
                ]
            ],
            [
                "id" => "43",
                "symbol" => "0000.1101.0607",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "męski",
                    "Długość" => "25",
                    "Szerokość" => "25"
                ]
            ],
            [
                "id" => "44",
                "symbol" => "0000.1101.0610",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "żeński",
                    "Długość" => "23.2345",
                    "Szerokość" => "23.2345"
                ]
            ],
            [
                "id" => "45",
                "symbol" => "0000.1101.0611",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "żeński",
                    "Długość" => "25",
                    "Szerokość" => "25"
                ]
            ],
            [
                "id" => "47",
                "symbol" => "0000.1101.0615",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "żeński",
                    "Długość" => "23.2345",
                    "Szerokość" => "23.2345"
                ]
            ],
            [
                "id" => "48",
                "symbol" => "0000.1101.1018",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Typ akcesoriów do czujników" => "obudowa"
                ]
            ],
            [
                "id" => "49",
                "symbol" => "0000.1101.0608",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "męski",
                    "Długość" => "20",
                    "Szerokość" => "20"
                ]
            ],
            [
                "id" => "50",
                "symbol" => "0000.1101.0609",
                "id_category" => "112995",
                "parameters" => [
                    "Kategoria" => "112995",
                    "Rodzaj konektora" => "męski"
                ]
            ]
        ];

        $rules = [
            "name" => "Wtyki męskie pasują do gniazda żeńskiego",
            "findProducts" => [
                [
                    "parameter" => "Kategoria",
                    "equals" => "112995"
                ],
                [
                    "parameter" => "Rodzaj konektora",
                    "equals" => "męski"
                ],
                [
                    "parameter" => "Długość",
                    "equals" => "any"
                ],
                [
                    "parameter" => "Szerokość",
                    "equals" => "any"
                ]
            ],
            "matchProducts" => [
                [
                    "parameter" => "Kategoria",
                    "equals" => "this"
                ],
                [
                    "parameter" => "Rodzaj konektora",
                    "equals" => "żeński"
                ],
                [
                    "parameter" => "Długość",
                    "equals" => "this"
                ],
                [
                    "parameter" => "Szerokość",
                    "equals" => "this"
                ]
            ]
        ];

        // when
        $strategy = (new \ProductMatcher\Strategy\FilterMatchedByGeneralRulesStrategy());
        $result = $strategy->match($products, $rules);

        // then
        $this->assertSame(
            [
                "0000.1101.0606" => [
                    "0000.1101.0610",
                    "0000.1101.0615"
                ],
                "0000.1101.0607" => [
                    "0000.1101.0611"
                ],
                "0000.1101.0608" => [
                ]
            ],
            $result
        );
    }
}