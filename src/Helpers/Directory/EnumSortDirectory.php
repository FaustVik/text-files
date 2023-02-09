<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\Directory;

enum EnumSortDirectory: int
{
    case Asc = 0;
    case Desc = 1;
    case NoSort = 2;
}
