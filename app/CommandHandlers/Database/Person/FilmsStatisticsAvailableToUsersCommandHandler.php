<?php

namespace App\CommandHandlers\Database\Person;

use App\Console\Commands\Person\FilmsStatisticsAvailableToUsers;
use App\Queries\Person\Users\UserQueriesInterface;

/**
 * Класс для получения статистики фильмов, которые имеются в наличие у пользователей
 */
final class FilmsStatisticsAvailableToUsersCommandHandler 
{
    public function __construct(
            private UserQueriesInterface $queries,
    ) {
    }
    
    public function handle(FilmsStatisticsAvailableToUsers $command): void
    {
        $films = $this->queries->getArray($this->getQuery());
        
        if ($command->option('isFile')) {
            $command->writeFile($films, "commannds\Dvd\FilmsStatisticsAvailableToUsers_" . date('d-m-Y') . ".txt");
        } else {
            $command->writeConsole($films);
        }
    }
    
    private function getQuery(): string
    {
        return <<<SQL
                SELECT
                    u.id,
                    u.login,
                    count(f.id) AS n
                FROM person.users u 
                LEFT JOIN person.users_films uf ON u.id = uf.user_id 
                LEFT JOIN dvd.films f ON f.id = uf.film_id
                GROUP BY u.id
                ORDER BY n DESC, u.login
            SQL;
    }
}
