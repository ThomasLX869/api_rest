<?php

namespace App\Controller;

use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/listeRegions", name="listeRegions")
     */
    public function listeregions(SerializerInterface $serializer)
    {
        // get regions names and codes
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
//        $mesRegionsTab = $serializer->decode($mesRegions, "json");
//        $mesRegionsObjet = $serializer->denormalize($mesRegionsTab, 'App\Entity\Region[]');
        $mesRegions = $serializer->deserialize($mesRegions, 'App\Entity\Region[]', 'json');
        return $this->render('api/index.html.twig', [
            'mesRegions' => $mesRegions
        ]);
    }

    /**
     * @Route("/listeDepsParRegion", name="listeDepsParRegion")
     */
    public function listeDepsParRegion(Request $request,SerializerInterface $serializer)
    {
        // je récupère la région sélectionnée dans le form
        $codeRegion = $request->query->get("region");

        // je récupère les régions
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        $mesRegions = $serializer->deserialize($mesRegions, 'App\Entity\Region[]', 'json');

        // je récupère la liste de mes départements
        if($codeRegion == null || $codeRegion == "Toutes") {
            $mesDeps =  file_get_contents('https://geo.api.gouv.fr/departements');
        }else {
            $mesDeps =  file_get_contents('https://geo.api.gouv.fr/regions/' . $codeRegion . '/departements');
        }
        // décodage du format json en tableau
        $mesDeps = $serializer->decode($mesDeps, "json");

        return $this->render('api/listDepsParRegion.html.twig', [
            'mesRegions' => $mesRegions,
            'mesDeps' => $mesDeps
        ]);
    }
}
