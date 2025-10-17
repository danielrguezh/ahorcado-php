<?php
declare(strict_types=1);

namespace App;

/**
 * Clase Renderer
 *
 * Genera el dibujo ASCII del ahorcado segun los intentos restantes.
 */
final class Renderer
{
    /**
     * Devuelve un bloque <pre> con el dibujo ASCII acorde a los intentos restantes
     *
     * @param int $attemptsLeft Intentos restantes (0..6)
     * @return string HTML con bloque <pre> del dibujo
     */
    public function ascii(int $attemptsLeft): string
    {
        $stages = [
            0 => [" +---+"," |   |"," O   |","/|\\  |","/ \\  |","     |","====="],
            1 => [" +---+"," |   |"," O   |","/|\\  |","/    |","     |","====="],
            2 => [" +---+"," |   |"," O   |","/|\\  |","     |","     |","====="],
            3 => [" +---+"," |   |"," O   |","/|   |","     |","     |","====="],
            4 => [" +---+"," |   |"," O   |"," |   |","     |","     |","====="],
            5 => [" +---+"," |   |"," O   |","     |","     |","     |","====="],
            6 => [" +---+"," |   |","     |","     |","     |","     |","====="],
        ];
        $idx = max(0, min(6, $attemptsLeft));
        $lines = $stages[$idx];
        return '<pre>'.implode("\n", $lines).'</pre>';
    }
}
