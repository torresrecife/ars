<?php

declare(strict_types=1);

namespace App\Support;

class WriteResult
{
    const SUCCESS = 'success';
    const DUPLICATE = 'duplicate';
    const ERROR = 'error';
    const LINKED_USERS = 'linked_users';

    /** @var string */
    private $status;

    private function __construct($status)
    {
        $this->status = (string) $status;
    }

    public static function success()
    {
        return new self(self::SUCCESS);
    }

    public static function duplicate()
    {
        return new self(self::DUPLICATE);
    }

    public static function error()
    {
        return new self(self::ERROR);
    }

    public static function linkedUsers()
    {
        return new self(self::LINKED_USERS);
    }

    public function status()
    {
        return $this->status;
    }

    public function isSuccess()
    {
        return $this->status === self::SUCCESS;
    }

    public function isDuplicate()
    {
        return $this->status === self::DUPLICATE;
    }

    public function isError()
    {
        return $this->status === self::ERROR;
    }

    public function isLinkedUsers()
    {
        return $this->status === self::LINKED_USERS;
    }
}
