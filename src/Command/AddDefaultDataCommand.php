<?php

namespace App\Command;

use App\Entity\Department;
use App\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddDefaultDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'add-default-data';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $departments = array(
            'PHP',
            '.NET',
            'JS',
            'OPS'
        );
        $positions = array(
            'developer',
            'junior developer',
            'manager',
            'architect'
        );
        $departmentRepository = $this->em->getRepository(Department::class);
        $existingDepartments = $departmentRepository->findAll();
        $existingDepartments = array_map(function ($department) {
            return $department->getName();
        }, $existingDepartments);
        foreach ($departments as $department) {
            if (!in_array($department, $existingDepartments)) {
                $newDepartment = new Department();
                $newDepartment->setName($department);
                $this->em->persist($newDepartment);
            }
        }

        $positionRepository = $this->em->getRepository(Position::class);
        $existingPositions = $positionRepository->findAll();
        $existingPositions = array_map(function ($position) {
            return $position->getName();
        }, $existingPositions);
        foreach ($positions as $position) {
            if (!in_array($position, $existingPositions)) {
                $newPosition = new Position();
                $newPosition->setName($position);
                $this->em->persist($newPosition);
            }
        }
        $this->em->flush();
    }
}