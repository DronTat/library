<?php

namespace App\Controller;

use App\Entity\Books;
use Psr\Log\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BooksAddController extends AbstractController
{
    /**
     * @Route("/books/range_year", name="range_year", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function booksInRangeYear(Request $request)
    {
        $data = $request->request->all();

        $start = $data['start'];
        $end = $data['end'];
        if ((!is_int($start) and !is_null($start)) or (!is_int($end) and !is_null($end))){
            throw new InvalidArgumentException('Argument $start or $end only accepts integers. $start = '. gettype($start) . ', $end = '. gettype($end));
        }
        $response = $this->getDoctrine()->getRepository(Books::class)->findRangeYear($start, $end);

        return $this->json($response);
    }

    /**
     * @Route("/books/average", name="average", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function averageValue(Request $request)
    {
        $name = $request->query->get('name');
        $arrayYears = $this->getDoctrine()->getRepository(Books::class)->findYearsValues();
        $data = array();
        foreach ($arrayYears as $k) {
            $db = $this->getDoctrine()->getRepository(Books::class)->findAverageValue($k['year'], $name);
            if ($db){
                $data[$k['year']] = $db;
            }
        }
        
        return $this->json([
            'data' => $data
            ]);
    }
}
