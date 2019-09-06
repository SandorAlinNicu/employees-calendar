<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntervalRepository")
 * @Table(name="`interval`")
 */
class Interval
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fromDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $toDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get total number of days, weekends included.
     *
     * @return float|int
     */
    public function getTimeInterval()
    {
        if ($this->toDate) {
            $datediff = $this->getToDate()->getTimestamp() - $this->getFromDate()->getTimestamp();
            return round($datediff / (60 * 60 * 24)) + 1;
        } else return 1;
    }

    /**
     * Number of working days covered by interval.
     *
     * @return float|int
     */
    public function getNumberOfDaysWithoutWeekend()
    {
        $totalNumberOfDays = $this->getTimeInterval();
        $startDate = $this->getFromDate()->getTimestamp();
        $finishDate = $this->getToDate()->getTimestamp();
        while ($startDate <= $finishDate) {
            $currentDate = strtotime('+1 day', $startDate);
            $dayofweek = date('w', $currentDate);
            if ($dayofweek == 0 || $dayofweek == 6) {
                $totalNumberOfDays = $totalNumberOfDays - 1;
            }
            $startDate = $currentDate;
        }
        return $totalNumberOfDays;
    }

    /**
     * Get days in current month covered by this interval.
     *
     * This will exclude Saturdays and Sundays.
     *
     * @param string $date
     *   Current date or custom date.
     *
     * @return array
     *   List of days covered by this interval, e.g. [1, 2, 3, 4]
     */
    public function getDaysArrayInCurrentMonth($date)
    {
        $time = strtotime($date);

        // Get boundaries of calendar page, we are displaying only one month, so we want to detect days of each interval
        // that are in these boundaries.
        // Get timestamp of start day of current month.
        $startMonth = strtotime(date('Y-m-01', $time));
        // Get timestamp of start day of next month.
        $startNextMonth = strtotime('+1 month', $startMonth);

        $startDate = $this->getFromDate()->getTimestamp();
        $finishDate = $this->getToDate()->getTimestamp();
        $days = [];

        // Check if current interval has any days in boundaries first.
        if (
            $startDate >= $startMonth && $startDate <= $startNextMonth ||
            $finishDate >= $startMonth && $finishDate <= $startNextMonth
        ) {
            // Loop until we complete the interval.
            while ($startDate <= $finishDate) {
                // Check if start date is in current month, otherwise just increment with one day.
                if ($startDate >= $startMonth && $startDate < $startNextMonth) {
                    $dayofweek = date('w', $startDate);
                    if ($dayofweek != 0 && $dayofweek != 6) {
                        $days[] = date('j', $startDate);
                    }
                }
                // Increment startDate with one day.
                $startDate = strtotime('+1 day', $startDate);
            }
        }
        return $days;
    }
}
