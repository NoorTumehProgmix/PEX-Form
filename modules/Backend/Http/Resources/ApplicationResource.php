<?php


namespace Juzaweb\Backend\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
        'email_sent',
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'title' => $this->title,
            'dob' => $this->dob,
            'city' => $this->city,
            'address' => $this->address,
            'diocese' => $this->diocese,
            'email' => $this->email,
            'card' => $this->card,
            'passport_number' => $this->passport_number,
            'issue_country' => $this->issue_country,
            'english' => $this->english,
            'validity' => $this->validity,
            'start_pilgrimage_date' => $this->start_pilgrimage_date,
            'end_pilgrimage_date' => $this->end_pilgrimage_date,
            'status' => $this->status,
        ];
    }
}
