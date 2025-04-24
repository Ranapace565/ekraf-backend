<?php

namespace App;

enum UserRole: string
{
    case USER = 'user';
    case ENTREPRENEUR = 'entrepreneur';
    case ADMIN = 'admin';
}
