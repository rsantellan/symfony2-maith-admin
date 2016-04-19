<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminParametersController extends Controller
{
  public function showParametersAction(Request $request)
  {
    $parametersService = $this->get('maith_common.parameters');
    $yamlData = $parametersService->getParametersList();
    $formParameters = $this->createFormBuilder();
    $counter = 1;
    $counters = array();
    foreach($yamlData['parameters'] as $key => $data)
    {
        $formParameters->add('param_'.$counter, 'text', array(
              'data' => $data,
              'label' => $key,
              'required' => true,
          ));
        $counters[$key] = $counter;
        $counter++;
    }
    if ($request->isMethod('POST')) {
      $realForm = $formParameters->getForm();
      $realForm->bind($request);
      if ($realForm->isValid()) {
        $formData = $realForm->getData();
        $saveDataArray = array();
        foreach($counters as $key => $value)
        {
          $saveDataArray[$key] = $formData['param_'.$value];
        }
        $parametersService->saveParameters($saveDataArray);
        return $this->redirect($this->generateUrl('maith_admin_parameters_config'));
        die;
      }
    }
    return $this->render('MaithCommonAdminBundle:AdminParameters:showParameters.html.twig', array(
      'form' => $formParameters->getForm()->createView(),
      'admin_menu' => 'parameters',
      'counters' => $counters,
    ));

    die;
  }
}
