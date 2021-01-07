<?php

namespace App\Doctrine\Library\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Library\Loan;
use App\Entity\Library\Reader;
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

        $alias = $queryBuilder->getRootAliases()[0];
        switch ($resourceClass) {
            case Loan::class:
                $queryBuilder->leftJoin("$alias.loaner", "loaner", "WITH", "$alias.id = loaner.id");
                $queryBuilder->leftJoin("$alias.borrower", "borrower", "WITH", "$alias.id = borrower.id");
                break;
            case Reader::class:
                // You need to change the Security Provider to use real User instead of inMemory (this provider does'nt allow id
                if (method_exists($user, 'getId')) {
                    $this->logger->warn('User does not have any id so it might be because you are using in_memory provider => it is not possible to use this provider with the UserExtension to protect Reader resource');
                    break;
                }

                // This one is the standard one, when you have an Entity Provider in the security component that uses the Reader Entity to represent the logged User
                $queryBuilder->andWhere("$alias.email = :userEmail");
                if (!$queryBuilder->getParameter('userEmail')) {
                    $queryBuilder->setParameter('userEmail', $user->getUsername());
                }
                break;
            default:
        }
    }
}
