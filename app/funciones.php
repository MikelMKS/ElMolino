<?php

function flotFormatoM2($val) {
    $for = number_format($val, 2, '.', ',');
    if($for <= '0'){
        $for = null;
    }
    return $for;
}

function siguienteLetra($letra) {
    // Convertir la letra a mayúsculas para manejar tanto minúsculas como mayúsculas
    $letra = strtoupper($letra);
    
    // Si la letra es vacía o no es una letra del alfabeto, retornar un error
    if (!ctype_alpha($letra) || strlen($letra) !== 1) {
        return "Error: '$letra' no es una letra del alfabeto.";
    }

    // Si es la última letra del alfabeto, devolver 'AA'
    if ($letra === 'Z') {
        return 'AA';
    }

    // Si la letra es diferente de 'Z', incrementarla en uno
    return ++$letra;
}

function hacerColorMasClaro($colorOriginal, $cantidad = 100) {
    // Convertir el color original a componentes de R, G y B
    $red = hexdec(substr($colorOriginal, 0, 2));
    $green = hexdec(substr($colorOriginal, 2, 2));
    $blue = hexdec(substr($colorOriginal, 4, 2));

    // Aumentar la intensidad de los componentes R, G y B
    $nuevoRed = min($red + $cantidad, 255);
    $nuevoGreen = min($green + $cantidad, 255);
    $nuevoBlue = min($blue + $cantidad, 255);

    // Convertir los nuevos valores de R, G y B a formato hexadecimal
    $nuevoColor = sprintf("%02X%02X%02X", $nuevoRed, $nuevoGreen, $nuevoBlue);

    return $nuevoColor;
}

?>