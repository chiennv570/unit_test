<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\DBAL\DBALException;

class PostController extends Controller
{
    public function submitForm(Request $request)
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
                     ->add('name', TextType::class)
                     ->add('description', TextareaType::class, array('required' => false))
                     ->add('submit', SubmitType::class)
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $product = $form->getData();

            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('submit');
        }

        return $this->render('post/submit_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/submit/form", name="submit_form")
     */
    public function submit(Request $request)
    {
        $data                = $request->request->all();
        $nameValidate        = false;
        $descriptionValidate = false;
        $name                = null;
        $description         = null;

        if ($data) {
            $product = new Product();

            if (isset($data['name'])) {
                $name = $data['name'];

                if (strlen($data['name']) >= 10) {
                    $product->setName($data['name']);
                } else {
                    $nameValidate = true;
                }
            }

            if (isset($data['description'])) {
                $description = $data['description'];

                if (strlen($data['description']) >= 10) {
                    $product->setDescription($data['name']);
                } else {
                    $descriptionValidate = true;
                }
            }

            if ( ! $nameValidate && ! $descriptionValidate) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($product);
                    $em->flush();
                } catch (DBALException $e) {
                    return new Response("Saved successfully");
                }

                return $this->redirectToRoute('submit');
            }
        }

        return $this->render('post/submit.html.twig', array(
            'name'                 => $name,
            'description'          => $description,
            'name_validate'        => $nameValidate,
            'description_validate' => $descriptionValidate
        ));
    }
}
