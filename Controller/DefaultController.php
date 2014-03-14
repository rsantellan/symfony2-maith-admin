<?php

namespace Maith\Common\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MaithCommonAdminBundle:Default:index.html.twig');
    }
    
    public function retrieveDataTableTextsAction(Request $request)
    {
        $stringEnglish = '{
            "sEmptyTable":     "No data available in table",
            "sInfo":           "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty":      "Showing 0 to 0 of 0 entries",
            "sInfoFiltered":   "(filtered from _MAX_ total entries)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Show _MENU_ entries",
            "sLoadingRecords": "Loading...",
            "sProcessing":     "Processing...",
            "sSearch":         "Search:",
            "sZeroRecords":    "No matching records found",
            "oPaginate": {
                "sFirst":    "First",
                "sLast":     "Last",
                "sNext":     "Next",
                "sPrevious": "Previous"
            },
            "oAria": {
                "sSortAscending":  ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        }';
        
        $stringSpanish = '"oLanguage" : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }';
        $arraySpanish = array(
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oPaginate" => array(
                "sFirst"=>    "Primero",
                "sLast"=>     "Último",
                "sNext"=>     "Siguiente",
                "sPrevious"=> "Anterior"
            ),
            "oAria" => array(
                "sSortAscending" =>  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente"
            )
        );

        $response = new JsonResponse();
        $locale = $request->getLocale();
        if($locale == 'es')
        {
            $response->setData($arraySpanish);
        }
        else 
        {
            $response->setData(array());
        }
        return $response;
    }
}
