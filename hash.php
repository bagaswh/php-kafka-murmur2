<?php

// The 64-bits version

declare(strict_types=1);

// original implementation: https://github.com/apache/kafka/blob/c2759df0676cef252596239baf8f1f361e76c49f/clients/src/main/java/org/apache/kafka/common/utils/Utils.java#L481
function hash3_64_int(string $key): int
{
    $data  = array_values(unpack('C*', $key));
    $length = count($data);
    $seed = 0x9747b28c;
    // 'm' and 'r' are mixing constants generated offline.
    // They're not really 'magic', they just happen to work well.
    $m = 0x5bd1e995;
    $r = 24;

    // Initialize the hash to a random value
    $h = $seed ^ $length;
    $length4 = floor($length / 4);

    for ($i = 0; $i < $length4; $i++) {
        $i4 = $i * 4;
        $k = ($data[$i4 + 0] & 0xff) + (($data[$i4 + 1] & 0xff) << 8) + (($data[$i4 + 2] & 0xff) << 16) + (($data[$i4 + 3] & 0xff) << 24);
        $k *= $m;
        $k ^= $k >> $r;
        $k *= $m;
        $h *= $m;
        $h ^= $k;
    }

    // Handle the last few bytes of the input array
    switch ($length % 4) {
        case 3:
            $h ^= ($data[($length & ~3) + 2] & 0xff) << 16;
        case 2:
            $h ^= ($data[($length & ~3) + 1] & 0xff) << 8;
        case 1:
            $h ^= $data[$length & ~3] & 0xff;
            $h *= $m;
    }

    $h ^= $h >> 13;
    $h *= $m;
    $h ^= $h >> 15;

    return $h;
}

function hash3_64(string $key): string
{
    return base_convert(sprintf("%u\n", hash3_64($key)), 10, 32);
}
