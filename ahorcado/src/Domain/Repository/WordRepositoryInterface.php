<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
declare(strict_types=1);

namespace App\Domain\Repository;

interface WordRepositoryInterface
{
    public function randomWord(): string;
}