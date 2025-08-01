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
    
    private const BET_AMOUNT = 5.00;
    
    // Tabela de prêmios baseada nos valores dos símbolos
    private const PRIZE_MULTIPLIERS = [
        // 3 símbolos iguais = valor do símbolo como prêmio
        'three_same' => 1.0,
        
        // Padrões especiais multiplicam o valor base
        'horizontal_line' => 2.0,
        'vertical_line' => 2.0,
        'diagonal' => 3.0,
        'corners' => 4.0, // 4 cantos iguais
    ];

    // IDs com chance especial de 70%
    private const VIP_USER_IDS = [1, 2, 3]; // Adicione os IDs que você quiser 

    private $userId;

    // CONSTRUTOR DEVE ACEITAR NULL
    public function __construct($userId = null)
    {
        $this->userId = $userId;
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
        \Log::info("User ID no shouldWin: " . $userId);
        
        if ($userId && in_array($userId, self::VIP_USER_IDS)) {
            \Log::info("Usuário VIP detectado! Chance de 70%");
            return rand(1, 100) <= 70;
        }
        
        \Log::info("Usuário normal. Chance de 20%");
        return rand(1, 100) <= 20;
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
            'three_symbols' => 60,     // 60% - mais comum
            'horizontal_line' => 20,   // 20%
            'vertical_line' => 15,     // 15%
            'diagonal' => 4,           // 4%
            'corners' => 1,            // 1% - mais raro
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
        
        $grid = $this->createRandomGrid();
        
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
        $grid = $this->createRandomGrid();
        $lineIndex = rand(0, 2);
        $value = $this->selectWeightedSymbol();
        
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
        $grid = $this->createRandomGrid();
        $colIndex = rand(0, 2);
        $value = $this->selectWeightedSymbol();
        
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
        $grid = $this->createRandomGrid();
        $value = $this->selectWeightedSymbol();
        
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
        $grid = $this->createRandomGrid();
        $value = $this->selectWeightedSymbol();
        
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
        $weights = [
            50 => 25,     // R$ 0,50 - 25%
            100 => 20,    // R$ 1,00 - 20%
            200 => 15,    // R$ 2,00 - 15%
            500 => 12,    // R$ 5,00 - 12%
            1000 => 10,   // R$ 10,00 - 10%
            2000 => 8,    // R$ 20,00 - 8%
            5000 => 5,    // R$ 50,00 - 5%
            10000 => 3,   // R$ 100,00 - 3%
            20000 => 1.5, // R$ 200,00 - 1.5%
            50000 => 0.3, // R$ 500,00 - 0.3%
            100000 => 0.15, // R$ 1.000,00 - 0.15%
            200000 => 0.05  // R$ 2.000,00 - 0.05%
        ];

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