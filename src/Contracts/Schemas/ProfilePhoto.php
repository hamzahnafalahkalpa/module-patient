<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\ProfilePhotoData;

interface ProfilePhoto extends DataManagement
{
    public function prepareShowProfilePhoto(? Model $model = null, ?array $attributes = null): mixed;
    public function showProfilePhoto(? Model $model = null): mixed;
    public function prepareStoreProfilePhoto(ProfilePhotoData $profile_photo_dto): mixed;
    public function storeProfilePhoto(? ProfilePhotoData $profile_photo_dto = null): array;
}