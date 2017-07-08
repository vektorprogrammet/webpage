<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use Doctrine\ORM\EntityRepository;

class SchoolRepository extends EntityRepository
{
    /**
     * @param $department
     *
     * @return School[]
     */
    public function findSchoolsByDepartment($department)
    {
        $schools = $this->getEntityManager()->createQuery('
		
		SELECT s, d
		FROM AppBundle:School s
		JOIN s.departments d		
		WHERE d = :department
		')
            ->setParameter('department', $department)
            ->getResult();

        return $schools;
    }

    public function findSchoolsByDepartmentQuery($department)
    {
        $query = $this->createQueryBuilder('s', 'd')
            ->from('AppBundle:School', 's')
            ->join('s.departments', 'd')
            ->where('d = :department')
            ->setParameter('department', $department);

        return $query;
    }

    public function getNumberOfSchools()
    {
        $schools = $this->getEntityManager()->createQuery('

		SELECT COUNT (s.id)
		FROM AppBundle:School s
		')
            ->getSingleScalarResult();

        return $schools;
    }

    public function findSchoolsWithoutCapacity(Department $department) {
    	return $this->createQueryBuilder('s')
		    ->leftJoin('s.capacities', 'capacities')
		    ->where('capacities.semester != :semester')
		    ->setParameter('semester', $department->getCurrentSemester())
		    ->orWhere('capacities.semester IS NULL');
    	$qb = $this->createQueryBuilder('s');
    	$qb2 = $this->_em->createQueryBuilder();

    	$schoolsWithCapacities = $qb2
		    ->select('capacity.school')
		    ->from('AppBundle:SchoolCapacity', 'capacity')
		    ->where('capacity.semester = :semester')
		    ->setParameter('semester', $department->getCurrentSemester());

    	return $qb
		    ->select('s')
		    ->where($qb->expr()->notIn('s.id', $schoolsWithCapacities->getDQL()));

    }
}
