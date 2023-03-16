<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\entities;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
enum OperatorEnum: string
{
    case EQUAL = "=";
    case GREATER = ">";
    case GREATER_OR_EQUAL = ">=";
    case LESS = "<";
    case LESS_OR_EQUAL = "<=";
}
