<?php

namespace App\Services;

use App\Models\Project\Project;

class ProjectService
{
    public function getProjects(int $userId): array|\Illuminate\Database\Eloquent\Collection
    {
        $rootQuery = Project::query();
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
        if (array_key_exists('tariff', $data) && $data['tariff']) {
            $project->tariff()->associate($data['tariff']);
        }
        $project->save();
        return $project;
    }
}
