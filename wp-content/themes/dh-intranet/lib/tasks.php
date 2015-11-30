<?php

function get_tasks()
{
    $tasks = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = get_field('task_'.$i, 'option');
        if ($val) {
            $tasks[] = $val;
        }
    }

    return $tasks;
}
