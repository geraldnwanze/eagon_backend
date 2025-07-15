<?php

namespace App\Enums;

enum RoleEnum: string
{
    case RESIDENT = 'resident';
    case ADMIN = 'admin';
    case GUEST = 'guest';
    case SUPER_ADMIN = 'super admin';
}
