<?php

namespace App\Services;

use App\Repositories\Contracts\EtapaRepositoryInterface;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use Illuminate\Support\Collection;

class CalendarService
{
    public function __construct(
        private JuegoRepositoryInterface $juegoRepository,
        private EtapaRepositoryInterface $etapaRepository
    ) {}

    public function getMatchesByStage(int $stageId): Collection
    {
        return $this->juegoRepository->getByStage($stageId);
    }

    public function getUpcomingMatches(): Collection
    {
        return $this->juegoRepository->getUpcoming();
    }

    public function getMatchesByDate(string $date): Collection
    {
        return $this->juegoRepository->getByDate($date);
    }

    public function getStages(): Collection
    {
        return $this->etapaRepository->all();
    }
}