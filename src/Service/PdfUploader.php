<?php
/**
 * Created by PhpStorm.
 * User: thocou
 * Date: 22/02/19
 * Time: 11:56
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PdfUploader
{
    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * PdfUploader constructor.
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $pdfFile
     * @return string
     */
    public function uploadPdf(UploadedFile $pdfFile)
    {
        $fileName = md5(uniqid()) . '.' . $pdfFile->guessExtension();

        try {
            $pdfFile->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            new HttpException(
                500,
                'Nous ne parvenons pas à traiter votre fichier, veuillez ré essayer plus tard ou uploader 
                un autre fichier au format .pdf'
            );
        }

        return $fileName;
    }

    /**
     * @return mixed
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}