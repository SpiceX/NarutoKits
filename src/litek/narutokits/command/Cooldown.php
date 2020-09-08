<?php


namespace litek\narutokits\command;


use InvalidArgumentException;

final class Cooldown
{
    public static function parseDuration(string $duration): int
    {
        $time_units = ['y' => 'year', 'M' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'm' => 'minute'];
        $regex = '/^(\d+y)?(\d+M)?(\d+w)?(\d+d)?(\d+h)?(\d+m)?$/';
        $matches = [];
        $is_matching = preg_match($regex, $duration, $matches);
        if (!$is_matching) {
            throw new InvalidArgumentException("Invalid duration passed to CommandParser::parseDuration(). Must be of the form [ay][bM][cw][dd][eh][fm] with a, b, c, d, e, f integers");
        }

        $time = '';

        foreach ($matches as $index => $match) {
            if ($index === 0 || $match === '') {
                continue;
            }
            $n = substr($match, 0, -1);
            $unit = $time_units[substr($match, -1)];
            $time .= "$n $unit ";
        }
        $time = trim($time);
        return $time === '' ? time() : strtotime($time);
    }
}