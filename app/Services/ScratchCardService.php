<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class ScratchCardService
{
    // Símbolos são os próprios valores em centavos (para facilitar cálculos)
    private const SYMBOLS = [
        50,    // R$ 0,50
        100,   // R$ 1,00
        200,   // R$ 2,00
        500,   // R$ 5,00
        1000,  // R$ 10,00
        2000,  // R$ 20,00
        5000,  // R$ 50,00
        10000, // R$ 100,00
        20000, // R$ 200,00
        50000, // R$ 500,00
        100000, // R$ 1.000,00
        200000  // R$ 2.000,00
    ];

    private const BET_AMOUNT = 1.00;

    // Tabela de prêmios baseada nos valores dos símbolos
    private const PRIZE_MULTIPLIERS = [
        // 3 símbolos iguais = valor do símbolo como prêmio
        'three_same' => 1.0,

        // Padrões especiais multiplicam o valor base
        'horizontal_line' => 1.5,
        'vertical_line' => 1.5,
        'diagonal' => 2.0,
        'corners' => 2.5, // 4 cantos iguais
    ];

    private $userId;

    // CONSTRUTOR DEVE ACEITAR NULL
    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    private function getVipUserIds()
    {
        // Buscar usuários demo do banco de dados
        $demoUsers = \App\Models\User::where('demo', true)->pluck('id')->toArray();
        return $demoUsers;
    }

    public function generateCard()
    {
        $willWin = $this->shouldWin();

        if ($willWin) {
            return $this->generateWinningCard();
        } else {
            return $this->generateLosingCard();
        }
    }

    private function shouldWin()
    {
        $userId = auth()->id();

        if ($userId && in_array($userId, $this->getVipUserIds())) {
            return rand(1, 100) <= 90;
        }
        return rand(1, 100) <= 18;
    }

    private function generateWinningCard()
    {
        $winType = $this->selectWinType();

        switch ($winType) {
            case 'horizontal_line':
                return $this->createHorizontalLine();
            case 'vertical_line':
                return $this->createVerticalLine();
            case 'diagonal':
                return $this->createDiagonal();
            case 'corners':
                return $this->createCorners();
            default:
                return $this->createThreeSymbols();
        }
    }

    private function selectWinType()
    {
        $types = [
            'three_symbols' => 30,     
            'horizontal_line' => 2,   
            'vertical_line' => 2,     
            'diagonal' => 0,          
            'corners' => 0,            
        ];

        $rand = rand(1, 100);
        $accumulated = 0;

        foreach ($types as $type => $probability) {
            $accumulated += $probability;
            if ($rand <= $accumulated) {
                return $type;
            }
        }

        return 'three_symbols';
    }

    private function createThreeSymbols()
    {
        // Escolher valor vencedor com probabilidade baseada no valor
        $winningValue = $this->selectWeightedSymbol();

        // Criar grid limpo sem o valor do prêmio
        $grid = $this->createCleanRandomGrid($winningValue);

        // Colocar 3 símbolos iguais em posições aleatórias
        $positions = $this->getRandomPositions(3);

        foreach ($positions as $pos) {
            $grid[$pos[0]][$pos[1]] = $winningValue;
        }

        $prize = $winningValue / 100; // Converter centavos para reais

        return [
            'grid' => $grid,
            'win_type' => 'three_same',
            'winning_value' => $winningValue,
            'prize' => $prize
        ];
    }

    private function createHorizontalLine()
    {
        $value = $this->selectWeightedSymbol();
        $grid = $this->createCleanRandomGrid($value);
        $lineIndex = rand(0, 2);

        // Preencher linha inteira
        for ($col = 0; $col < 3; $col++) {
            $grid[$lineIndex][$col] = $value;
        }

        $prize = ($value / 100) * self::PRIZE_MULTIPLIERS['horizontal_line'];

        return [
            'grid' => $grid,
            'win_type' => 'horizontal_line',
            'winning_value' => $value,
            'prize' => $prize
        ];
    }

    private function createVerticalLine()
    {
        $value = $this->selectWeightedSymbol();
        $grid = $this->createCleanRandomGrid($value);
        $colIndex = rand(0, 2);

        // Preencher coluna inteira
        for ($row = 0; $row < 3; $row++) {
            $grid[$row][$colIndex] = $value;
        }

        $prize = ($value / 100) * self::PRIZE_MULTIPLIERS['vertical_line'];

        return [
            'grid' => $grid,
            'win_type' => 'vertical_line',
            'winning_value' => $value,
            'prize' => $prize
        ];
    }

    private function createDiagonal()
    {
        $value = $this->selectWeightedSymbol();
        $grid = $this->createCleanRandomGrid($value);

        if (rand(0, 1)) {
            // Diagonal principal
            $grid[0][0] = $value;
            $grid[1][1] = $value;
            $grid[2][2] = $value;
        } else {
            // Diagonal secundária
            $grid[0][2] = $value;
            $grid[1][1] = $value;
            $grid[2][0] = $value;
        }

        $prize = ($value / 100) * self::PRIZE_MULTIPLIERS['diagonal'];

        return [
            'grid' => $grid,
            'win_type' => 'diagonal',
            'winning_value' => $value,
            'prize' => $prize
        ];
    }

    private function createCorners()
    {
        $value = $this->selectWeightedSymbol();
        $grid = $this->createCleanRandomGrid($value);

        // 4 cantos
        $grid[0][0] = $value;
        $grid[0][2] = $value;
        $grid[2][0] = $value;
        $grid[2][2] = $value;

        $prize = ($value / 100) * self::PRIZE_MULTIPLIERS['corners'];

        return [
            'grid' => $grid,
            'win_type' => 'corners',
            'winning_value' => $value,
            'prize' => $prize
        ];
    }

    /**
     * Gera posições aleatórias únicas no grid 3x3
     * 
     * @param int $count Quantidade de posições a gerar
     * @return array Array de posições [linha, coluna]
     */
    private function getRandomPositions($count)
    {
        $positions = [];
        $used = [];

        while (count($positions) < $count) {
            $row = rand(0, 2);
            $col = rand(0, 2);
            $key = "$row,$col";

            if (!in_array($key, $used)) {
                $positions[] = [$row, $col];
                $used[] = $key;
            }
        }

        return $positions;
    }

    private function selectWeightedSymbol()
    {
        // Valores menores têm maior probabilidade
        $userId = auth()->id();

        if ($userId && in_array($userId, $this->getVipUserIds())) {
            $weights = [
                500 => 40,
                1000 => 40,
                2000 => 40,
                5000 => 30,
                10000 => 50,
                20000 => 30,
                50000 => 20,
                100000 => 10,
                200000 => 1
            ];
        } else {
            $weights = [
                50 => 35,   // 50 centavos
                100 => 20,  // 1 real 
                200 => 10,  // 2 reais
                500 => 8,   // 5 reais
                1000 => 5,  // 10 reais
                2000 => 5,  // 20 reais
                5000 => 1,  // 50 reais
                10000 => 0, // 100 reais
                20000 => 0  // 200 reais
            ];
        }

        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight * 100) / 100;

        $accumulated = 0;
        foreach ($weights as $value => $weight) {
            $accumulated += $weight;
            if ($random <= $accumulated) {
                return $value;
            }
        }

        return 50; // Fallback
    }

    private function generateLosingCard()
    {
        $grid = $this->createRandomGrid();

        // Garantir que não há combinação vencedora
        $attempts = 0;
        while ($this->checkIfWinning($grid) && $attempts < 50) {
            $grid = $this->createRandomGrid();
            $attempts++;
        }

        // Se ainda estiver ganhando após 50 tentativas, force uma perda
        if ($this->checkIfWinning($grid)) {
            $grid = $this->forceLosingGrid();
        }

        return [
            'grid' => $grid,
            'win_type' => null,
            'winning_value' => null,
            'prize' => 0
        ];
    }

    /**
     * Força a criação de um grid perdedor
     */
    private function forceLosingGrid()
    {
        $symbols = self::SYMBOLS;
        return [
            [$symbols[0], $symbols[1], $symbols[2]],
            [$symbols[3], $symbols[4], $symbols[5]],
            [$symbols[6], $symbols[7], $symbols[8]]
        ];
    }

    private function createRandomGrid()
    {
        $grid = [];
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $grid[$row][$col] = self::SYMBOLS[array_rand(self::SYMBOLS)];
            }
        }
        return $grid;
    }

    /**
     * Cria um grid base garantindo que não há valores repetidos
     * exceto aqueles que serão definidos como prêmio
     */
    private function createCleanRandomGrid($excludeValue = null)
    {
        $availableSymbols = self::SYMBOLS;
        
        // Remove o valor do prêmio dos símbolos disponíveis se especificado
        if ($excludeValue !== null) {
            $availableSymbols = array_filter($availableSymbols, function($symbol) use ($excludeValue) {
                return $symbol !== $excludeValue;
            });
        }
        
        $grid = [];
        $usedValues = [];
        
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                // Tenta encontrar um valor que não foi usado ainda
                $attempts = 0;
                do {
                    $value = $availableSymbols[array_rand($availableSymbols)];
                    $attempts++;
                } while (in_array($value, $usedValues) && $attempts < 20);
                
                $grid[$row][$col] = $value;
                $usedValues[] = $value;
                
                // Se usamos todos os símbolos disponíveis, resetamos a lista
                if (count($usedValues) >= count($availableSymbols)) {
                    $usedValues = [];
                }
            }
        }
        
        return $grid;
    }

    private function checkIfWinning($grid)
    {
        // Verificar linhas horizontais
        for ($row = 0; $row < 3; $row++) {
            if ($grid[$row][0] === $grid[$row][1] && $grid[$row][1] === $grid[$row][2]) {
                return true;
            }
        }

        // Verificar colunas verticais
        for ($col = 0; $col < 3; $col++) {
            if ($grid[0][$col] === $grid[1][$col] && $grid[1][$col] === $grid[2][$col]) {
                return true;
            }
        }

        // Verificar diagonais
        if ($grid[0][0] === $grid[1][1] && $grid[1][1] === $grid[2][2]) {
            return true;
        }
        if ($grid[0][2] === $grid[1][1] && $grid[1][1] === $grid[2][0]) {
            return true;
        }

        // Verificar 4 cantos
        if ($grid[0][0] === $grid[0][2] && $grid[0][2] === $grid[2][0] && $grid[2][0] === $grid[2][2]) {
            return true;
        }

        // Verificar 3 valores iguais em qualquer lugar
        $valueCount = [];
        foreach ($grid as $row) {
            foreach ($row as $value) {
                $valueCount[$value] = ($valueCount[$value] ?? 0) + 1;
                if ($valueCount[$value] >= 3) {
                    return true;
                }
            }
        }

        return false;
    }

    public function formatValue($centavos)
    {
        return 'R$ ' . number_format($centavos / 100, 2, ',', '.');
    }

    public function getBetAmount()
    {
        return self::BET_AMOUNT;
    }

    // Helper para converter valores para exibição
    public static function getDisplayValues()
    {
        return [
            50 => 'R$ 0,50',
            100 => 'R$ 1,00',
            200 => 'R$ 2,00',
            500 => 'R$ 5,00',
            1000 => 'R$ 10,00',
            2000 => 'R$ 20,00',
            5000 => 'R$ 50,00',
            10000 => 'R$ 100,00',
            20000 => 'R$ 200,00',
            50000 => 'R$ 500,00',
            100000 => 'R$ 1.000,00',
            200000 => 'R$ 2.000,00'
        ];
    }
}
