<?php

use PHPUnit\Framework\TestCase;

class IndexStrictRulesParametersStrategyTest extends TestCase
{
    public function testPositiveMatchBasicDemo()
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
        $strategy = (new \ProductMatcher\Strategy\IndexStrictRulesParametersStrategy());
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

    public function testPositiveMatchPartOfBigProductsAndRule()
    {
        // given
        $products = [
            [
                "id" => "481",
                "symbol" => "0154.125DR",
                "id_category" => "113013",
                "parameters" => [
                    "Kategoria" => "113013",
                    "Producent" => "LITTELFUSE",
                    "Montaż" => "SMD",
                    "Obudowa" => "2410",
                    "Charakterystyka bezpiecznika" => "superszybki",
                    "Rodzaj bezpiecznika" => "ceramiczny",
                    "Rozmiar bezpiecznika" => "9,9x3,8x5mm",
                    "Typ bezpiecznika" => "topikowy",
                    "Napięcie znamionowe" => "125V DC",
                    "Prąd znamionowy" => "125mA"
                ]
            ],
            [
                "id" => "482",
                "symbol" => "0154.250DR",
                "id_category" => "113013",
                "parameters" => [
                    "Kategoria" => "113013",
                    "Producent" => "LITTELFUSE",
                    "Montaż" => "SMD",
                    "Obudowa" => "2410",
                    "Charakterystyka bezpiecznika" => "superszybki",
                    "Rodzaj bezpiecznika" => "ceramiczny",
                    "Rozmiar bezpiecznika" => "9,9x3,8x5mm",
                    "Typ bezpiecznika" => "topikowy",
                    "Napięcie znamionowe" => "125V DC",
                    "Prąd znamionowy" => "250mA"
                ]
            ],
            [
                "id" => "483",
                "symbol" => "0154.500DR",
                "id_category" => "113013",
                "parameters" => [
                    "Kategoria" => "113013",
                    "Producent" => "LITTELFUSE",
                    "Montaż" => "SMD",
                    "Obudowa" => "2410",
                    "Charakterystyka bezpiecznika" => "superszybki",
                    "Rodzaj bezpiecznika" => "ceramiczny",
                    "Rozmiar bezpiecznika" => "9,9x3,8x5mm",
                    "Typ bezpiecznika" => "topikowy",
                    "Napięcie znamionowe" => "125V DC",
                    "Prąd znamionowy" => "500mA"
                ]
            ]
        ];

        $rules = [
            "name" => "Do każdego produktu z parametrem Obudowa dopasuj",
            "findProducts" => [
                [
                    "parameter" => "Obudowa",
                    "equals" => "any"
                ]
            ],
            "matchProducts" => [
                [
                    "parameter" => "Obudowa",
                    "equals" => "this"
                ],
                [
                    "parameter" => "Montaż",
                    "equals" => "SMD"
                ]
            ]
        ];

        // when
        $strategy = (new \ProductMatcher\Strategy\IndexStrictRulesParametersStrategy());
        $result = $strategy->match($products, $rules);

        // then
        $this->assertSame(
            [
                "0154.125DR" => [
                    "0154.125DR",
                    "0154.250DR",
                    "0154.500DR"
                ],
                "0154.250DR" => [
                    "0154.125DR",
                    "0154.250DR",
                    "0154.500DR"
                ],
                "0154.500DR" => [
                    "0154.125DR",
                    "0154.250DR",
                    "0154.500DR"
                ]
            ],
            $result
        );
    }
}