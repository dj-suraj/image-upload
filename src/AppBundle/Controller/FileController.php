<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{

    /**
     * @param Request $request
     * @Route("api/upload", name="upload_image")
     * @Method({"POST"})
     * @return JsonResponse
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function uploadImage(Request $request)
    {

        $file = new File();

        $uploadedImage = $request->files->get('file');

        /**
         * @var UploadedFile $image
         */
        $image = $uploadedImage;

        $imageName=md5(uniqid('', true)).'.'.$image->guessExtension();
        $image->move($this->getParameter('image_directory'), $imageName);
        $file->setImage($imageName);

        $em = $this->getDoctrine()->getManager();
        $em->persist($file);
        $em->flush();

        $response = [
            'code' => 0,
            'message' => 'File uploaded successfully!',
            'errors' => null,
            'result' => null
        ];

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Route("api/images", name="show_images")
     * @Method({"GET"})
     * @return JsonResponse
     */
    public function getImages()
    {
        $images = $this->getDoctrine()->getRepository('AppBundle:File')->findAll();

        $data = $this->get('jms_serializer')->serialize($images, 'json');

        $response = [
            'message' => 'Images loaded',
            'result' => json_decode($data)
        ];

        return new JsonResponse($response);
    }


    /**
     * @param $id
     * @Route("api/image/{id}", name="show_image")
     * @Method({"GET"})
     * @return JsonResponse
     */
    public function getImage($id)
    {

        $image = $this->getDoctrine()->getRepository('AppBundle:File')->find($id)->getImage();

        $response = [
            'code' => 0,
            'message' => 'Got image with success',
            'errors' => null,
            'result' => null
        ];

        return new JsonResponse($response);
    }
}

