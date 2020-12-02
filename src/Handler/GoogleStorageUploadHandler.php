<?php namespace Mobileia\Expressive\Handler;

/**
 * Description of GoogleStorageUploadHandler
 *
 * @author matiascamiletti
 */
class GoogleStorageUploadHandler extends \Mobileia\Expressive\Request\MiaRequestHandler
{
    protected $bucketName = '';
    
    public function __construct($bucket)
    {
        $this->bucketName = $bucket;
    }
    
    /**
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface 
    {
        // Obtener servicio de Google
        /* @var $storage \Mobileia\Expressive\Google\Storage */
        $storage = $request->getAttribute('GoogleStorage');
        // Nombre del archivo
        $generatedName = '';
        // recorremos archivos enviadoss
        foreach($request->getUploadedFiles() as $file){
            // Generamos nombre
            $generatedName = 'file_'. time() . $this->getExtension($file->getClientMediaType());
            // Subimos archivo a Storage
            $storage->uploadFile($this->bucketName, $generatedName, $file->getStream()->detach());
        }
        if($generatedName == ''){
            // No se subio el archivo
            return new \Mobileia\Expressive\Diactoros\MiaJsonErrorResponse(-2, 'No se ha podido subir el archivo');
        }
        // Devolvemos respuesta
        return new \Mobileia\Expressive\Diactoros\MiaJsonResponse(array(
            'name' => $generatedName,
            'url' => 'https://storage.googleapis.com/' . $this->bucketName . '/' . $generatedName
        ));
    }
    
    /**
     * 
     * @param string $mimetype
     * @return string
     */
    protected function getExtension($mimetype)
    {
        if($mimetype == 'text/csv'){
            return '.csv';
        }else if($mimetype == 'image/png'){
            return '.png';
        }else if($mimetype == 'image/jpeg'){
            return '.jpg';
        }else if($mimetype == 'image/gif'){
            return '.gif';
        }else if($mimetype == 'image/webp'){
            return '.webp';
        }else if($mimetype == 'application/pdf'){
            return '.pdf';
        }else if($mimetype == 'video/x-matroska'||$mimetype == 'video/x-matroska;codecs=avc1,opus') {
            return '.mkv';
        }else if($mimetype == 'application/vnd.ms-excel') {
            return '.xls';
        }else if($mimetype == 'text/csv') {
            return '.csv';
        }else if($mimetype == 'application/msword') {
            return '.doc';
        }else if($mimetype == 'application/epub+zip') {
            return '.epub';
        }else if($mimetype == 'image/svg+xml') {
            return '.svg';
        }else if($mimetype == 'image/svg+xml') {
            return '.xml';
        }else if($mimetype == 'application/zip') {
            return '.zip';
        }else if($mimetype == 'application/vnd.visio') {
            return '.vsd';
        }
        
        return '.other';
    }
}
