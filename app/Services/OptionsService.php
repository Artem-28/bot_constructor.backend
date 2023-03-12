<?php

namespace App\Services;

use Illuminate\Support\Collection;

class OptionsService
{
    const ACCOUNT_TYPE_OPTIONS = 'account_type';

    private AccountTypeService $accountTypeService;

    public function __construct
    (
        AccountTypeService $accountTypeService,
    )
    {
        $this->accountTypeService = $accountTypeService;
    }

    public function getOptions(string $type): ?Collection
    {
        return match ($type) {
            self::ACCOUNT_TYPE_OPTIONS => $this->accountType(),
            default => null,
        };
    }

    private function accountType(): Collection
    {
        return $this->accountTypeService->getList();
    }
}
