<?php

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class UploadImage
{


    protected $path;

    protected array $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }
    
    /**
     * upload
     * 
     * ca uplode une image, 
     *
     * @param  mixed $file
     * @param  mixed $oldFile
     * @param  mixed $filename
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null, ?string $filename = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $targetPath = $this->addCopySuffix(
                $this->path .
                    DIRECTORY_SEPARATOR .
                   ($filename ?: $file->getClientFilename())
            );
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }
            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);
            return pathinfo($targetPath)['basename'];
        }
        return null;
    }

    
    /**
     * delete
     * 
     * ca efface image existant dans le dossier 
     *
     * @param  mixed $oldFile
     * @return void
     */
    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }
    
    /**
     * generateFormats
     * 
     * ca genere un format thumb (small) pour l'image 
     *
     * @param  mixed $targetPath
     * @return void
     */
    private function generateFormats($targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $destination = $this->getPathWithSuffix($targetPath, 'small');
            $manager = new ImageManager(['driver' => 'gd']);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
        
    }
    
    /**
     * addCopySuffix
     * 
     * on genere meme image avec suffix copy si image existe deja ! au lieu l'effacer
     *
     * @param  mixed $targetPath
     * @return string
     */
    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }
    
    /**
     * getPathWithSuffix 
     * 
     * ca genere un suffix pour les images
     *
     * @param  mixed $path
     * @param  mixed $suffix
     * @return string
     */
    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }
}
