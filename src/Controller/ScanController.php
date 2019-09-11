<?php
//declare(strict_types=1);

namespace App\Controller;

use App\Entity\Books;
use Exception;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScanController extends AbstractController
{
    /**
     * @Route("/scan", name="scan", methods={"POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request, LoggerInterface $logger)
    {
        $data = $request->request->all();
        if (!is_int($data['isbn']) or !is_string($data['author_full_name']) or !is_string($data['title']) or !is_int($data['year'])){
//            throw new InvalidArgumentException('One of the arguments does not match the declared type!');
            return $this->json([
                'status' => 'error'
            ]);
        }
        $em = $this->getDoctrine()->getManager();

        $book = new Books();
        $book->setIsbn($data['isbn']);
        $book->setAuthorFullName($data['author_full_name']);
        $book->setTitle($data['title']);
        $book->setYear($data['year']);
        $book->setDataCreated(new \DateTime("now"));

        $em->persist($book);
        $em->flush();

        $logger->info('Запрос пользователя. isbn: '. $data['isbn']. ' (' . gettype($data['isbn']) . ')' .
                ', author_full_name: ' . $data['author_full_name'] . ' ('. gettype($data['author_full_name']) . ')' .
                ', title: ' . $data['title'] . ' ('. gettype($data['title']) . ')' .
                ', year: ' . $data['year'] . ' (' . gettype($data['year']) . ')');


        return $this->json([
            'status' => 'success'
        ]);
    }
}
