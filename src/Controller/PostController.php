<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

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

                return $this->redirectToRoute('submit_form');
            }
        }

        return $this->render('post/submit.html.twig', array(
            'name'                 => $name,
            'description'          => $description,
            'name_validate'        => $nameValidate,
            'description_validate' => $descriptionValidate
        ));
    }

    /**
     * @Route("/click", name="click")
     */
    public function click()
    {
        return $this->render('post/click.html.twig');
    }

    /**
     * @Route("/result", name="result")
     */
    public function result()
    {
        return new Response('Redirect to here');
    }

    /**
     * @Route("/request/form", name="request_form")
     */
    public function requestForm()
    {
        $url           = 'http://fooddocs.local/worker/42';
        $filePath      = 'file/test.pdf';
        $content       = file_get_contents($filePath);
        $contentToSend = base64_encode($content);


        $params = array(
            'name'                    => 'Karin Repp',
            'position'                => 'Testija',
            'email'                   => 'margus.pala+fooddocs@gmail.com',
            'idcode'                  => '49301011234',
            'healthCertFile'          => 'MTIzCg==',
            'healthCertFileName'      => 'chien12333.pdf',
            'healthCertDate'          => '2018-05-20',
            'hygieneTrainingFile'     => $contentToSend,
            'hygieneTrainingFileName' => 'eqwr.pdf',
            'hygieneTrainingDate'     => '2018-05-20',
        );

        $params = json_encode($params);
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

// This should be the default Content-type for POST requests
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));

        $result = curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
        }

        curl_close($ch);
        print_r($result);
        die;
        //return $this->render('post/request_form.html.twig');
    }
}
