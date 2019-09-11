<?php

namespace App\Repository;

use App\Entity\Books;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Books|null find($id, $lockMode = null, $lockVersion = null)
 * @method Books|null findOneBy(array $criteria, array $orderBy = null)
 * @method Books[]    findAll()
 * @method Books[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BooksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Books::class);
    }

    /**
     * @return Books[] Returns an array of Books objects
     */
    public function findAllTopAuthorField()
    {
        return $this->createQueryBuilder('b')
            ->select('b.author_full_name')
            ->addSelect('count(b.author_full_name)'. ' AS count')
            ->groupBy('b.author_full_name')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $value string
     * @return Books|null Returns an array of Books objects
     */
    public function findBooksByOneAuthor($value)
    {
        return $this->createQueryBuilder('b')
            ->select('b.author_full_name, b.title, b.isbn')
            ->andWhere('b.author_full_name like concat(\'%\', :val, \'%\')')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $start int
     * @param $end int
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findRangeYear($start, $end){
        $em = $this->getEntityManager()->getConnection();
        $sql = 'SELECT title, isbn, author_full_name 
                FROM books
                WHERE YEAR BETWEEN IFNULL(:startDate, (SELECT MIN(YEAR) FROM books)) AND IFNULL(:endDate, (SELECT MAX(YEAR) FROM books)) ORDER BY YEAR ASC';
        $stmt = $em->prepare($sql);
        $stmt->execute(['startDate' => $start, 'endDate' => $end]);
        return $stmt->fetchAll();
    }

    /**
     * @param $year int
     * @param $name string
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAverageValue($year, $name)
    {
        $em = $this->getEntityManager()->getConnection();
        $sql = 'SELECT author_full_name, COUNT(YEAR) AS `count` 
                FROM books 
                WHERE YEAR = :year_p AND (:name_p IS NULL OR author_full_name LIKE CONCAT(\'%\',:name_p,\'%\'))
                GROUP BY author_full_name';
        $stmt = $em->prepare($sql);
        $stmt->execute(['year_p' => $year, 'name_p' => $name]);
        return $stmt->fetchAll();
    }

    /**
     * @return mixed[]
     */
    public function findYearsValues()
    {
        return $this->createQueryBuilder('b')
            ->select('b.year')
            ->groupBy('b.year')
            ->getQuery()
            ->getResult()
            ;
    }
}
