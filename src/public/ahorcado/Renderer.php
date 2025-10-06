<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
class Renderer {
    /**
     * Funcion que imprime el cuerpo del ahorcado segun los intentos restantes
     */
    public function ascii(int $attemptsLeft): string {
        $stages = [
            0 => "
  +---+
  |   |
  O   |
 /|\\  |
 / \\  |
      |
=========",
            1 => "
  +---+
  |   |
  O   |
 /|\\  |
 /    |
      |
=========",
            2 => "
  +---+
  |   |
  O   |
 /|\\  |
      |
      |
=========",
            3 => "
  +---+
  |   |
  O   |
 /|   |
      |
      |
=========",
            4 => "
  +---+
  |   |
  O   |
  |   |
      |
      |
=========",
            5 => "
  +---+
  |   |
  O   |
      |
      |
      |
=========",
            6 => "
  +---+
  |   |
      |
      |
      |
      |
========="
        ];
        return "<pre>" . $stages[$attemptsLeft] . "</pre>";
    }
}
