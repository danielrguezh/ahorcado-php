<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
class WordProvider {
    private string $filePath;

    /**
     * Constructor por defecto
     */
    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }

    /**
     * Funcion que escoge una palabra aleatoria de la seleccion
     */
    public function randomWord(): string {
        $words = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $word = $words[array_rand($words)];
        $word = strtoupper($this->normalize($word));
        return $word;
    }

    /**
     * Funcion normaliza las palabras adaptandolas al formato
     */
    private function normalize(string $word): string {
        $word = iconv('UTF-8', 'ASCII//TRANSLIT', $word);
        return preg_replace('/[^A-Z]/', '', strtoupper($word));
    }
}
