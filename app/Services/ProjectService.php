<?php

namespace App\Services;

use App\Models\Project\Project;
use App\Models\Tariff\TariffProject;

class ProjectService
{
    private function formatProjectRelations(array $relations)
    {
        $with = array();
        if (in_array('tariff', $relations)) {
            $with[] = 'tariff';
        }
        if (
            array_key_exists('tariff', $relations)
            && is_array($relations['tariff'])
            && !empty($relations['tariff'])
        ) {
            $tariffRelations = $relations['tariff'];
            $with['tariff'] = function ($query) use ($tariffRelations) {
                $query->with($tariffRelations);
            };
        }
        return $with;
    }
    public function getProjects(int $userId, ...$relations): array|\Illuminate\Database\Eloquent\Collection
    {
        $formatRelations = $this->formatProjectRelations($relations);
        $rootQuery = Project::query();
        if (!empty($formatRelations)) {
            $rootQuery->with($formatRelations);
        }
        $rootQuery->where('user_id', $userId);
        return $rootQuery->get();
    }

    public function getProjectById(int $projectId, int $userId)
    {
        $rootQuery = Project::query();
        $rootQuery->where('user_id', $userId);
        return $rootQuery->find($projectId);
    }
    public function create($data): Project
    {

        $project = new Project($data);
        $project->save();
        return $project;
    }

    public function updateProject(int $projectId, int $userId, array $data)
    {
        $project = $this->getProjectById($projectId, $userId);
        if (!$project) {
            throw new \Exception(__('errors.project.update'));
        }
        if (array_key_exists('title', $data)) {
            $project->title = $data['title'];
        }
        if (array_key_exists('tariff', $data)) {
            $project = $this->updateProjectTariff($project, $data['tariff']);
        }
        $project->save();
        return $project;
    }

    private function updateProjectTariff(Project $project, TariffProject | null $tariff): Project
    {
        if ($tariff) {
            $project->tariff()->associate($tariff);
            return $project;
        }
        $project->tariff()->dissociate();
        return $project;
    }

}
