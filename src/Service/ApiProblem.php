<?php


namespace App\Service;



class ApiProblem
{
    const VALIDATION_ERROR = 'validation_error';
    const ENTITY_NOT_EXISTS = 'entity_not_exists';
    const OWNERSHIP_VIOLATION = 'ownership_violation';

    public static $title = [
        self::VALIDATION_ERROR => 'Missing or incorrect query parameters provided.',
        self::ENTITY_NOT_EXISTS => 'Entity is not found.',
        self::OWNERSHIP_VIOLATION => 'Current user don\'t own the entity.',
    ];

    private $message;

    public function __construct(string $type)
    {
        if (isset(self::$title[$type])) {
            $this->message = self::$title[$type];
        } else {
            throw new \InvalidArgumentException('No title for type ' . $type);
        }
    }

    public function toArray()
    {
        return ['error' => $this->message];
    }
}