<?php

function make_random_hash(string $salt = '')
{
    try {
        $string = bin2hex(random_bytes(32)) . $salt;
    } catch (Exception $e) {
        $string = mt_rand() . $salt;
    }
    return sha1($string);
}

function make_random_referral_code()
{
    return strtoupper(substr(make_random_hash(),4,5));
}

function make_random_hash_with_length($length)
{
    return strtoupper(substr(make_random_hash(),0,$length));
}

function make_random_numeric_token($digits = 6)
{
    $max = pow(10, $digits) - 1;
    $token = mt_rand(0, $max);
    return str_pad($token, $digits, '0', STR_PAD_LEFT);
}

function fmod_round($x, $y) {
    $i = round($x / $y);
    return $x - $i * $y;
}

function numberToNth(int $index) {
    if ($index >= 10 && $index <= 20) {
        return $index . 'th';
    }
    $lastDigit = $index % 10;
    switch ($lastDigit) {
        case 1:
            $suffix = 'st';
            break;
        case 2:
            $suffix = 'nd';
            break;
        case 3:
            $suffix = 'rd';
            break;
        default:
            $suffix = 'th';
            break;
    }
    return $index . $suffix;
}

function getMonthName($index) {
    $months = [
        1 => 'فر.ردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];
    if ($index > 12 || $index < 1) {
        return '';
    }
    return $months[$index];
}

function float_number_format($value) {
    if (intval(abs($value)) != abs($value)) {
        $floatingPart = preg_replace('/^\d*\./', '', abs($value) . '');
    } else {
        $floatingPart = '';
    }
    if ($floatingPart) {
        return number_format($value) . '.' . $floatingPart;
    }
    return number_format($value);
}