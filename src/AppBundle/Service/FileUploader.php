<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploader
{
    private $signatureFolder;
    private $logoFolder;
    private $receiptFolder;
    private $profilePhotoFolder;
    private $galleryImageFolder;

    /**
     * FileUploader constructor.
     *
     * @param string $signatureFolder
     * @param string $logoFolder
     * @param string $receiptFolder
     * @param string $galleryImageFolder
     * @param string $profilePhotoFolder
     */
    public function __construct(string $signatureFolder, string $logoFolder, string $receiptFolder, string $galleryImageFolder, string $profilePhotoFolder)
    {
        $this->signatureFolder = $signatureFolder;
        $this->logoFolder = $logoFolder;
        $this->receiptFolder = $receiptFolder;
        $this->galleryImageFolder = $galleryImageFolder;
        $this->profilePhotoFolder = $profilePhotoFolder;
    }

    /**
     * @param Request $request
     *
     * @return string absolute file path
     */
    public function uploadSignature(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->signatureFolder);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadLogo(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadReceipt(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        $mimeType = $file->getMimeType();
        $fileType = explode('/', $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->receiptFolder);
        } else {
            throw new BadRequestHttpException('Filtypen må være et bilde.');
        }
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function uploadGalleryImage(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        $fileType = explode('/', $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->galleryImageFolder);
        } else {
            throw new BadRequestHttpException('Filtypen må være et bilde.');
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadProfileImage(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        $mimeType = $file->getMimeType();
        $fileType = explode('/', $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->profilePhotoFolder);
        } else {
            throw new BadRequestHttpException('Filtypen må være et bilde.');
        }
    }

    /**
     * @param UploadedFile $file
     * @param string       $targetFolder
     *
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder)
    {
        $fileExt = $file->guessExtension();
        $fileName = $this->generateRandomFileNameWithExtension($fileExt);

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0775, true);
        }

        try {
            $file->move($targetFolder, $fileName);
        } catch (FileException $e) {
            $originalFileName = $file->getClientOriginalName();
            $relativePath = $this->getRelativePath($targetFolder, $fileName);

            throw new UploadException('Could not copy the file '.$originalFileName.' to '.$relativePath);
        }

        return $this->getAbsolutePath($targetFolder, $fileName);
    }

    public function deleteSignature(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->signatureFolder/$fileName");
    }

    public function deleteReceipt(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->receiptFolder/$fileName");
    }

    public function deleteGalleryImage(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->galleryImageFolder/$fileName");
    }

    public function deleteProfileImage(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->profilePhotoFolder/$fileName");
    }

    public function deleteFile(string $path)
    {
        if (file_exists($path)) {
            if (!unlink($path)) {
                throw new FileException('Could not remove file '.$path);
            }
        }
    }

    private function getFileFromRequest(Request $request)
    {
        $fileKey = current($request->files->keys());
        $file = $request->files->get($fileKey);

        if (is_array($file)) {
            return current($file);
        }

        return $file;
    }

    private function generateRandomFileNameWithExtension(string $fileExtension)
    {
        return uniqid().'.'.$fileExtension;
    }

    private function getRelativePath(string $targetDir, string $fileName)
    {
        return "$targetDir/$fileName";
    }

    private function getAbsolutePath(string $targetDir, string $fileName)
    {
        // Removes ../, ./, //
        $absoluteTargetDir = preg_replace('/\.+\/|\/\//i', '', $targetDir);

        if ($absoluteTargetDir[0] !== '/') {
            $absoluteTargetDir = '/'.$absoluteTargetDir;
        }

        return "$absoluteTargetDir/$fileName";
    }

    private function getFileNameFromPath(string $path)
    {
        return substr($path, strrpos($path, '/') + 1);
    }
}
