<?php

    function count_array_values($my_array, $match) {
        $count = 0;
        foreach ($my_array as $key => $value) {
            if ($value === $match) {
                $count++;
            }
        }
        return $count;
    }
