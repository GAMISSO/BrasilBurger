<?php
namespace App\Service;

interface StatistiqueService
{
    public function getStatistiquesJour(\DateTime $date): array;
    public function getStatistiquesPeriode(\DateTime $debut, \DateTime $fin): array;

}