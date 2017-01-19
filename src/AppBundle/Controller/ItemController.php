<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Form\Type\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ItemController extends Controller
{
    /**
     * @Route("/item/{id}", name="item", defaults={"id":null})
     */
    public function addAction(Request $request)
    {

        $session = new Session();
        $item = new Item();

        if ($request->get('id')){

            $id_item = $request->get('id');

            $repository = $this->getDoctrine()->getRepository('AppBundle:Item');
            $item = $repository->find($id_item);

            return $this->render('item/show.html.twig',[ 'item' => $item ]);

        }

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Utilsation d'une formType

        $form = $this->createForm(ItemType::class, $item);

        // Creation du formulaire dans le controller

/*
        $form = $this->createFormBuilder($item)
            ->add('title',TextType::class)
            ->add('description', TextType::class)
            ->add('code', IntegerType::class)
            ->add('collection', TextType::class)
            ->add('imageUrl', UrlType::class)
            // ->add('user', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm()

        ;
*/

        $form->handleRequest($request);

        if($form->isValid()){

            $item = $form->getData();


/*
            $item->setTitle($data['title']);
            $item->setDescription($data['description']);
            $item->setCode($data['code']);
            $item->setCollection($data['collection']);

*/

            $item->setUser($this->getUser()->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            $session->getFlashBag()->add('infos', 'Le post a été rajouté avec success' );

            return $this->redirectToRoute('items');
        }

        // replace this example code with whatever you need
        return $this->render('item/add.html.twig', [
        'form' =>$form->createView(),

        ]);
    }


    /**
     * @Route("/items", name="items")
     */
    public function itemsAction()
    {

        $repository = $this->getDoctrine()->getRepository('AppBundle:Item');

        $items = $repository->findAll();
        $collections = $repository->getCollections();
        return $this->render('item/list.html.twig', [ 'items' =>$items, 'collections' => $collections  ]);
        
    }


    /**
     * @Route("/item/edit/{id}", name="item_edit")
     */

    public function editAction(Request $request, $id)
    {

        $session = new Session();
        $item = $this->getDoctrine()
            ->getRepository('AppBundle:Item')
            ->find($id)
        ;

        $em = $this->getDoctrine()->getManager();



        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if($form->isValid()){

            $item = $form->getData();

            // Change Owner : si le bouton changeOwner est cliquer : Uniquement role Admin

            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            if($form->get('changeOwner')->isClicked()){

                $item->setUser($this->getUser()->getId());
                $session->getFlashBag()->add('infos','L\'utilisateur a été changé  ! ' );
            }
            $em->persist($item);
            $em->flush();

            $session->getFlashBag()->add('infos','Item a ete modifié avec succès ! ' );

        }

        return $this->render('item/edit.html.twig', [  'form' =>$form->createView() ]);


    }



    /**
     * @Route("/item/remove/{id}", name="removeItem")
     */
    public function removeAction(Request $request, $id)
    {


        $session = new Session();
        // Securisation des routes : Utilisation des roles dans le controller : On ne peut pas executer l'action qui suit si on n'a pas le role approprié.

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('AppBundle:Item');
        $item = $repository->find($id);

        if ($item->isAuthor($this->getUser())){

            $em->remove($item);
            $em->flush();

            $session->getFlashBag()->add('infos','Item supprimé avec succès !' );


        }else{

            $session->getFlashBag()->add('errors','Item ne vous appartient pas  !' );
        }

        return $this->redirectToRoute('items');


    }




}
