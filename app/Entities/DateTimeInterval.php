<?php

namespace App\Entities;

class DateTimeInterval
{
    private \DateTime $_startAt;
    private \DateTime | null $_endAt;

    /**
     * @throws \Exception
     */
    public function __construct(int $start, int | null $period)
    {
        $startDate = new \DateTime();
        $this->_startAt = $this->_updateDateTime($startDate, $start);
        $this->_endAt = null;
        if ($period !== null) {
            $endDate = new \DateTime($this->_startAt->format('Y-m-d H:i:s'));
            $this->_endAt = $this->_updateDateTime($endDate, $period);
        }
    }

    /**
     * @return \DateTime
     */
    public function getStartAt(): \DateTime
    {
        return $this->_startAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt(): \DateTime | null
    {
        return $this->_endAt;
    }

    /**
     * @throws \Exception
     */
    private function _updateDateTime(\DateTime $currentDate, int $days): \DateTime
    {
        $formatInterval = $this->_formatterInterval($days);
        $interval = new \DateInterval($formatInterval);
        if ($days > 0) {
            return $currentDate->add($interval);
        }
        return $currentDate->sub($interval);
    }

    private function _formatterInterval(int $days): string
    {
        if ($days < 0) {
            $days *= -1;
        }

        if ($days < 31) {
            return 'P' . $days . 'D';
        }

        $months = round($days / 31);

        if ($months < 12 ) {
            return 'P' . $months . 'M';
        }

        $years = round($months / 12);

        return 'P' . $years . 'Y';

    }
}
