<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable
{
    public $timestamps = false;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "username",
        "password",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        "password",
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, "username", "username");
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Booking::class, firstKey: "username", localKey: "username");
    }

    public function rules(): array
    {
        return [
            "username" => ["required", "unique:users,username", "min:".config("constants.MIN_USERNAME_LENGTH"), "max:".config("constants.MAX_USERNAME_LENGTH")],
            "password" => ["required", "min:".config("constants.MIN_PASSWORD_LENGTH"), "max:".config("constants.MAX_PASSWORD_LENGTH")]
        ];
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make($value)
        );
    }
}
