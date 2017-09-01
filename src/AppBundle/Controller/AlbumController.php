<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 01-09-17
 * Time: 18:12
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Album;
use AppBundle\Entity\Category;
use AppBundle\Entity\Artist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AlbumController extends Controller
{
    public function indexAction(){

        $listAlbum = array(
            array(
                'title'   => 'Hybrid Theory',
                'id'      => 6,

            ),
            array(
                'title'   => 'Barbie Girl',
                'id'      => 7,

            )

        );

        return $this->render('Album/index.html.twig', array(
            'listAlbum' => $listAlbum,
        ));
    }

    public function viewAction($id){

        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $album = $em->getRepository('AppBundle:Album')->find($id);

        if (null === $album) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }


        $listCategory = $em
            ->getRepository('AppBundle:Category')
            ->findBy(array('album' => $album))
        ;

        return $this->render('Album/view.html.twig', array(
            'album'           => $album,
            'listCategory' => $listCategory
        ));
    }

    public function addAction(Request $request){
        $album = new Album();
        $album->setTitle('Meteora');

        $artist = new Artist();
        $artist->setNom('Linkin Park');
        $artist->setInstrument('Guitare');
        $artist->setBiography('Leur Bio');

        $artist->setAlbum($album);

        $category = new Category();
        $category->setNom('Electro-Rock');
        $category->setDescription("Electro Rock");

        $album->setArtist($artist);

        $em = $this->getDoctrine()->getManager();

        $em->persist($album);

        $em->persist($category);

        $em->persist($artist);

        $em->flush();


        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('app_view', array('id' => $album->getId()));
        }

        return $this->render('Album/add.html.twig', array('album' => $album));
    }

}