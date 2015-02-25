<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Maith\Common\AdminBundle\Model\Encrypt;
use Maith\Common\AdminBundle\Model\GalleryFile;
use Maith\Common\AdminBundle\Entity\mFile;
use Maith\Common\AdminBundle\Entity\mAlbum;
use Maith\Common\AdminBundle\Form\mFileType;
use Maith\Common\AdminBundle\Form\mFileVideoType;
use Maith\Common\AdminBundle\Model\OEmbededHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;


class AlbumsController extends Controller
{
  
    private $VIMEOURl = 'https://vimeo.com/api/oembed.json';
    private $YOUTUBEURl = 'https://vimeo.com/api/oembed.json';
    
    public function indexAction()
    {
        return $this->render('MaithCommonAdminBundle:Default:index.html.twig');
    }
    
    public function retrieveAlbumsDataAction()
    {
        return $this->render('MaithCommonAdminBundle:Albums:showAlbums.html.twig');
        exit(0);
    }
    
    public function albumsDataAction($id, $objectclass, $listmode = false)
    {
      $em = $this->getDoctrine()->getManager();
      $query = $em->createQuery("select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :id and a.object_class = :object_class")->setParameters(array('id' => $id, 'object_class' => $objectclass));
      $albums = $query->getResult();
      if(count($albums) == 0)
      {
        $obj = new $objectclass;
        if(method_exists($obj, 'retrieveAlbums'))
        {
          $checkForOnlineVideos = false;
          if(method_exists($obj, 'checkAlbumForOnlineVideo'))
          {
              $checkForOnlineVideos = true;
          }
          // var_dump('estoy aca');
          foreach($obj->retrieveAlbums() as $name)
          {
            $album = new mAlbum();
            $album->setObjectId($id);
            $album->setObjectClass($objectclass);
            $album->setName($name);
            if($checkForOnlineVideos)
            {
              $album->setHasonlinevideo($obj->checkAlbumForOnlineVideo($name));
            }
            $em->persist($album);       
          }
          $em->flush();
          $query = $em->createQuery("select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :id and a.object_class = :object_class")->setParameters(array('id' => $id, 'object_class' => $objectclass));
          $albums = $query->getResult();
        }
      }
      //var_dump($albums);
      //$imageManager = $this->get('maith_common_image.image.mimage');
      //$imageManager->doResize();
      if(!$listmode){
        return $this->render('MaithCommonAdminBundle:Albums:showAlbums.html.twig', array('albums' => $albums));
      }
      return $this->render('MaithCommonAdminBundle:Albums:showAlbumsList.html.twig', array('albums' => $albums));
    }
    
    
    public function refreshAlbumAction()
    {
      $listmode = $this->getRequest()->get("list", false);
      $albumId = $this->getRequest()->get("id");
      $em = $this->getDoctrine()->getManager();
      $album = $em->getRepository("MaithCommonAdminBundle:mAlbum")->find($albumId);
      
      $response = new JsonResponse();
      if(!$listmode)
      {
        $response->setData(array('status'=> 'OK', 'options' => array('html' => $this->renderView('MaithCommonAdminBundle:Albums:showAlbumFiles.html.twig', array('files' => $album->getFiles())) )));
      }
      else
      {
        $response->setData(array('status'=> 'OK', 'options' => array('html' => $this->renderView('MaithCommonAdminBundle:Albums:showAlbumFilesList.html.twig', array('files' => $album->getFiles())) )));
      }
      
      return $response;
    }
    
    public function sortAlbumAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $album = $em->getRepository("MaithCommonAdminBundle:mAlbum")->find($id);
      return $this->render('MaithCommonAdminBundle:Albums:fileSortable.html.twig', array('album' => $album));
    }
    
    public function doSortAlbumAction()
    {
      $albumId = $this->getRequest()->get("album_id");
      $items = $this->getRequest()->get("listItem");
      $em = $this->getDoctrine()->getManager();
      $album = $em->getRepository("MaithCommonAdminBundle:mAlbum")->find($albumId);
      $files = $album->getFiles();
      $counter = 0;
      while($counter < count($items))
      {
        $finish = false;
        $index = 0;
        $number = (int) $items[$counter];
        while(!$finish && $index < $files->count())
        {
          $file = $files->get($index);
          if(!$file)
          {
            $finish = true;
          }
          else
          {
            if($number == $file->getId())
            {
              $file->setOrden($counter);
              $em->persist($file);
              $em->flush();
              $finish = true;
            }
          }
          $index++;
        }
        $counter++;
      }
      
      $response = new JsonResponse();
      $response->setData(array('output' => true));
      return $response;
    }
    
    public function removeFileAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $file = $em->getRepository("MaithCommonAdminBundle:mFile")->find($id);
      $response = new JsonResponse();
      $status = "OK";
      if (!$file) 
      {
        $status = "ERROR";
      }
      else
      {
        $file->removeAllFiles($this->get('kernel')->getCacheDir());
        $em->remove($file);
        $em->flush();
      }
      $response->setData(array('status' => $status));
      return $response;
    }
    
    public function uploadFormAction($id)
    {
      $token = $this->container->getParameter('maith_common_admin.upload_token');
      $encrypt = new Encrypt($token);
      
      return $this->render('MaithCommonAdminBundle:Albums:upload.html.twig', array(
          'albumId' => $id, 
          'dataSession' => urldecode($encrypt->encrypt(session_id())),
          //'videoform' => $form->createView(),
        )
      );
    }
    
    public function onlineVideoFormAction(Request $request, $id)
    {
      $myFile = new mFile();
      $form = $this->createForm(new mFileVideoType(), $myFile, array(
            //'action' => $this->generateUrl('admin_multimedia_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
        ));
      
      if ($request->isMethod('POST')) 
      {
          $form->bind($request);
          
          
          $response = new JsonResponse();
          
          $dataResponse = array(
              'result' => false,
              'message' => 'Formulario incorrecto',
              'albumId' => $id,
          );
          $form->isValid();
          $onlineVideo = $form->getData()->getOnlinevideo();
          
          $wwwData = OEmbededHandler::retrieveData($onlineVideo);

          if ($wwwData['videoType'] != NULL) 
          {
            
            if(count($wwwData['data']) > 0 ) 
            {
              $em = $this->getDoctrine()->getManager();
              
              $myFile->setAlbum($em->getRepository("MaithCommonAdminBundle:mAlbum")->find($id));
              $myFile->setName($wwwData['data']['title']);
              $myFile->setPath('.');
              $myFile->setType($wwwData['videoType']);
              $myFile->setOnlinevideo($onlineVideo);
              $myFile->setSfPath('.');
              $em->persist($myFile);
              $em->flush();
              $dataResponse['result'] = true;
              $message = 'Video guardado con exito';
            }
            
            //$dataResponse['message'] = $message;
          }
          else
          {
            $view = $this->renderView('MaithCommonAdminBundle:Albums:videoForm.html.twig', array(
              'albumId' => $id, 
              'videoform' => $form->createView(),
            ));
            $message = 'El video no es de youtube o vimeo';
            //$response->setData(array('status'=> 'OK', 'options' => array('html' => $view )));
            $dataResponse['html'] = $view;
          }
          $dataResponse['message'] = $message;
          $response->setData($dataResponse);
          return $response;
      }
      return $this->render('MaithCommonAdminBundle:Albums:videoForm.html.twig', array(
          'albumId' => $id, 
          'videoform' => $form->createView(),
        )
      );
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
    
    public function doFormUploadAction()
    {
      
      $albumId = $this->getRequest()->get('albumId');
      $fileName = $this->getRequest()->get('name', 0);
      $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
      $sf_targetDir = "upload". DIRECTORY_SEPARATOR."album-".$albumId;
      $targetDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$sf_targetDir;
      if (!file_exists($targetDir))
        @mkdir($targetDir);
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
      
      $movedFile = $fileUploaded->move($targetDir, $name);
      if ($movedFile) 
      {
        $em = $this->getDoctrine()->getManager();
        $myFile = new mFile();
        $myFile->setAlbum($em->getRepository("MaithCommonAdminBundle:mAlbum")->find($albumId));
        $myFile->setName($name);
        $myFile->setPath($targetDir);
        if($mimeAndName === null)
        {
            $myFile->setType($movedFile->getMimeType());
        }
        else
        {
            $myFile->setType($mimeAndName['mime']);
        }
        $myFile->setSfPath($sf_targetDir);
        $em->persist($myFile);
        $em->flush();
        return new Response(json_encode(array("jsonrpc" => '2.0', 'result' => null, 'id' => $albumId)));
      }
      else
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'id' => $albumId)));
      }
      die;
    }
    
    
    public function showFileEditionAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $file = $em->getRepository("MaithCommonAdminBundle:mFile")->find($id);
      
      $form   = $this->createForm(new mFileType(), $file);
      return $this->render('MaithCommonAdminBundle:Albums:fileForm.html.twig', array('file' => $file, 'form'   => $form->createView()));
    }
    
    public function editFileEditionAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('MaithCommonAdminBundle:mFile')->find($id);

      if (!$entity) {
          throw $this->createNotFoundException('Unable to find mFile entity.');
      }
       $form   = $this->createForm(new mFileType(), $entity);
       $form->bind($request);
       $valid = false;
       if($form->isValid())
       {
         $valid = true;
         $em->persist($entity);
         $em->flush();
       }
       return new Response(json_encode(array("result" => $valid, 'errors' => $form->getErrorsAsString())));
    }
    
    public function downloadOriginalFileAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $file = $em->getRepository("MaithCommonAdminBundle:mFile")->find($id);
      $content = file_get_contents($file->getFullPath());
      $response = new Response();

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
          "jpeg" => "image/jpeg",
          "jpg" => "image/jpg",
          "php" => "text/plain"
      );
      $mime_type = $file->getType();
      if(!in_array($file->getType(), $known_mime_types))
      {
        $file_extension = strtolower(substr(strrchr($file->getName(), "."), 1));
        if (array_key_exists($file_extension, $known_mime_types)) {
          $mime_type = $known_mime_types[$file_extension];
        }
      }
      
      $response->headers->set('Content-Type', $mime_type);
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$file->getName());

      $response->setContent($content);
      return $response;
    }
    
    /***
     * 
     * Galleries
     * 
     ***/
	
	public function showGalleriesAction()
    {
	  $sf_targetDir = "upload". DIRECTORY_SEPARATOR."galleries";
      $targetDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$sf_targetDir;
	  $finder = new Finder();
	  $galleries = array();
	  foreach($finder->in($targetDir)->directories()->sortByName() as $dir)
	  {
		$galleries[$dir->getFilename()] = array();
		$file_finder = new Finder();
		foreach($file_finder->in($dir->getRealpath())->files()->sortByModifiedTime() as $file)
		{
          $obj = new GalleryFile();
          $obj->setFilename($file->getFilename());
          $obj->setFullpath($file->getRealpath());
          //$obj->fullPath = $file->getRealpath();
          //$obj->fileName = $file->getFilename();
		  $galleries[$dir->getFilename()][] = $obj;//$file->getRealpath();
		  
		}
	  }
	  //var_dump($galleries);
      return $this->render('MaithCommonAdminBundle:Albums:folders.html.twig', array('galleries' => $galleries));
    }
    
    public function uploadGalleryFormAction($gallery)
    {
      $token = $this->container->getParameter('maith_common_admin.upload_token');
      $encrypt = new Encrypt($token);
      
      return $this->render('MaithCommonAdminBundle:Albums:uploadGallery.html.twig', array('gallery' => $gallery, 'dataSession' => urldecode($encrypt->encrypt(session_id()))));
    }
    
    public function doGalleryFormUploadAction()
    {
      
      $gallery = $this->getRequest()->get('gallery');
      $fileName = $this->getRequest()->get('name', 0);
      $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
      $sf_targetDir = "upload". DIRECTORY_SEPARATOR."galleries".DIRECTORY_SEPARATOR.$gallery;
      $targetDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$sf_targetDir;
      if (!file_exists($targetDir))
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'id' => $gallery)));
      }
        
      $fileUploaded = $this->container->get('request')->files->get('file');
      //$name = uniqid() . '.' . $fileUploaded->guessExtension();
      $movedFile = $fileUploaded->move($targetDir, $fileName);
      if ($movedFile) 
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'result' => null, 'id' => $gallery)));
      }
      else
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'id' => $albumId)));
      }
      die;
    }
    
    public function removeGalleryFileAction($gallery, $file)
    {
      //$fileName = preg_replace('/[^\w\._]+/', '_', $file);
      $sf_targetDir = "upload". DIRECTORY_SEPARATOR."galleries".DIRECTORY_SEPARATOR.$gallery.DIRECTORY_SEPARATOR.$file;
      $targetDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'. DIRECTORY_SEPARATOR.$sf_targetDir;
      $response = new JsonResponse();
      $status = "OK";
      if(file_exists($targetDir))
      {
        $cache_dir = $this->get('kernel')->getCacheDir().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;
        GalleryFile::removeAllCacheOfFile($cache_dir, $gallery, $file);
        @unlink($targetDir);
      }
      else
      {
        $status = "ERROR";
      }
      $response->setData(array('status' => $status));
      return $response;
      die;
    }
    
}
