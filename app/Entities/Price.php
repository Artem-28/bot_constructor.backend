<?php

namespace App\Entities;

use App\Enums\EnumDiscount;
use App\Enums\EnumPrice;
use App\Models\Discount\Coupon;
use App\Models\Discount\Sale;

class Price
{
    public float $current;  // текущая цена
    public float $old; // старая цена
    public float $sale; // размер скидки
    public string $currency; // валюта
    private int $_increment; // общее колличество товара
    private float $_percentSale; // общее колличество процента скидки
    private float $_priceSale; // общее колличество не процентной скидки

    public function __construct(int $value, string $currency = EnumPrice::CURRENCY_RUB)
    {
        $this->current = $value;
        $this->old = $value;
        $this->currency = $currency;
        $this->sale = 0;
        $this->_increment = 1;
        $this->_percentSale = 0;
        $this->_priceSale = 0;
    }

    // Расчет и установка значений
    private function _calculate(): void
    {
        $totalSale = ($this->old * $this->_increment / 100 * $this->_percentSale) + $this->_priceSale;
        $this->current = round(max(0, $this->old - $totalSale / $this->_increment), 2);
        $this->sale = round(min($this->old, $totalSale / $this->_increment), 2);
    }

    // Установка скидки
    public function setSale(Sale | Coupon $sale): Price
    {
        if ($sale->currency !== $this->currency) return $this;

        switch ($sale->unit) {
            case EnumDiscount::UNIT_VALUE_PERCENT:
                $this->_percentSale += $sale->value;
                break;
            case EnumDiscount::UNIT_VALUE_PRICE:
                $this->_priceSale += $sale->value;
                break;
            default:
                break;
        }

        $this->_calculate();
        return $this;
    }

    // Увеличение колличества товара
    public function increment(int $count): Price
    {
        if (!$count) return $this;
        $this->_increment = $count;
        $this->_calculate();
        return $this;
    }

    // Общая стоимость
    public function total(): array
    {
        return array(
            'count' => $this->_increment,
            'current' => $this->current * $this->_increment,
            'old' => $this->old * $this->_increment,
            'sale' => $this->sale * $this->_increment,
            'currency' => $this->currency,
        );
    }
}
