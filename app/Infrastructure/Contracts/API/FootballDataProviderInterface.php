<?php
declare(strict_types=1);

namespace App\Infrastructure\Contracts\API;

/**
 * Interface para el proveedor Football-Data (contrato base)
 */
interface FootballDataProviderInterface
{
    /**
     * Obtener información de una competición por su id
     *
     * @param int|string $competitionId
     * @return array
     */
    public function getCompetition($competitionId): array;

    /**
     * Listar competiciones disponibles
     *
     * @param array $params Opcionales (p.ej. country, season)
     * @return array
     */
    public function listCompetitions(array $params = []): array;

    /**
     * Obtener partidos de una competición (o filtros generales)
     *
     * @param array $params Filtros: competitionId, dateFrom, dateTo, status, season, matchday
     * @return array
     */
    public function getMatches(array $params = []): array;

    /**
     * Obtener un partido por su id
     *
     * @param int|string $matchId
     * @return array
     */
    public function getMatch($matchId): array;

    /**
     * Listar equipos de una competición
     *
     * @param int|string $competitionId
     * @return array
     */
    public function listTeams($competitionId): array;

    /**
     * Obtener información de un equipo por su id
     *
     * @param int|string $teamId
     * @return array
     */
    public function getTeam($teamId): array;

    /**
     * Obtener la clasificación (standings) de una competición
     *
     * @param int|string $competitionId
     * @return array
     */
    public function getStandings($competitionId): array;

    /**
     * Obtener máximos goleadores de una competición
     *
     * @param int|string $competitionId
     * @return array
     */
    public function getScorers($competitionId): array;

    /**
     * Realizar una llamada genérica al endpoint de Football-Data si se necesita
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function request(string $endpoint, array $params = []): array;
}
