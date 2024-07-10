<?php

namespace App\Service;

use App\Entity\Program;

class ProgramDuration extends Program
{
    /**
     * Calculates the total duration of a program in days, hours, and minutes.
     *
     * @param Program $program The program entity to calculate the duration for.
     *
     * @return string The total duration in the format "X jours, Y heures et Z minutes" or "Y heures et Z minutes" if less than 24 hours.
     */
    public function calculate(Program $program): string
    {
        $totalMinutes = 0;

        $seasons = $program->getSeasons();
        foreach ($seasons as $season) {
            $episodes = $season->getEpisodes();
            foreach ($episodes as $episode) {
                $totalMinutes += $episode->getDuration(); // Assuming duration is in minutes in the Episode entityx
            }
        }
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        if ($hours < 24) {
            return sprintf('%d heures et %d minutes', $hours, $minutes);
        }

        $days = intdiv($hours, 24);
        $hours = $hours % 24;

        return sprintf('%d jours, %d heures et %d minutes', $days, $hours, $minutes);
    }

}