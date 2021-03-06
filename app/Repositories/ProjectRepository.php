<?php


namespace App\Repositories;


use App\Models\Professional;
use App\Models\Project;
use App\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectRepository extends BaseRepository
{

    public function create(array $data): Project
    {
        $data = $this->convertToSnakeCase($data);
        $project = new Project($data);
        $project->save();

        return $project;
    }

    public function addProjectImages(Project $project, Professional $professional, array $images): Project
    {
        $images = array_map(
            function ($image) use ($professional, $project) {
                return [
                    'path' => $image,
                    'title' => $project->title,
                    'description' => $project->description,
                    'professional_id' => $professional->id
                ];
            },
            $images
        );

        $project->images()->createMany($images);

        // update images slug
        foreach ($project->images as $image) {
            $slug = Str::arSlug($project->title) . '-' . $image->id;
            $image->update(['slug' => $slug]);
        }

        return $project;
    }

    public function getLatestProjects(int $count): Collection
    {
        $query = (new Project())->newQuery();

        return $query->orderBy('id', 'desc')
            ->take($count)
            ->get();
    }

    public function getById(int $id): ?Project
    {
        return Project::find($id);
    }

    public function getBySlug(string $slug): ?Project
    {
        return Project::where('slug', $slug)->first();
    }

    public function update(Project $project, array $data): void
    {
        $data = $this->convertToSnakeCase($data);

        Project::where('id', $project->id)->update(
            [
                'title' => $data['title'],
                'description' => $data['description'],
            ]
        );
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function getProjectsHasTag(?string $tag): Collection
    {
        return (new Project())->newQuery()
            ->whereHas(
                'tags',
                fn($query) => $query->where('name', 'like', '%' . $tag . '%')
            )
            ->get();
    }
}
