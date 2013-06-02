<?php
    class Util {
        static public function urlencode_rfc3986($str) {
            return str_replace('%7E', '~', rawurlencode($str));
        }
    }
?>