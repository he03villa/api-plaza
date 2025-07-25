<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Exception;

class ImageService
{
    /**
     * Guardar imagen desde base64
     *
     * @param string $ruta Ruta donde guardar la imagen (relativa al disco)
     * @param string $base64 Imagen en formato base64
     * @param string|null $nombreImagen Nombre personalizado para la imagen (sin extensión)
     * @param string $disco Disco de almacenamiento (por defecto 'public')
     * @return string Ruta completa del archivo guardado
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function guardarImagen($ruta, $base64, $nombreImagen = null, $disco = 'public')
    {
        // Extraer la información del header del base64
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = $matches[1];
            // Normalizar extensiones
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
            // Remover el header completo del base64
            $base64 = substr($base64, strpos($base64, ',') + 1);
        } else {
            // Si no hay header, intentar obtener la extensión con método auxiliar
            $extension = self::getExtensionFromBase64($base64);
            // Limpiar posibles headers residuales
            $base64 = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64);
        }
        
        // Limpiar cualquier caracter no válido del base64
        $base64 = str_replace([' ', '\n', '\r', '\t'], '', $base64);
        
        // Validar que el base64 es válido
        if (!preg_match('/^[a-zA-Z0-9\/+]*={0,2}$/', $base64)) {
            throw new InvalidArgumentException('Base64 inválido');
        }
        
        // Decodificar la imagen
        $imagen = base64_decode($base64, true);
        
        // Verificar que la decodificación fue exitosa
        if ($imagen === false) {
            throw new InvalidArgumentException('Error al decodificar el base64');
        }
        
        // Generar nombre de archivo
        if ($nombreImagen == null) {
            $nombreImagen = uniqid() . '.' . $extension;
        } else {
            $nombreImagen .= '.' . $extension;
        }
        
        $rutaCompleta = $ruta . '/' . $nombreImagen;
        
        // Guardar la imagen
        Storage::disk($disco)->put($rutaCompleta, $imagen);
        
        // Verificar que el archivo se guardó correctamente
        if (!Storage::disk($disco)->exists($rutaCompleta)) {
            throw new Exception('Error al guardar la imagen');
        }
        
        return $rutaCompleta;
    }

    /**
     * Función auxiliar para obtener extensión desde base64
     *
     * @param string $base64
     * @return string
     */
    private static function getExtensionFromBase64($base64)
    {
        // Si tiene header, extraer de ahí
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = $matches[1];
            return $extension === 'jpeg' ? 'jpg' : $extension;
        }
        
        // Si no tiene header, analizar los primeros bytes
        $imageData = base64_decode(substr($base64, 0, 50), true);
        if ($imageData === false) {
            return 'jpg'; // Default fallback
        }
        
        // Detectar tipo de imagen por magic bytes
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            case 'image/webp':
                return 'webp';
            default:
                return 'jpg'; // Default fallback
        }
    }

    /**
     * Eliminar imagen del almacenamiento
     *
     * @param string $rutaImagen Ruta de la imagen a eliminar
     * @param string $disco Disco de almacenamiento
     * @return bool
     */
    public static function eliminarImagen($rutaImagen, $disco = 'public')
    {
        if (Storage::disk($disco)->exists($rutaImagen)) {
            return Storage::disk($disco)->delete($rutaImagen);
        }
        
        return false;
    }

    /**
     * Verificar si una imagen existe
     *
     * @param string $rutaImagen Ruta de la imagen
     * @param string $disco Disco de almacenamiento
     * @return bool
     */
    public static function imagenExiste($rutaImagen, $disco = 'public')
    {
        return Storage::disk($disco)->exists($rutaImagen);
    }

    /**
     * Obtener URL pública de la imagen
     *
     * @param string $rutaImagen Ruta de la imagen
     * @param string $disco Disco de almacenamiento
     * @return string|null
     */
    public static function obtenerUrlImagen($rutaImagen, $disco = 'public')
    {
        if (self::imagenExiste($rutaImagen, $disco)) {
            return Storage::disk($disco)->url($rutaImagen);
        }
        
        return null;
    }

    /**
     * Validar formato de imagen base64
     *
     * @param string $base64
     * @return bool
     */
    public static function validarBase64($base64)
    {
        // Verificar si tiene header válido de imagen
        if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
            return true;
        }
        
        // Si no tiene header, verificar que sea base64 válido
        $base64Clean = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64);
        $base64Clean = str_replace([' ', '\n', '\r', '\t'], '', $base64Clean);
        
        return preg_match('/^[a-zA-Z0-9\/+]*={0,2}$/', $base64Clean) && base64_decode($base64Clean, true) !== false;
    }

    /**
     * Obtener información de la imagen desde base64
     *
     * @param string $base64
     * @return array
     */
    public static function obtenerInfoImagen($base64)
    {
        $info = [
            'extension' => null,
            'mime_type' => null,
            'size' => null,
            'valid' => false
        ];

        try {
            // Extraer extensión
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $info['extension'] = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                $info['mime_type'] = 'image/' . $matches[1];
                $base64Clean = substr($base64, strpos($base64, ',') + 1);
            } else {
                $info['extension'] = self::getExtensionFromBase64($base64);
                $base64Clean = $base64;
            }

            // Decodificar y obtener tamaño
            $imageData = base64_decode($base64Clean, true);
            if ($imageData !== false) {
                $info['size'] = strlen($imageData);
                $info['valid'] = true;
            }

        } catch (Exception $e) {
            $info['valid'] = false;
        }

        return $info;
    }
}