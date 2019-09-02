<?php
// src/Entity/Task.php
namespace App\Entity;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Holiday
{
    protected $name;
    protected $departments;
    protected $positions;
    /**
     * @Assert\Range(
     *      min = 1,
     *      max = 22,
     *      minMessage = "You should take at least 1 day off",
     *      maxMessage = "You are allowed to take a maximum of 22 days of holiday"
     * )
     */
    protected $days;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * @param mixed $departments
     */
    public function setDepartments($departments)
    {
        $this->departments = $departments;
    }

    /**
     * @return mixed
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param mixed $positions
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }


}