<?php

namespace App\Doctrine\Library\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Library\Loan;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /** @var Security */
    protected $security;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * MedicalSupportsExtension constructor.
     * @param Security $security
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(Security $security, ManagerRegistry $managerRegistry)
    {
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->filterResultSet($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->filterResultSet($queryBuilder, $resourceClass);
    }

    private function filterResultSet(QueryBuilder $queryBuilder, string $resourceClass)
    {
        /** @var UserInterface $user */
        $user = $this->security->getUser();

        // admin can access all
        if ($this->security->isGranted('ROLE_ADMIN')
            || null === $user) {
            return;
        }

        switch ($resourceClass) {
            case Loan::class:
                $loanAlias = $queryBuilder->getRootAliases()[0];
                $queryBuilder->leftJoin("$loanAlias.loaner", "loaner", "WITH", "$loanAlias.id = loaner.id");
                $queryBuilder->leftJoin("$loanAlias.borrower", "borrower", "WITH", "$loanAlias.id = borrower.id");
                break;
            // do not break here ! or you can let DoctrineFilter taking part App\Filter\UserFilter
            /*case Booking::class:
                $booking = $booking ?? $queryBuilder->getRootAliases()[0];
                $queryBuilder->innerJoin("$booking.user", 'user');
                $queryBuilder->andWhere(':user = user');
                $queryBuilder->setParameter(':user', $this->security->getUser());*/
        }
    }
}
