<?php

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp' . number_format((float) $angka, 0, ',', '.');
    }
}

