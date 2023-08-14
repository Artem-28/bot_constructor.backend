<?php

namespace App\Enums;

class EnumTariff
{
    const CODE_FREE = 'free';
    const CODE_BASE = 'base';
    const CODE_STANDARD = 'standard';
    const CODE_PREMIUM = 'premium';
    const CODE_SPECIAL = 'special';

    const PARAMS_TYPE_RESPONDENT = 'respondent';
    const PARAMS_TYPE_ADMIN = 'admin';
    const PARAMS_TYPE_STORAGE = 'storage';
    const PARAMS_TYPE_SCRIPT = 'script';
    const PARAMS_TYPE_QUESTION = 'question';

    const PERIOD_XS = 15;
    const PERIOD_S = 31;
    const PERIOD_L = 183;
    const PERIOD_XXL = 365;

    const STATUS_NOT_USED = 'not_used';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
}
