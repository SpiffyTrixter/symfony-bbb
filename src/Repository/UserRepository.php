<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array|null $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array|null $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function searchPaginated(
        string|null $query = null,
        DateTime|null $createdAfter = null,
        DateTime|null $updatedAfter = null,
        bool|null   $hasConfigurations = null,
        int|null    $currentPage = null,
        int         $maxPerPage = 10
    ): Pagerfanta
    {
        $hasConfigurations ??= false;
        $currentPage ??= 1;
        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if ($query) {
            $queryBuilder
                ->andWhere('c.username LIKE :query')
                ->orWhere('c.email LIKE :query')
                ->setParameter('query', '%' . $query . '%');
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

        if ($hasConfigurations) {
            $queryBuilder
                ->join('c.configurations', 'configurations')
                ->andWhere('configurations.id IS NOT NULL');
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
