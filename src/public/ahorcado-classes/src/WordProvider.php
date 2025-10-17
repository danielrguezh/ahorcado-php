<?php
declare(strict_types=1);

namespace App;

/**
 * Clase WordProvider
 *
 * Proporciona palabras desde un fichero (u otra fuente) en MAYUSCULAS y sin acentos.
 */
final class WordProvider
{
    /**
     * @var string Ruta al fichero de palabras (una por linea)
     */
    private string $filePath;

    /**
     * @param string $filePath Ruta del fichero de palabras
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Obtiene una palabra aleatoria
     *
     * @return string Palabra en MAYUSCULAS y sin acentos/diacriticos
     * @throws \RuntimeException Si el fichero no es legible o esta vacio
     */
    public function randomWord(): string
    {
        if (!is_readable($this->filePath)) {
            throw new \RuntimeException("No se puede leer {$this->filePath}");
        }
        $lines = array_filter(array_map('trim', file($this->filePath)));
        if (!$lines) {
            throw new \RuntimeException("El fichero de palabras esta vacio");
        }
        $word = $lines[array_rand($lines)];
        $word = $this->normalize($word);
        return mb_strtoupper($word, 'UTF-8');
    }

    /**
     * Normaliza texto eliminando acentos y caracteres no alfabeticos A-Z
     *
     * @param string $text Texto de entrada
     * @return string Texto normalizado
     */
    private function normalize(string $text): string
    {
        $withoutAccents = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $clean = preg_replace('/[^A-Za-z]/', '', $withoutAccents ?? $text);
        return $clean ?? $text;
    }
}
