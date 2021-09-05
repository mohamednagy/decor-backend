<?php


namespace App\Repositories;


use App\Models\Professional;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectRepository extends BaseRepository
{

    public function create(array $data): Project
    {
        $data = $this->convertToSnakeCase($data);

        return Project::create($data);
    }

    public function addProjectImages(Project $project, array $images): Project
    {
        $images = array_map(
            function ($image) {
                return ['path' => $image];
            },
            $images
        );

        $project->images()->createMany($images);

        return $project;
    }
}
