<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_LECTURER = 'lecturer';
    public const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classroomsTeaching(): HasMany
    {
        return $this->hasMany(Classroom::class, 'lecturer_id');
    }

    public function classroomEnrollments(): HasMany
    {
        return $this->hasMany(ClassroomEnrollment::class, 'student_id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isLecturer(): bool
    {
        return $this->hasRole(self::ROLE_LECTURER);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    public function hasRole(array|string $roles): bool
    {
        $roleList = is_array($roles) ? $roles : array_filter(explode(',', (string) $roles));
        $resolvedRole = $this->role ?: ($this->is_admin ? self::ROLE_ADMIN : self::ROLE_STUDENT);

        if ($this->is_admin) {
            $resolvedRole = self::ROLE_ADMIN;
        }

        return in_array($resolvedRole, $roleList, true);
    }

    public function landingRouteName(): string
    {
        if ($this->isAdmin()) {
            return 'admin.feedback.index';
        }

        if ($this->isLecturer()) {
            return 'dashboard';
        }

        return 'feedback.create';
    }

    public function landingUrl(): string
    {
        return route($this->landingRouteName(), absolute: false);
    }
}
