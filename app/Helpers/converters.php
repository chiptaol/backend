<?php

function only(array|\Illuminate\Support\Collection $data, ...$keys)
{
    $collection = is_array($data) ? \Illuminate\Support\Collection::make($data) : $data;

    return $collection->map(function ($value) use ($keys, &$result) {
        return \Illuminate\Support\Arr::only($value, $keys);
    });
}
