<?php

namespace p2pool;

class Utils {
    static function findBottomValue(callable $exists, $start = 1, $stride = 100){
        $index = $start;

        while(!$exists($index)){ //Find starting value
            $index += $stride;
        }

        $maxFound = $index;

        $min = $start;
        $max = $maxFound - 1;

        while ($min <= $max){
            $m = (int) floor(($min + $max) / 2);

            $r = !$exists($m);
            if($r){
                $min = $m + 1;
            }else{
                $max = $m - 1;
            }
        }

        return $min;
    }
    static function findTopValue(callable $exists, $start = 1, $stride = 100){
        $index = $start;

        while(!$exists($index)){ //Find starting value
            $index += $stride;
        }

        $minFound = $index;

        while($exists($index)){
            $minFound = $index;
            $index *= 2;
        }

        $maxNotFound = $index;

        $baseIndex = $minFound;

        $min = $minFound - $baseIndex;
        $max = $maxNotFound - $baseIndex - 1;

        while ($min <= $max){
            $m = (int) floor(($min + $max) / 2);

            $r = $exists($m + $baseIndex);
            if($r){
                $min = $m + 1;
            }else{
                $max = $m - 1;
            }
        }

        return $max + $baseIndex;
    }

    static function moneroRPC(string $method, array $params){
        $ch = curl_init(getenv("MONEROD_RPC_URL") . $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return @json_decode(curl_exec($ch));
    }

    static function encodeBinaryNumber(int $i): string {
        $v = gmp_strval($i, 62);

        return preg_match("#^[0-9]+$#", $v) > 0 ? ".$v" : $v;
    }

    static function decodeBinaryNumber(string $i): int {
        if(preg_match("#^[0-9]+$#", $i) > 0){
            return (int) $i;
        }
        return gmp_intval(gmp_init(str_replace(".", "", $i), 62));
    }
}