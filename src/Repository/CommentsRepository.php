<?php

namespace App\Repository;

use App\Entity\Comments;
use App\Entity\Tricks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comments>
 *
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function getCommentsPaginated(Tricks $tricks, int $offset): Paginator
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('App\Entity\Comments', 'c')
            ->where("c.tricks = :tricks")
            ->setParameter("tricks", $tricks)
            ->setFirstResult($offset)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->orderBy('c.createdAt', 'desc')
            ->getQuery();

        return new Paginator($query);
    }
}
