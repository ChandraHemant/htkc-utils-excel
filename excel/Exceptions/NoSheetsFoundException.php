<?php

namespace ChandraHemant\HtkcUtilsExcel\Exceptions;

use LogicException;

class NoSheetsFoundException extends LogicException implements LaravelExcelException
{
}
