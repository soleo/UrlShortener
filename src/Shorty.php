<?php

namespace Soleo\UrlShortener;

class Shorty
{
    private $conn;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function getShortUrl($longURL)
    {
        // validate Long URL first and normalize it
        if (strlen($longURL) <= 3) {
            throw new \InvalidArgumentException("URL is malformated!");
        }

        $currentDomain = "localhost";
        // Lookup
        $slug = $this->conn->reverseLookup($longURL);

        $fullURL = 'http://'.$currentDomain.'/';
        if ($slug) {
            return $fullURL.$slug;
        } else {
            // add new records to DB
            $slug = $this->generateId();
            $this->conn->addNewRecord($longURL, $slug);
            return $fullURL.$slug;
        }
    }

    public function getLongUrl($slug, $update = false)
    {
        // filter out invalid string
        $slug = preg_replace('[^A_Za_z0-9]', '', $slug);
        $longURL = $this->conn->lookup($slug, $update);
        return $longURL;
    }

    private function generateId()
    {
        $url_num_id = $this->conn->getIncrementUid();
        $slug = $this->alphaID($url_num_id);
        return $slug;
    }

    private function alphaID($in, $to_num = false, $pad_up = 3, $passKey = "prephe.ro")
    {
        $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = [];
        $p = [];
        if ($passKey !== null) {
            // Although this function's purpose is to just make the
            // ID short - and not so much secure,
            // with this patch by Simon Franz (http://blog.snaky.org/)
            // you can optionally supply a password to make it harder
            // to calculate the corresponding numeric ID

            for ($n = 0; $n<strlen($index); $n++) {
                $i[] = substr($index, $n, 1);
            }

            $passhash = hash('sha256', $passKey);
            $passhash = (strlen($passhash) < strlen($index))
                ? hash('sha512', $passKey)
                : $passhash;

            for ($n=0; $n < strlen($index); $n++) {
                $p[] =  substr($passhash, $n, 1);
            }

            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        $base  = strlen($index);

        if ($to_num) {
            // Digital number  <<--  alphabet letter code
            $in  = strrev($in);
            $out = 0;
            $len = strlen($in) - 1;
            for ($t = 0; $t <= $len; $t++) {
                $bcpow = bcpow($base, $len - $t);
                $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
            }

            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up > 0) {
                    $out -= pow($base, $pad_up);
                }
            }
            $out = sprintf('%F', $out);
            $out = substr($out, 0, strpos($out, '.'));
        } else {
            // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up > 0) {
                    $in += pow($base, $pad_up);
                }
            }

            $out = "";
            for ($t = floor(log($in, $base)); $t >= 0; $t--) {
                $bcp = bcpow($base, $t);
                $a   = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in  = $in - ($a * $bcp);
            }
            $out = strrev($out); // reverse
        }

        return $out;
    }
}
