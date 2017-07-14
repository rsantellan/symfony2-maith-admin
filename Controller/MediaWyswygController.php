<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MediaWyswygController extends Controller
{

	public function wyswygShowFilesAction(Request $request)
    {
    	$ckeditor = $request->query->get('CKEditor');
    	$CKEditorFuncNum = $request->query->get('CKEditorFuncNum');
    	$lang = $request->query->get('langCode');
        return $this->render('MaithCommonAdminBundle:Wyswyg:showFiles.html.twig', array(
            'folders' => $this->get('media_wyswyg_manager')->getAllFilesWithData(),
            'ckeditor' => $ckeditor,
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'lang' => $lang,
        ));
    }

    public function wyswygAddFolderFormAction(Request $request){
    	 $form = $this->createFormBuilder()
	        ->add('name', TextType::class)
	        ->setAction($this->generateUrl('maith_admin_wyswyg_media_add_folder'))
    		  ->setMethod('POST')
	        ->getForm();
        $form->handleRequest($request);
        $response = new JsonResponse();
        $isvalid = true;
        $message = '';
        $html = '';
        $reload = false;
  	    if ($form->isSubmitted() && $form->isValid()) {
  	        // data is an array with "name", "email", and "message" keys
  	        $data = $form->getData();
  	        $reload = true;
  	        $isvalid = $this->get('media_wyswyg_manager')->createFolder($data['name']);
  	    } else {
  	    	$html = $this->renderView('MaithCommonAdminBundle:Wyswyg:_addFolderForm.html.twig', array(
  			            'form' => $form->createView(),
  			        ));
  	    }
  	    $response->setData(array(
  		        'isvalid' => $isvalid,
  		        'message' => $message,
  		        'html' => $html,
  		        'reload' => $reload,
  	      	));
  	    return $response;
    }

	public function uploadFormAction($name)
    {
      return $this->render('MaithCommonAdminBundle:Wyswyg:upload.html.twig', array('folder' => $name));
    }    

    public function refreshFolderAction(Request $request){
      $folder = $request->get('folder');
      $files = $this->get('media_wyswyg_manager')->getFiles($folder);
      $response = new JsonResponse();
      $response->setData(array(
          'status' => 'OK',
          'options' => array('html' => $this->renderView('MaithCommonAdminBundle:Wyswyg:folder.html.twig', array('name' => $folder, 'files' => $files)))
      ));
      return $response;
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
    
    public function doUploadAction(Request $request)
    {
      $logger = $this->get('logger');
      $folder = $request->request->get('folder');
      $logger->debug(sprintf('The folder name is: %s', $folder));
      $targetDir = $this->get('media_wyswyg_manager')->checkFolder($folder);
      $logger->debug("final folder is: ".$targetDir);
      if ($targetDir === NULL)
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'folder' => $folder)));
      }
        
      $fileUploaded = $request->files->get('file');
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
        return new Response(json_encode(array("jsonrpc" => '2.0', 'result' => null, 'id' => $folder)));
      }
      else
      {
        return new Response(json_encode(array("jsonrpc" => '2.0', 'error' => array('code' => 100, 'message' => "Failed to open temp directory."), 'id' => $gallery)));
      }
      die;
    }

    public function downloadFileAction($folder, $file)
    {
      $fileObject = $this->get('media_wyswyg_manager')->getFile($folder, $file);
      $content = $fileObject-> getContents();
      $response = new Response();
      $fileMetadata = $this->retrieveExtensionAndMiMeType($fileObject->getFilename());
      $response->headers->set('Content-Type', $fileMetadata['mime']);
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileObject->getFilename());
      $response->setContent($content);
      return $response;
    }

    public function removeFileAction($folder, $file)
    {
      $result = $this->get('media_wyswyg_manager')->removeFile($folder, $file);
      $response = new JsonResponse();
      $response->setData(array(
          'result' => $result          
      ));
      return $response;
    }

    public function showFileAction(Request $request, $folder, $file)
    {
      $fileObject =  $this->get('media_wyswyg_manager')->getFile($folder, $file);
      $choices = [
        'Miniatura basico' => 't', 
        'Miniatura tomando el lado mas grande' => 'ot',
        'Respetar el ancho' => 'rce',
        'Hacer un resize maximo con los parametros dados' => 'mpr',
        'Hacer un resize centrado' => 'rcce',
        'Original' => '', 
      ];
      $form = $this->createFormBuilder()
        ->add('width', IntegerType::class, array('required' => true))
        ->add('heigth', IntegerType::class, array('required' => true))
        ->add('tipo', ChoiceType::class, array('choices' => $choices, 'choices_as_values' => true, 'required' => false))
        ->setAction($this->generateUrl('maith_admin_wyswyg_show_file', ['folder' => $folder, 'file' => $file]))
        ->setMethod('POST')
        ->getForm();
      $form->handleRequest($request);
      $response = new JsonResponse();
      $isvalid = true;
      $message = '';
      $html = '';
      $url = '';
      $close = false;
      if ($form->isSubmitted() && $form->isValid()) {
          // data is an array with "name", "email", and "message" keys
          $data = $form->getData();
          $close = true;
          $url = $this->get('maith_common_image.image.urlgenerator')->mImageFilter($fileObject->getPathName(), $data['width'], $data['heigth'], $data['tipo'], false, true);
          $this->get('logger')->debug('url', [$url]);
          //$isvalid = $this->get('media_wyswyg_manager')->createFolder($data['name']);
      } else {
        $html = $this->renderView('MaithCommonAdminBundle:Wyswyg:_useFileForm.html.twig', array(
                  'form' => $form->createView(),
                  'folder' => $folder,
                  'filename' => $file,
                  'file' => $fileObject,
              ));
      }
      $response->setData(array(
            'isvalid' => $isvalid,
            'message' => $message,
            'html' => $html,
            'close' => $close,
            'url' => $url,
          ));
      return $response;
    }

}