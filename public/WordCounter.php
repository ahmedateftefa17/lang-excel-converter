<?php

/**
 *
 */
class WordCounter
{
    public static function countLineWords($line)
    {
        $count = 0;
        $words = explode(' ', $line);
        if (is_array($words) && count($words)) {
            foreach ($words as $word) {
                if (strlen(strip_tags($word)) > 3) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
