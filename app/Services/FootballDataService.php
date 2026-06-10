<?php
declare(strict_types=1);

namespace App\Services;

use App\Infrastructure\Contracts\API\FootballDataProviderInterface;

/**
 * Servicio para acceder a datos de Football-Data a través del proveedor concreto.
 */
class FootballDataService
{
    private FootballDataProviderInterface $provider;

    public function __construct(FootballDataProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getCompetition(int $competitionId): array
    {
        return $this->provider->getCompetition($competitionId);
    }

    public function listCompetitions(array $params = []): array
    {
        return $this->provider->listCompetitions($params);
    }

    public function getMatches(array $params = []): array
    {
        return $this->provider->getMatches($params);
    }

    public function getMatch(int $matchId): array
    {
        return $this->provider->getMatch($matchId);
    }

    public function listTeams(int $competitionId): array
    {
        return $this->provider->listTeams($competitionId);
    }

    public function getTeam(int $teamId): array
    {
        return $this->provider->getTeam($teamId);
    }

    public function getStandings(int $competitionId): array
    {
        return $this->provider->getStandings($competitionId);
    }

    public function getScorers(int$competitionId): array
    {
        return $this->provider->getScorers($competitionId);
    }

    public function request(string $endpoint, array $params = []): array
    {
        return $this->provider->request($endpoint, $params);
    }
}
