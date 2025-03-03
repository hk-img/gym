<?php
namespace App\Enums;

enum Status: string 
{
    case PENDING = '1';
    case VERIFIED = '2';
    case REJECTED = '3';
}