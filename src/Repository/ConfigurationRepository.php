<?php

namespace App\Repository;

use App\Entity\Configuration;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Configuration>
 *
 * @method Configuration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Configuration|null findOneBy(array $criteria, array|null $orderBy = null)
 * @method Configuration[]    findAll()
 * @method Configuration[]    findBy(array $criteria, array|null $orderBy = null, $limit = null, $offset = null)
 */
final class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    public function save(Configuration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Configuration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByUser(User $user, bool $flush = false): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchPaginated(
        string|null $query = null,
        string|null $color = null,
        array|null $types = null,
        DateTime|null $createdAfter = null,
        DateTime|null $updatedAfter = null,
        int|null    $currentPage = null,
        int         $maxPerPage = 10
    ): Pagerfanta
    {
        $currentPage ??= 1;
        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if ($query) {
            $queryBuilder
                ->andWhere('c.name LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if ($color) {
            $queryBuilder
                ->andWhere('c.hexColor = :color')
                ->setParameter('color', $color);
        }

        if ($types) {
            $queryBuilder
                ->andWhere('c.type IN (:types)')
                ->setParameter('types', $types);
        }

        if ($createdAfter) {
            $queryBuilder
                ->andWhere('c.createdAt >= :createdAfter')
                ->setParameter('createdAfter', $createdAfter);
        }

        if ($updatedAfter) {
            $queryBuilder
                ->andWhere('c.updatedAt >= :updatedAfter')
                ->setParameter('updatedAfter', $updatedAfter);
        }

        return $this->paginate($queryBuilder, $currentPage, $maxPerPage);
    }

    public function findAllPaginated(int|null $currentPage = null, int $maxPerPage = 10): Pagerfanta
    {
        $currentPage ??= 1;
        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        return $this->paginate($queryBuilder, $currentPage, $maxPerPage);
    }

    public function findByUserPaginated(User $user, int|null $currentPage = null, int $maxPerPage = 10): Pagerfanta
    {
        $currentPage ??= 1;
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('c.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC');

        return $this->paginate($queryBuilder, $currentPage, $maxPerPage);
    }

    public function findAllTypes(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c.type')
            ->distinct(true)
            ->orderBy('c.type', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    private function paginate(QueryBuilder $queryBuilder, int|null $currentPage = null, int $maxPerPage = 10): Pagerfanta
    {
        $currentPage ??= 1;
        $adapter = new QueryAdapter($queryBuilder);
        return Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $currentPage,
            $maxPerPage
        );
    }
}
