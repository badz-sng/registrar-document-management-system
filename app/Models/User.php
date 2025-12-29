<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Centralized role constants — use these everywhere instead of hard-coded strings.
    // Rationale: defining roles here prevents typos across controllers, views and middleware
    // and makes it easy to change role identifiers in one place.
    // Usage examples:
    // - Validate input: ['role' => ['required', 'in:'.implode(',', User::ROLES)]]
    // - Compare: if ($user->role === User::ROLE_ENCODER) { ... }
    // - Route middleware: Route::middleware(['auth', 'role:'.User::ROLE_ENCODER])...
    // Note: existing database enum (migration) still lists the same values —
    // if you later change these constants you should also add a migration to sync the DB.
    
    // Centralized role constants — use these everywhere instead of hard-coded strings
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ENCODER = 'encoder';
    public const ROLE_PROCESSOR = 'processor';
    public const ROLE_VERIFIER = 'verifier';
    public const ROLE_RETRIEVER = 'retriever';

    /** @var array<int, string> */
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_ENCODER,
        self::ROLE_PROCESSOR,
        self::ROLE_VERIFIER,
        self::ROLE_RETRIEVER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'department'
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

     // Relationships
    public function encodedRequests()
    {
        return $this->hasMany(RequestModel::class, 'encoded_by');
    }

    public function retrievedRequests()
    {
        return $this->hasMany(RequestModel::class, 'retriever_id');
    }

    public function processedRequests()
    {
        return $this->hasMany(RequestModel::class, 'processor_id');
    }

    public function verifiedRequests()
    {
        return $this->hasMany(RequestModel::class, 'verifier_id');
    }

    public function processingLogs()
    {
        return $this->hasMany(ProcessingLog::class, 'personnel_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Login history relationship
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id');
    }

    /**
     * Latest login (hasOne latestOfMany)
     */
    public function latestLogin()
    {
        return $this->hasOne(LoginHistory::class)->latestOfMany();
    }
}
