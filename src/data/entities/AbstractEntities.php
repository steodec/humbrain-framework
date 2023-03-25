<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\data\entities;

use Exception;
use PDO;
use PDOStatement;
use stdClass;

/**
 * Class AbstractEntities
 * @package Humbrain\Framework\entities
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
abstract class AbstractEntities
{
    /**
     * Default table name
     * @var string $TABLE_NAME
     */
    public static string $TABLE_NAME = '';
    /**
     * Default primary key
     * @var int $id
     */
    public int $id;
}
