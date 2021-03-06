<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use GeneratesUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description',
        'priority', 'user_id',
        'project_id', 'scheduled_date',
        'remember_date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function uuidColumn(): string
    {
        return 'id';
    }
}
