<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EmailInUse extends Constraint {
    public $message = 'Email is already in use.';
}