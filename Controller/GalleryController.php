<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Maith\Common\AdminBundle\Model\Encrypt;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GalleryController extends Controller
{
    public function indexAction()
    {
        $mediaGallery = $this->get('media_gallery_manager');
        return $this->render('MaithCommonAdminBundle:Gallery:index.html.twig', array(
                'galleries' => $mediaGallery->getAllFilesWithData(),
            ));    
        
    }
    
    public function uploadFormAction($name)
    {
      $token = $this->container->getParameter('maith_common_admin.upload_token');
      $encrypt = new Encrypt($token);
      
      return $this->render('MaithCommonAdminBundle:Gallery:upload.html.twig', array('gallery' => $name, 'dataSession' => urldecode($encrypt->encrypt(session_id()))));
    }
    
    private function retrieveExtensionAndMiMeType($filename)
    {
        /* Figure out the MIME type (if not specified) */
        $known_mime_types = array(
            "pdf" => "application/pdf",
            "txt" => "text/plain",
            "html" => "text/html",
            "htm" => "text/html",
            "exe" => "application/octet-stream",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg" => "image/jpg",
            "jpg" => "image/jpg",
            "php" => "text/plain"
        );

        
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));
        if (array_key_exists($file_extension, $known_mime_types)) {
            $mime_type = $known_mime_types[$file_extension];
        } else {
            $mime_type = "application/force-download";
        }
        
        return array('extension' => $file_extension, 'mime' => $mime_type);
    }
    
    public function doUploadAction()
    {
      $logger = $this->container->get('logger');
      $gallery = $this->getRequest()->get('gallery');
      $mediaGallery = $this->get('media_gallery_manager');
      $logger->debug(sprintf('The gallery name is: %s', $gallery));
      $targetDir = $mediaGallery->getFilesFullPath().DIRECTORY_SEPARATOR.$gallery;
      
      if (!file_exists($targetDir))
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'gallery' => $gallery)));
      }
        
      $fileUploaded = $this->container->get('request')->files->get('file');
      $mimeAndName = null;
      if(function_exists('finfo_open'))
      {
          $name = uniqid() . '.' . $fileUploaded->guessExtension();
      }
      else
      {
          $mimeAndName = $this->retrieveExtensionAndMiMeType($fileUploaded->getClientOriginalName());
          $name = uniqid(). '.'.$mimeAndName['extension'];
      }
      $logger->debug(sprintf('Moving the file with name: %s to the following dir: %s', $name, $targetDir));
      $movedFile = $fileUploaded->move($targetDir, $name);
      if ($movedFile) 
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'result' => null, 'id' => $gallery)));
      }
      else
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'id' => $gallery)));
      }
      die;
    }
    
    public function refreshGalleryAction(Request $request){
      $gallery = $request->get('gallery');
      $mediaGallery = $this->get('media_gallery_manager');
      $files = $mediaGallery->getFiles($gallery);
      $response = new JsonResponse();
      $response->setData(array(
          'status' => 'OK',
          'options' => array('html' => $this->renderView('MaithCommonAdminBundle:Gallery:gallery.html.twig', array('name' => $gallery, 'files' => $files)))
      ));
      return $response;
    }

    public function downloadFileAction($gallery, $file)
    {
      $mediaGallery = $this->get('media_gallery_manager');
      $fileObject = $mediaGallery->getFile($gallery, $file);
      $content = $fileObject-> getContents();
      $response = new Response();
      $fileMetadata = $this->retrieveExtensionAndMiMeType($fileObject->getFilename());
      $response->headers->set('Content-Type', $fileMetadata['mime']);
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileObject->getFilename());
      $response->setContent($content);
      return $response;
    }
    
    public function removeFile($gallery, $file)
    {
      $mediaGallery = $this->get('media_gallery_manager');
      $result = $mediaGallery->removeFile($gallery, $file);
      $response = new JsonResponse();
      $response->setData(array(
          'result' => $result          
      ));
      return $response;
    }
}
