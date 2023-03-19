<?php

namespace App\Helpers;

class ApiCodes
{
    const SUCCESS = 200;
    const ACCEPTED = 202;
    const BAD_REQUEST = 400;
    const RESOURCE_NOT_FOUND = 404;
    const FORBIDDEN = 403;
    const RESOURCE_CAN_NOT_BE_MODIFIED = 5;
    const UNAUTHENTICATED = 401;
    const METHOD_NOT_ALLOWED = 405;
    const INTERNAL_SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;

    public static function getSuccessMessage(): string
    {
        return 'The action was completed successfully!';
    }

    public static function getResourceNotFoundMessage(): string
    {
        return 'No data found!';
    }

    public static function getGeneralErrorMessage(): string
    {
        return 'Internal error! Please try again!';
    }

    public static function getModelSoftDeletedMessage(): string
    {
        return 'There is a deleted entity with this name!';
    }

    public static function getForbiddenErrorMessage(): string
    {
        return 'You are not allowed to perform this action!';
    }

    public static function getWrongCredentialsMessage(): string
    {
        return 'The credentials entered are incorrect!';
    }

    public static function getRecordExistsErrorMessage(): string
    {
        return 'This record exists in the database!';
    }
}
