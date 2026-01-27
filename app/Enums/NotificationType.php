<?php

namespace App\Enums;

enum NotificationType: string
{
    case INFO = 'INFO';
    case SUCCESS = 'SUCCESS';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
    case ACTION = 'ACTION';
    case SYSTEM = 'SYSTEM';
    case PROMOTION = 'PROMOTION';
}
