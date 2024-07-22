<?php
// Helper function to perform right rotate
function rightRotate($n, $d) {
    return ($n >> $d) | ($n << (32 - $d));
}

// Pre-processing (Padding)
function preprocess($message) {
    $length = strlen($message) * 8;
    $message .= chr(0x80);
    while ((strlen($message) % 64) !== 56) {
        $message .= chr(0x00);
    }
    $message .= pack('N2', 0, $length);
    return $message;
}

// SHA-256 Constants
$K = [
    0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
    0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
    0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
    0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
    0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
    0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
    0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
];

// SHA-256 Initial Hash Values
$H = [
    0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
    0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
];

// Main SHA-256 Algorithm
function sha256($message) {
    global $K, $H;
    $message = preprocess($message);

    for ($i = 0; $i < strlen($message); $i += 64) {
        $chunk = substr($message, $i, 64);
        $M = [];
        for ($j = 0; $j < 64; $j += 4) {
            $M[] = unpack('N', substr($chunk, $j, 4))[1];
        }

        for ($j = 16; $j < 64; $j++) {
            $s0 = rightRotate($M[$j - 15], 7) ^ rightRotate($M[$j - 15], 18) ^ ($M[$j - 15] >> 3);
            $s1 = rightRotate($M[$j - 2], 17) ^ rightRotate($M[$j - 2], 19) ^ ($M[$j - 2] >> 10);
            $M[$j] = ($M[$j - 16] + $s0 + $M[$j - 7] + $s1) & 0xffffffff;
        }

        list($a, $b, $c, $d, $e, $f, $g, $h) = $H;

        for ($j = 0; $j < 64; $j++) {
            $S1 = rightRotate($e, 6) ^ rightRotate($e, 11) ^ rightRotate($e, 25);
            $ch = ($e & $f) ^ ((~$e) & $g);
            $temp1 = ($h + $S1 + $ch + $K[$j] + $M[$j]) & 0xffffffff;
            $S0 = rightRotate($a, 2) ^ rightRotate($a, 13) ^ rightRotate($a, 22);
            $maj = ($a & $b) ^ ($a & $c) ^ ($b & $c);
            $temp2 = ($S0 + $maj) & 0xffffffff;

            $h = $g;
            $g = $f;
            $f = $e;
            $e = ($d + $temp1) & 0xffffffff;
            $d = $c;
            $c = $b;
            $b = $a;
            $a = ($temp1 + $temp2) & 0xffffffff;
        }

        $H[0] = ($H[0] + $a) & 0xffffffff;
        $H[1] = ($H[1] + $b) & 0xffffffff;
        $H[2] = ($H[2] + $c) & 0xffffffff;
        $H[3] = ($H[3] + $d) & 0xffffffff;
        $H[4] = ($H[4] + $e) & 0xffffffff;
        $H[5] = ($H[5] + $f) & 0xffffffff;
        $H[6] = ($H[6] + $g) & 0xffffffff;
        $H[7] = ($H[7] + $h) & 0xffffffff;
    }

    $hash = '';
    foreach ($H as $h) {
        $hash .= sprintf('%08x', $h);
    }

    return $hash;
}

?>
