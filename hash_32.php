<?php

// The 32-bits version
// PHP uses 64-bits for numeric types. We convert to 32-bits by doing `$n & 0xFFFFFFFF`

declare(strict_types=1);

function uint32($n)
{
    if ($n < 0) {
        return ((2 ** 32) + $n) & 0xFFFFFFFF;
    }
    return $n & 0xFFFFFFFF;
}


// original implementation: https://github.com/apache/kafka/blob/c2759df0676cef252596239baf8f1f361e76c49f/clients/src/main/java/org/apache/kafka/common/utils/Utils.java#L481
function hash3_32_int(string $key)
{
    $data = array_values(unpack('C*', $key));
    $length = count($data);
    $seed = 0x9747b28c;
    $m = 0x5bd1e995;
    $r = 24;

    $h = ($seed ^ $length) & 0xFFFFFFFF;
    $length4 = (int)($length / 4);

    for ($i = 0; $i < $length4; $i++) {
        $i4 = $i * 4;
        $k = (
            ($data[$i4 + 0] & 0xff) |
            (($data[$i4 + 1] & 0xff) << 8) |
            (($data[$i4 + 2] & 0xff) << 16) |
            (($data[$i4 + 3] & 0xff) << 24)
        );
        $k = ($k * $m) & 0xFFFFFFFF;
        $k ^= (uint32($k) >> $r);
        $k = ($k * $m) & 0xFFFFFFFF;
        $h = ($h * $m) & 0xFFFFFFFF;
        $h ^= $k;
    }

    switch ($length % 4) {
        case 3:
            $h ^= ($data[($length & ~3) + 2] & 0xff) << 16;
        case 2:
            $h ^= ($data[($length & ~3) + 1] & 0xff) << 8;
        case 1:
            $h ^= $data[$length & ~3] & 0xff;
            $h = ($h * $m) & 0xFFFFFFFF;
    }

    $h ^= (uint32($h) >> 13);
    $h = ($h * $m) & 0xFFFFFFFF;
    $h ^= (uint32($h) >> 15);

    return (int)$h;
}

function hash3_32(string $key): string
{
    return base_convert(sprintf("%u\n", hash3_32_int($key)), 10, 32);
}
