<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\ProjectImage;
use App\Modules\Images\ProfessionalLogo;
use App\Modules\Images\ProjectThumb;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class MiniProjectImagesResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        /** @var User $user */
        $user = $this->getUser($request);
        $images = collect();
        if ($user) {
            $images = $user->favoriteProjectImages()->get();
        }

        return $this->resource->map(function (ProjectImage $projectImage) use ($user, $images) {
            return [
                'slug' => $projectImage->slug,
                'title' => $projectImage->title,
                'thumbnail' => new ProjectThumb($projectImage->path),
                'space_id' => $projectImage->space_id,
                'isFavorited' => $user && $this->isFavorited($images, $projectImage),
                'professional' => [
                    'uid' => $projectImage->professional->uid,
                    'companyName' => $projectImage->professional->company_name,
                    'logo' => new ProfessionalLogo($projectImage->professional->logo),
                ]
            ];
        });
    }

    private function isFavorited(Collection $images, ProjectImage $projectImage): bool
    {
        if ($images->isEmpty()) {
            return false;
        }

        return $images->filter(fn(ProjectImage $image) => $image->id === $projectImage->id)
            ->count() !== 0;
    }

    private function getUser(Request $request): User|null
    {
        $user = $request->user();
        if ($user) {
            return $user;
        }

        if ($request->bearerToken() === null) {
            return null;
        }

        return null;
    }
}
