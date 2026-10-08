<?php


namespace Juzaweb\CMS\Support\Validators;

class DomainValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        return filter_var($value, FILTER_VALIDATE_DOMAIN);
    }
}
