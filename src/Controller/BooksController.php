<?php

namespace App\Controller;

use App\Entity\Books;
use Faker\Factory;
use Psr\Log\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    /**
     * @Route("/books/top", name="top_author", methods={"GET"})
     */
    public function top()
    {
        $data = $this->getDoctrine()->getRepository(Books::class)->findAllTopAuthorField();

        return $this->json([
            'data' => $data
        ]);
    }

    /**
     * @Route("/books/author", name="author", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function booksAuthor(Request $request)
    {
        $name = $request->query->get('name');
        if (!is_string($name)){
            throw new InvalidArgumentException('Argument $name only accepts string. $name = '. gettype($name));
        }

        $data = $this->getDoctrine()->getRepository(Books::class)->findBooksByOneAuthor($name);

        return $this->json([
            'data' => $data
        ]);
    }

    /**
     * @Route("/books/random", name="random", methods={"GET"})
     */
    public function randomData()
    {
        $faker = Factory::create('ru_RU');
        $em = $this->getDoctrine()->getManager();

        $array = array();
        for ($i = 0; $i < 20; $i++){
            $array[] = $faker->lastName;
        }

        for ($i = 1; $i < 200; $i++){
            $book = new Books();
            $book->setIsbn(rand(1000,2000));
            $book->setAuthorFullName($array[array_rand($array)]);
            $book->setTitle($faker->titleMale);
            $book->setYear(rand(2009,2019));
            $book->setDataCreated(new \DateTime("now"));

            $em->persist($book);
            $em->flush();
        }
        return $this->json([
            'success'
        ]);
    }
}
